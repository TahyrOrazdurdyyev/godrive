<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleTypeController extends Controller
{
    /**
     * Get all active vehicle types
     * GET /api/vehicle-types
     */
    public function index()
    {
        try {
            $vehicleTypes = DB::connection('mysql_main')->table('vehicle_types')
                ->where('enable', 1)
                ->orderBy('id', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'vehicle_types' => $vehicleTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vehicle type by ID
     * GET /api/vehicle-types/{id}
     */
    public function show($id)
    {
        try {
            $vehicleType = DB::connection('mysql_main')->table('vehicle_types')
                ->where('id', $id)
                ->where('enable', 1)
                ->first();

            if (!$vehicleType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle type not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'vehicle_type' => $vehicleType
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
