<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SosController extends Controller
{
    /**
     * Get SOS by order ID
     * GET /api/sos/order/{orderId}
     */
    public function getByOrder(Request $request, $orderId)
    {
        try {
            $orderType = $request->query('order_type', 'city');
            
            $query = DB::connection('mysql_main')
                ->table('sos')
                ->where('order_type', $orderType);
            
            if ($orderType === 'city') {
                $query->where('order_id', $orderId);
            } else {
                $query->where('intercity_order_id', $orderId);
            }
            
            $sos = $query->orderBy('created_at', 'desc')->first();

            if (!$sos) {
                return response()->json([
                    'success' => false,
                    'message' => 'SOS not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'sos' => $sos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get SOS',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create SOS
     * POST /api/sos
     */
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'order_type' => 'required|in:city,intercity',
                'status' => 'nullable|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $data = [
                'user_id' => $request->user_id,
                'order_type' => $request->order_type,
                'status' => $request->status ?? 'initiated',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'notes' => $request->notes,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($request->order_type === 'city') {
                $data['order_id'] = $request->order_id;
            } else {
                $data['intercity_order_id'] = $request->intercity_order_id;
            }

            if ($request->driver_id) {
                $data['driver_id'] = $request->driver_id;
            }

            $sosId = DB::connection('mysql_main')
                ->table('sos')
                ->insertGetId($data);

            return response()->json([
                'success' => true,
                'message' => 'SOS created successfully',
                'sos_id' => $sosId
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create SOS',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update SOS status
     * PUT /api/sos/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|string',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $data = [
                'status' => $request->status,
                'updated_at' => now(),
            ];

            if ($request->has('notes')) {
                $data['notes'] = $request->notes;
            }

            DB::connection('mysql_main')
                ->table('sos')
                ->where('id', $id)
                ->update($data);

            return response()->json([
                'success' => true,
                'message' => 'SOS updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update SOS',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's SOS history
     * GET /api/sos/user/{userId}
     */
    public function getUserSos($userId)
    {
        try {
            $sosList = DB::connection('mysql_main')
                ->table('sos')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'sos_list' => $sosList
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get SOS history',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
