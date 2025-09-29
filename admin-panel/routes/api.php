<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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