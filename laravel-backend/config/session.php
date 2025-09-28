<?php

return [
    'driver' => env('SESSION_DRIVER', 'file'),
    'lifetime' => env('SESSION_LIFETIME', 120),
    'files' => storage_path('framework/sessions'),
    'cookie' => 'laravel_session',
    'path' => '/',
    'secure' => false,
    'http_only' => true,
];
