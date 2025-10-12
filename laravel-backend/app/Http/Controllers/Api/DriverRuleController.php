<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverRuleController extends Controller
{
    /**
     * Get all active driver rules
     * GET /api/driver-rules
     */
    public function index()
    {
        try {
            $driverRules = DB::connection('mysql_main')->table('driver_rules')
                ->where('enable', 1)
                ->orderBy('id', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'driver_rules' => $driverRules
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get driver rule by ID
     * GET /api/driver-rules/{id}
     */
    public function show($id)
    {
        try {
            $driverRule = DB::connection('mysql_main')->table('driver_rules')
                ->where('id', $id)
                ->where('enable', 1)
                ->first();

            if (!$driverRule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver rule not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'driver_rule' => $driverRule
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
