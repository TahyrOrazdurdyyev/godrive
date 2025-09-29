<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminStatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getDashboardStats()
    {
        try {
            $today = Carbon::today();
            $mainDb = DB::connection('mysql_main');
            
            $stats = [
                'today' => [
                    'total_rides' => $mainDb->table('orders')->whereDate('created_at', $today)->count(),
                    'total_users' => $mainDb->table('users')->whereDate('created_at', $today)->count(),
                    'total_drivers' => $mainDb->table('drivers')->whereDate('created_at', $today)->count(),
                ],
                'total' => [
                    'total_rides' => $mainDb->table('orders')->count(),
                    'total_users' => $mainDb->table('users')->count(),
                    'total_drivers' => $mainDb->table('drivers')->count(),
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}