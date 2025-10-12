<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ZoneController extends Controller
{
    /**
     * Get all active zones
     * GET /api/zones
     */
    public function index()
    {
        try {
            $zones = DB::connection('mysql_main')->table('zones')
                ->where('enable', 1)
                ->orderBy('id', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'zones' => $zones
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get zone by ID
     * GET /api/zones/{id}
     */
    public function show($id)
    {
        try {
            $zone = DB::connection('mysql_main')->table('zones')
                ->where('id', $id)
                ->where('enable', 1)
                ->first();

            if (!$zone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Zone not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'zone' => $zone
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
