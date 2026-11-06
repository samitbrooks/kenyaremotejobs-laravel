<?php

namespace App\Providers;

use App\Models\User;
use App\Payments\DarajaGateway;
use App\Payments\MockGateway;
use App\Payments\PaymentGateway;
use App\Support\NoInlineMarkdown;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Every call site depends on PaymentGateway, not a concrete class —
        // this is the only place that knows which implementation is live.
        $this->app->bind(PaymentGateway::class, function () {
            $gateway = config('payments.default');

            if ($this->app->isProduction() && ($gateway === 'mock' || ! $gateway)) {
                throw new \RuntimeException('Mock payment gateway is strictly forbidden in production. Configure PAYMENT_GATEWAY=mpesa in .env');
            }

            return match ($gateway) {
                'mpesa' => new DarajaGateway,
                default => new MockGateway,
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(MessageSending::class, function (MessageSending $event): ?bool {
            $recipients = array_merge($event->message->getTo(), $event->message->getCc(), $event->message->getBcc());
            $emails = array_map(fn ($address) => strtolower($address->getAddress()), $recipients);

            return User::query()->whereIn('email', $emails)->whereNotNull('banned_at')->exists() ? false : null;
        });

        // MailServiceProvider is a DeferrableProvider — it only registers its
        // own Markdown::class singleton the first time something actually
        // resolves it, which would silently clobber a binding set here in
        // register() regardless of provider order. extend() sidesteps that:
        // it wraps whatever eventually gets built, independent of when the
        // underlying binding was registered. See App\Support\NoInlineMarkdown
        // for why this override exists.
        $this->app->extend(Markdown::class, function ($markdown, $app) {
            $config = $app->make('config');

            return new NoInlineMarkdown($app->make('view'), [
                'theme' => $config->get('mail.markdown.theme', 'default'),
                'paths' => $config->get('mail.markdown.paths', []),
                'extensions' => $config->get('mail.markdown.extensions', []),
            ]);
        });
    }
}
