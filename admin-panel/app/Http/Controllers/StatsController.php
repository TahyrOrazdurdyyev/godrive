<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Order;

class StatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getDashboardStats()
    {
        try {
            $today = now()->startOfDay();
            $todayEnd = now()->endOfDay();

            // Today stats
            $todayUsers = Customer::whereBetween('created_at', [$today, $todayEnd])->count();
            $todayDrivers = Driver::whereBetween('created_at', [$today, $todayEnd])->count();
            $todayRides = Order::whereBetween('created_at', [$today, $todayEnd])->count();

            // Total stats
            $totalUsers = Customer::count();
            $totalDrivers = Driver::count();
            $totalRides = Order::count();

            // Ride statuses today
            $todayPlaced = Order::whereBetween('created_at', [$today, $todayEnd])
                ->where('status', 'placed')->count();
            $todayActive = Order::whereBetween('created_at', [$today, $todayEnd])
                ->whereIn('status', ['accepted', 'ongoing'])->count();
            $todayCompleted = Order::whereBetween('created_at', [$today, $todayEnd])
                ->where('status', 'completed')->count();
            $todayCanceled = Order::whereBetween('created_at', [$today, $todayEnd])
                ->whereIn('status', ['canceled', 'rejected'])->count();

            // Ride statuses total
            $totalPlaced = Order::where('status', 'placed')->count();
            $totalActive = Order::whereIn('status', ['accepted', 'ongoing'])->count();
            $totalCompleted = Order::where('status', 'completed')->count();
            $totalCanceled = Order::whereIn('status', ['canceled', 'rejected'])->count();

            // Earnings today
            $todayEarnings = Order::whereBetween('created_at', [$today, $todayEnd])
                ->where('status', 'completed')
                ->sum('final_rate');
            
            // Earnings total
            $totalEarnings = Order::where('status', 'completed')->sum('final_rate');

            return response()->json([
                'success' => true,
                'data' => [
                    'today' => [
                        'total_users' => $todayUsers,
                        'total_drivers' => $todayDrivers,
                        'total_rides' => $todayRides,
                        'total_earnings' => round($todayEarnings, 2),
                        'total_commission' => 0,
                        'placed' => $todayPlaced,
                        'active' => $todayActive,
                        'completed' => $todayCompleted,
                        'canceled' => $todayCanceled
                    ],
                    'total' => [
                        'total_users' => $totalUsers,
                        'total_drivers' => $totalDrivers,
                        'total_rides' => $totalRides,
                        'total_earnings' => round($totalEarnings, 2),
                        'total_commission' => 0,
                        'placed' => $totalPlaced,
                        'active' => $totalActive,
                        'completed' => $totalCompleted,
                        'canceled' => $totalCanceled
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading stats: ' . $e->getMessage()
            ], 500);
        }
    }
}
