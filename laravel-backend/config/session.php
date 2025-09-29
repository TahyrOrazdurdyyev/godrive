<?php

return [
    'driver' => env('SESSION_DRIVER', 'file'),
    'lifetime' => env('SESSION_LIFETIME', 120),
    'files' => storage_path('framework/sessions'),
    'cookie' => 'laravel_session',
    'path' => '/',
    'domain' => env('SESSION_DOMAIN', null),
    'secure' => false,
    'http_only' => true,
    'lottery' => [2, 100],
    'expire_on_close' => false,
];