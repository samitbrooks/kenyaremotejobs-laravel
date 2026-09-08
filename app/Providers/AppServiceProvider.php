<?php

namespace App\Providers;

use App\Payments\MockGateway;
use App\Payments\PaymentGateway;
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
        //
    }
}
