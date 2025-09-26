<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\WalletController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\ChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes (no authentication required)
Route::prefix('v1')->group(function () {
    // Authentication
    Route::post('/customer/login', [AuthController::class, 'customerLogin']);
    Route::post('/driver/login', [AuthController::class, 'driverLogin']);
    
    // Public services
    Route::get('/services', [ServiceController::class, 'getServices']);
    Route::get('/services/city', [ServiceController::class, 'getCityServices']);
    Route::get('/services/intercity', [ServiceController::class, 'getIntercityServices']);
    Route::get('/services/{id}', [ServiceController::class, 'getService']);
    Route::get('/zones', [ServiceController::class, 'getZones']);
    Route::post('/zones/find', [ServiceController::class, 'findZone']);
    Route::get('/banners', [ServiceController::class, 'getBanners']);
    
    // Fare calculation (can be public or authenticated)
    Route::post('/calculate-fare', [ServiceController::class, 'calculateFare']);
});

// Customer protected routes
Route::prefix('v1/customer')->middleware(['auth:sanctum', 'customer'])->group(function () {
    // Profile
    Route::get('/profile', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateCustomerProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Orders
    Route::post('/orders', [OrderController::class, 'createOrder']);
    Route::get('/orders', [OrderController::class, 'getUserOrders']);
    Route::get('/orders/{id}', [OrderController::class, 'getOrderDetails']);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateOrderStatus']);
    
    // Wallet
    Route::get('/wallet/balance', [WalletController::class, 'getBalance']);
    Route::get('/wallet/transactions', [WalletController::class, 'getTransactions']);
    Route::post('/wallet/add-money', [WalletController::class, 'addMoney']);
    Route::get('/wallet/transactions/{id}', [WalletController::class, 'getTransactionDetails']);
    
    // Coupons
    Route::get('/coupons', [ServiceController::class, 'getAvailableCoupons']);
    Route::post('/coupons/validate', [ServiceController::class, 'validateCoupon']);
    
    // Reviews
    Route::post('/orders/{id}/review', [ReviewController::class, 'createReview']);
    Route::get('/reviews', [ReviewController::class, 'getUserReviews']);
    
    // Chat
    Route::get('/orders/{id}/messages', [ChatController::class, 'getOrderMessages']);
    Route::post('/orders/{id}/messages', [ChatController::class, 'sendMessage']);
});

// Driver protected routes
Route::prefix('v1/driver')->middleware(['auth:sanctum', 'driver'])->group(function () {
    // Profile
    Route::get('/profile', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateDriverProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Location and status
    Route::post('/location', [AuthController::class, 'updateDriverLocation']);
    Route::post('/status', [AuthController::class, 'updateDriverStatus']);
    
    // Orders
    Route::get('/orders/nearby', [OrderController::class, 'getNearbyOrders']);
    Route::post('/orders/{id}/accept', [OrderController::class, 'acceptOrder']);
    Route::post('/orders/{id}/reject', [OrderController::class, 'rejectOrder']);
    Route::get('/orders', [OrderController::class, 'getDriverOrders']);
    Route::get('/orders/{id}', [OrderController::class, 'getOrderDetails']);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateOrderStatus']);
    
    // Wallet
    Route::get('/wallet/balance', [WalletController::class, 'getBalance']);
    Route::get('/wallet/transactions', [WalletController::class, 'getTransactions']);
    Route::post('/wallet/withdraw', [WalletController::class, 'withdrawMoney']);
    Route::get('/wallet/transactions/{id}', [WalletController::class, 'getTransactionDetails']);
    
    // Reviews
    Route::get('/reviews', [ReviewController::class, 'getDriverReviews']);
    
    // Chat
    Route::get('/orders/{id}/messages', [ChatController::class, 'getOrderMessages']);
    Route::post('/orders/{id}/messages', [ChatController::class, 'sendMessage']);
});

// Shared routes (both customer and driver)
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Fallback route
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found'
    ], 404);
});

