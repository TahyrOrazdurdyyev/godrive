<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'GoRide Laravel API is running!',
        'version' => '1.0',
        'timestamp' => now()
    ]);
});

Route::get('/health', function () {
    return response()->json(['status' => 'healthy']);
});
