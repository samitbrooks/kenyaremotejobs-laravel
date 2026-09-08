<?php

return [

    // Which App\Payments\PaymentGateway implementation PaymentService uses,
    // bound in AppServiceProvider. Set PAYMENT_GATEWAY=mpesa in .env once a
    // DarajaGateway class exists and is registered there — everything that
    // calls PaymentService (credits, subscriptions, employer job posting)
    // stays unchanged.
    'default' => env('PAYMENT_GATEWAY', 'mock'),

    // Placeholders for the M-Pesa Daraja app credentials a future
    // DarajaGateway will need (Safaricom's OAuth consumer key/secret,
    // Lipa Na M-Pesa shortcode + passkey, and the public callback URL it
    // posts STK push results to). Reading them from config now, even
    // unused, means the .env shape doesn't need to change when that
    // gateway is added.
    'mpesa' => [
        'consumer_key' => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'shortcode' => env('MPESA_SHORTCODE'),
        'passkey' => env('MPESA_PASSKEY'),
        'environment' => env('MPESA_ENVIRONMENT', 'sandbox'),
        'callback_url' => env('MPESA_CALLBACK_URL'),
    ],

];
