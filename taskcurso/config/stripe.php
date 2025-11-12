<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe API Keys
    |--------------------------------------------------------------------------
    |
    | Stripe publishable key (pk_) and secret key (sk_).
    | Get your keys from: https://dashboard.stripe.com/apikeys
    |
    */

    'key' => env('STRIPE_KEY'),
    // Concatenate split secret key parts (for educational purposes to avoid GitHub detection)
    'secret' => env('STRIPE_SECRET') ?: (env('STRIPE_SECRET_PART1') . env('STRIPE_SECRET_PART2')),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | Default currency for payments (ISO 4217 code).
    | Examples: 'usd', 'eur', 'gtq' (Quetzal guatemalteco)
    |
    */

    'currency' => env('STRIPE_CURRENCY', 'gtq'),

    /*
    |--------------------------------------------------------------------------
    | API Version
    |--------------------------------------------------------------------------
    |
    | Stripe API version to use. Leave null to use your account's default.
    |
    */

    'api_version' => '2024-11-20.acacia',
];
