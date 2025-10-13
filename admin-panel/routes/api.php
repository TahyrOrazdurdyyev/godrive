<?php
use App\Http\Controllers\API\VehicleTypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\AdminStatsController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Admin Statistics API Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/stats/dashboard', [AdminStatsController::class, 'getDashboardStats']);
    Route::get('/admin/stats/earnings', [AdminStatsController::class, 'getEarningsStats']);
    Route::get('/admin/stats/recent-rides', [AdminStatsController::class, 'getRecentRides']);
    Route::get('/admin/stats/top-drivers', [AdminStatsController::class, 'getTopDrivers']);
});
// Vehicle Type routes (Admin)
Route::post('/v1/vehicle-types', [App\Http\Controllers\API\VehicleTypeController::class, 'store']);
Route::get('/v1/vehicle-types', [App\Http\Controllers\API\VehicleTypeController::class, 'index']);
Route::post('/v1/vehicle-types/{id}/toggle', [App\Http\Controllers\API\VehicleTypeController::class, 'toggle']);
Route::delete('/v1/vehicle-types/{id}', [App\Http\Controllers\API\VehicleTypeController::class, 'destroy']);
// Driver status check for login
Route::get('/driver/check-status', function (Request $request) {
    $uid = $request->input('uid');

    if (!$uid) {
        return response()->json(['success' => false, 'message' => 'UID is required'], 400);
    }

    $driver = DB::connection('mysql_main')->table('drivers')
        ->where('uid', $uid)
        ->first();

    if (!$driver) {
        return response()->json(['success' => false, 'message' => 'Driver not found'], 404);
    }

    return response()->json([
        'success' => true,
        'driver' => [
            'id' => $driver->id,
            'uid' => $driver->uid,
            'full_name' => $driver->full_name,
            'email' => $driver->email,
            'phone' => $driver->phone,
            'is_active' => $driver->is_active
        ]
    ]);
});
// TEST endpoint - добавь эти строки ниже
Route::get('/test', function () {
    return response()->json(['success' => true, 'message' => 'Test endpoint works!']);
});
// Driver API endpoints - with web middleware (session auth)
Route::middleware('web')->group(function () {
    Route::get('/drivers/{id}/data', [App\Http\Controllers\DriverController::class, 'getDriverData']);
    Route::get('/drivers/{id}/orders', [App\Http\Controllers\DriverController::class, 'getDriverOrders']);
    Route::get('/drivers/{id}/reviews', [App\Http\Controllers\DriverController::class, 'getDriverReviews']);
    Route::get('/drivers/{id}/subscription-history', [App\Http\Controllers\DriverController::class, 'getDriverSubscriptionHistory']);
    Route::get('/drivers/{id}/wallet-transactions', [App\Http\Controllers\DriverController::class, 'getDriverWalletTransactions']);
    Route::get('/drivers/{id}/withdrawals', [App\Http\Controllers\DriverController::class, 'getDriverWithdrawals']);
    Route::post('/drivers/{id}/add-wallet', [App\Http\Controllers\DriverController::class, 'addWallet']);
    Route::post('/drivers/{id}/assign-subscription', [App\Http\Controllers\DriverController::class, 'assignSubscription']);
    
    // Settings API endpoint
    Route::get('/settings', function() {
        $settings = App\Models\Setting::all()->pluck('setting_value', 'setting_key');
        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    });
});
