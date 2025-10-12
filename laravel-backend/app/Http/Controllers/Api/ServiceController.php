<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * Get all active services
     * GET /api/services
     */
        
    public function index(Request $request)
    {
        try {
            $query = DB::connection('mysql_main')->table('services')
                ->where('enable', 1)
                ->whereNull('deleted_at');

            // Filter by intercity type if provided
            if ($request->has('intercity')) {
                $query->where('intercity_type', $request->input('intercity'));
            }

            $services = $query->orderBy('id', 'asc')->get();

            return response()->json([
                'success' => true,
                'services' => $services
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get service by ID
     * GET /api/services/{id}
     */
    public function show($id)
    {
        try {
            $service = DB::connection('mysql_main')->table('services')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'service' => $service
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get intercity services
     * GET /api/services/intercity
     */
    public function getIntercityServices()
    {
        try {
            $services = DB::connection('mysql_main')->table('intercity_services')
                ->where('enable', 1)
                ->whereNull('deleted_at')
                ->orderBy('id', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'services' => $services
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
