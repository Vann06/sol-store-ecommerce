<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */
    
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

        // Use env-driven domains first to avoid hard-coded localhost in production.
        'allowed_origins' => array_values(array_filter([
            env('FRONTEND_URL'),               // e.g. http://solstoredev.duckdns.org
            env('APP_URL'),                    // backend base URL
            // Explicit fallbacks (keep for local dev / legacy builds)
            'http://solstoredev.duckdns.org',
            'http://solstoredev.duckdns.org:5173',
            'http://solstoredev.duckdns.org:8000',
            'http://35.226.128.220:5173',
            'http://localhost',
            'http://localhost:5173'
        ])),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
