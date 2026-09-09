<?php

namespace App\Providers;

use App\Payments\MockGateway;
use App\Payments\PaymentGateway;
use App\Support\NoInlineMarkdown;
use Illuminate\Mail\Markdown;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Swap this match for a real gateway (e.g. 'mpesa' => new
        // DarajaGateway(...)) once one exists — every call site depends on
        // PaymentGateway, not a concrete class.
        $this->app->bind(PaymentGateway::class, fn () => match (config('payments.default')) {
            default => new MockGateway,
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
