<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\WalletController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\FileUploadController;
use App\Http\Controllers\API\NegotiationController;

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
    Route::get('/languages', [ServiceController::class, 'getLanguages']);
    
    // Fare calculation (can be public or authenticated)
    Route::post('/calculate-fare', [ServiceController::class, 'calculateFare']);
});

// Customer protected routes
Route::prefix('v1/customer')->middleware(['auth:sanctum', 'customer'])->group(function () {
    // Profile
    Route::get('/profile', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateCustomerProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/upload-avatar', [FileUploadController::class, 'uploadCustomerAvatar']);
    Route::delete('/delete-avatar', [FileUploadController::class, 'deleteCustomerAvatar']);
    
    // Orders
    Route::post('/orders', [OrderController::class, 'createOrder']);
    Route::get('/orders', [OrderController::class, 'getUserOrders']);
    Route::get('/orders/{id}', [OrderController::class, 'getOrderDetails']);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateOrderStatus']);
    
    // Price negotiation
    Route::post('/orders/{id}/accept-counter', [NegotiationController::class, 'acceptCounterOffer']);
    Route::post('/orders/{id}/reject-counter', [NegotiationController::class, 'rejectCounterOffer']);
    
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
    Route::post('/upload-avatar', [FileUploadController::class, 'uploadDriverAvatar']);
    Route::delete('/delete-avatar', [FileUploadController::class, 'deleteDriverAvatar']);
    
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
    
    // Price negotiation
    Route::post('/orders/{id}/accept-offer', [NegotiationController::class, 'driverAcceptOffer']);
    Route::post('/orders/{id}/counter-offer', [NegotiationController::class, 'driverCounterOffer']);
    
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

// ============================================
// PUBLIC API ROUTES (NO AUTH REQUIRED)
// ============================================

// Settings API
Route::get('/settings', [App\Http\Controllers\Api\SettingsController::class, 'getSettings']);

// Driver API Routes
Route::post('/driver/register', [App\Http\Controllers\Api\DriverController::class, 'register']);
Route::get('/driver/profile', [App\Http\Controllers\Api\DriverController::class, 'getProfile']);
Route::put('/driver/profile', [App\Http\Controllers\Api\DriverController::class, 'updateProfile']);
Route::post('/driver/update-location', [App\Http\Controllers\Api\DriverController::class, 'updateLocation']);
Route::post('/driver/update-status', [App\Http\Controllers\Api\DriverController::class, 'updateStatus']);
Route::post('/driver/update-fcm', [App\Http\Controllers\Api\DriverController::class, 'updateFcmToken']);

// Driver Documents routes
Route::post('/driver/documents/upload', [App\Http\Controllers\Api\DriverDocumentController::class, 'upload']);
Route::get('/driver/documents', [App\Http\Controllers\Api\DriverDocumentController::class, 'getDocuments']);
Route::delete('/driver/documents/{id}', [App\Http\Controllers\Api\DriverDocumentController::class, 'delete']);

// User (Customer) API Routes
Route::post('/user/register', [App\Http\Controllers\Api\UserController::class, 'register']);
Route::get('/user/profile', [App\Http\Controllers\Api\UserController::class, 'getProfile']);
Route::put('/user/profile', [App\Http\Controllers\Api\UserController::class, 'updateProfile']);
Route::post('/user/update-fcm', [App\Http\Controllers\Api\UserController::class, 'updateFcmToken']);

// Customer API Routes (legacy)
Route::post('/customer/register', [App\Http\Controllers\Api\CustomerController::class, 'register']);
Route::get('/customer/profile', [App\Http\Controllers\Api\CustomerController::class, 'getProfile']);
Route::put('/customer/profile', [App\Http\Controllers\Api\CustomerController::class, 'updateProfile']);
Route::post('/customer/update-fcm', [App\Http\Controllers\Api\CustomerController::class, 'updateFcmToken']);

// File Upload API Routes
Route::post('/driver/upload-avatar', [App\Http\Controllers\Api\FileUploadController::class, 'uploadDriverAvatar']);
Route::post('/driver/upload-document', [App\Http\Controllers\Api\FileUploadController::class, 'uploadDriverDocument']);
Route::get('/driver/documents', [App\Http\Controllers\Api\FileUploadController::class, 'getDriverDocuments']);
Route::delete('/driver/document/{id}', [App\Http\Controllers\Api\FileUploadController::class, 'deleteDriverDocument']);
Route::post('/customer/upload-avatar', [App\Http\Controllers\Api\FileUploadController::class, 'uploadCustomerAvatar']);

// Service API Routes
Route::get('/services', [App\Http\Controllers\Api\ServiceController::class, 'index']);
Route::get('/services/{id}', [App\Http\Controllers\Api\ServiceController::class, 'show']);
Route::get('/services/intercity', [App\Http\Controllers\Api\ServiceController::class, 'getIntercityServices']);

// Order API Routes (nearby ПЕРЕД {id} для правильного роутинга!)
Route::post('/orders', [App\Http\Controllers\Api\OrderController::class, 'create']);
Route::get('/orders/nearby', [App\Http\Controllers\Api\OrderController::class, 'getNearbyOrders']);
Route::get('/orders/customer', [App\Http\Controllers\Api\OrderController::class, 'getCustomerOrders']);
Route::get('/orders/driver', [App\Http\Controllers\Api\OrderController::class, 'getDriverOrders']);
Route::put('/orders/{id}', [App\Http\Controllers\Api\OrderController::class, 'update']);
Route::get('/orders/{id}', [App\Http\Controllers\Api\OrderController::class, 'show']);
Route::post('/orders/{id}/accept', [App\Http\Controllers\Api\OrderController::class, 'acceptOrder']);
Route::put('/orders/{id}/status', [App\Http\Controllers\Api\OrderController::class, 'updateStatus']);
Route::post('/orders/{id}/cancel', [App\Http\Controllers\Api\OrderController::class, 'cancelOrder']);

// Wallet API Routes
Route::get('/wallet/driver', [App\Http\Controllers\Api\WalletController::class, 'getDriverBalance']);
Route::get('/wallet/customer', [App\Http\Controllers\Api\WalletController::class, 'getCustomerBalance']);
Route::get('/wallet/transactions', [App\Http\Controllers\Api\WalletController::class, 'getTransactions']);
Route::post('/wallet/add', [App\Http\Controllers\Api\WalletController::class, 'addMoney']);
Route::post('/wallet/withdraw', [App\Http\Controllers\Api\WalletController::class, 'withdrawMoney']);

// Conversation API Routes
Route::get('/conversations', [App\Http\Controllers\Api\ConversationController::class, 'getMessages']);
Route::post('/conversations', [App\Http\Controllers\Api\ConversationController::class, 'sendMessage']);
Route::put('/conversations/read', [App\Http\Controllers\Api\ConversationController::class, 'markAsRead']);
Route::get('/conversations/unread', [App\Http\Controllers\Api\ConversationController::class, 'getUnreadCount']);

// Zone API Routes
Route::get('/zones', [App\Http\Controllers\Api\ZoneController::class, 'index']);
Route::get('/zones/{id}', [App\Http\Controllers\Api\ZoneController::class, 'show']);

// Vehicle Type API Routes
Route::get('/vehicle-types', [App\Http\Controllers\Api\VehicleTypeController::class, 'index']);
Route::get('/vehicle-types/{id}', [App\Http\Controllers\Api\VehicleTypeController::class, 'show']);

// Driver Rules API Routes
Route::get('/driver-rules', [App\Http\Controllers\Api\DriverRuleController::class, 'index']);
Route::get('/driver-rules/{id}', [App\Http\Controllers\Api\DriverRuleController::class, 'show']);

// Language API Routes
Route::get('/languages', [App\Http\Controllers\Api\LanguageController::class, 'index']);
Route::get('/languages/{code}', [App\Http\Controllers\Api\LanguageController::class, 'show']);

// Review API Routes
Route::get('/reviews/order', [App\Http\Controllers\Api\ReviewController::class, 'getReviewByOrder']);
Route::get('/reviews/driver', [App\Http\Controllers\Api\ReviewController::class, 'getDriverReviews']);
Route::post('/reviews', [App\Http\Controllers\Api\ReviewController::class, 'createReview']);

// InterCity Order routes
Route::post('/intercity-orders', [App\Http\Controllers\Api\InterCityOrderController::class, 'create']);
Route::get('/intercity-orders/customer', [App\Http\Controllers\Api\InterCityOrderController::class, 'getCustomerOrders']);
Route::get('/intercity-orders/search', [App\Http\Controllers\Api\InterCityOrderController::class, 'searchForDriver']);
Route::get('/intercity-orders/{id}', [App\Http\Controllers\Api\InterCityOrderController::class, 'show']);
Route::put('/intercity-orders/{id}', [App\Http\Controllers\Api\InterCityOrderController::class, 'update']);
Route::post('/intercity-orders/{id}/cancel', [App\Http\Controllers\Api\InterCityOrderController::class, 'cancel']);
Route::get('/intercity-services', [App\Http\Controllers\Api\InterCityOrderController::class, 'getServices']);

// Coupon routes
Route::get('/coupons', [App\Http\Controllers\Api\CouponController::class, 'getAll']);
Route::get('/coupons/validate', [App\Http\Controllers\Api\CouponController::class, 'validate']);
Route::post('/coupons/use', [App\Http\Controllers\Api\CouponController::class, 'use']);

// Order Bids routes
Route::get('/orders/{orderId}/bids', [App\Http\Controllers\Api\OrderBidController::class, 'getOrderBids']);
Route::get('/orders/{orderId}/bids/accepted', [App\Http\Controllers\Api\OrderBidController::class, 'getAcceptedBids']);
Route::get('/orders/{orderId}/bids/{driverId}', [App\Http\Controllers\Api\OrderBidController::class, 'getBid']);
Route::post('/orders/{orderId}/bids', [App\Http\Controllers\Api\OrderBidController::class, 'createOrUpdateBid']);

// FAQ routes
Route::get('/faqs', [App\Http\Controllers\Api\FaqController::class, 'getAll']);
Route::get('/faqs/{id}', [App\Http\Controllers\Api\FaqController::class, 'show']);

// SOS routes
Route::get('/sos/order/{orderId}', [App\Http\Controllers\Api\SosController::class, 'getByOrder']);
Route::get('/sos/user/{userId}', [App\Http\Controllers\Api\SosController::class, 'getUserSos']);
Route::post('/sos', [App\Http\Controllers\Api\SosController::class, 'create']);
Route::put('/sos/{id}', [App\Http\Controllers\Api\SosController::class, 'update']);

// Driver Wallet routes
Route::get('/wallet/driver', [App\Http\Controllers\Api\DriverWalletController::class, 'getBalance']);
Route::post('/wallet/driver/transaction', [App\Http\Controllers\Api\DriverWalletController::class, 'addTransaction']);
Route::get('/wallet/driver/transactions', [App\Http\Controllers\Api\DriverWalletController::class, 'getTransactions']);

// Subscription routes
Route::get('/subscriptions/plans', [App\Http\Controllers\Api\SubscriptionController::class, 'getAllPlans']);
Route::get('/subscriptions/plans/{id}', [App\Http\Controllers\Api\SubscriptionController::class, 'getPlanById']);
Route::post('/subscriptions/history', [App\Http\Controllers\Api\SubscriptionController::class, 'createHistory']);
Route::get('/subscriptions/history/{userId}', [App\Http\Controllers\Api\SubscriptionController::class, 'getHistory']);

// Fallback route
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found'
    ], 404);
});
