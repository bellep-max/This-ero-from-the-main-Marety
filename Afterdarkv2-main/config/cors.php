<?php

return [

    'paths' => ['api/*', 'api/v1/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:5000'),
        env('APP_URL', 'http://localhost'),
    ],

    'allowed_origins_patterns' => [
        '#https?://.*\.replit\.dev$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
