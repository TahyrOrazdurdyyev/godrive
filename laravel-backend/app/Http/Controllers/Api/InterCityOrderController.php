<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class InterCityOrderController extends Controller
{
    /**
     * Create intercity order
     * POST /api/intercity-orders
     */
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'intercity_service_id' => 'required|integer',
                'source_city' => 'required|string',
                'source_location_name' => 'required|string',
                'destination_city' => 'required|string',
                'destination_location_name' => 'required|string',
                'source_lat' => 'required|numeric',
                'source_lng' => 'required|numeric',
                'destination_lat' => 'required|numeric',
                'destination_lng' => 'required|numeric',
                'distance' => 'required|numeric',
                'offer_rate' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $orderId = DB::connection('mysql_main')->table('intercity_orders')->insertGetId([
                'user_id' => $request->user_id,
                'intercity_service_id' => $request->intercity_service_id,
                'zone_id' => $request->zone_id,
                'coupon_id' => $request->coupon_id,
                'freight_vehicle_id' => $request->freight_vehicle_id,
                'source_city' => $request->source_city,
                'source_location_name' => $request->source_location_name,
                'destination_city' => $request->destination_city,
                'destination_location_name' => $request->destination_location_name,
                'source_lat' => $request->source_lat,
                'source_lng' => $request->source_lng,
                'destination_lat' => $request->destination_lat,
                'destination_lng' => $request->destination_lng,
                'distance' => $request->distance,
                'distance_type' => $request->distance_type ?? 'km',
                'offer_rate' => $request->offer_rate,
                'final_rate' => $request->final_rate ?? $request->offer_rate,
                'payment_type' => $request->payment_type ?? 'cash',
                'payment_status' => 0,
                'status' => 'placed',
                'parcel_dimension' => $request->parcel_dimension,
                'parcel_weight' => $request->parcel_weight,
                'parcel_images' => $request->parcel_images ? json_encode($request->parcel_images) : null,
                'when_date' => $request->when_date,
                'when_time' => $request->when_time,
                'number_of_passenger' => $request->number_of_passenger,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $order = DB::connection('mysql_main')
                ->table('intercity_orders')
                ->where('id', $orderId)
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Intercity order created successfully',
                'order' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get intercity order by ID
     */
    public function show($id)
    {
        try {
            $order = DB::connection('mysql_main')
                ->table('intercity_orders')
                ->where('id', $id)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'order' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer intercity orders
     */
    public function getCustomerOrders(Request $request)
    {
        try {
            $userId = $request->input('user_id');

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'user_id is required'
                ], 400);
            }

            $orders = DB::connection('mysql_main')
                ->table('intercity_orders')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'orders' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update intercity order
     */
    public function update(Request $request, $id)
    {
        try {
            $order = DB::connection('mysql_main')
                ->table('intercity_orders')
                ->where('id', $id)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            $updateData = [];
            $allowedFields = [
                'driver_id', 'status', 'final_rate', 'payment_status', 'otp'
            ];

            foreach ($allowedFields as $field) {
                if ($request->has($field)) {
                    $updateData[$field] = $request->input($field);
                }
            }

            if (empty($updateData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No fields to update'
                ], 400);
            }

            $updateData['updated_at'] = now();

            DB::connection('mysql_main')
                ->table('intercity_orders')
                ->where('id', $id)
                ->update($updateData);

            $updatedOrder = DB::connection('mysql_main')
                ->table('intercity_orders')
                ->where('id', $id)
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'order' => $updatedOrder
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel intercity order
     */
    public function cancel(Request $request, $id)
    {
        try {
            DB::connection('mysql_main')
                ->table('intercity_orders')
                ->where('id', $id)
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully'
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
     */
    public function getServices()
    {
        try {
            $services = DB::connection('mysql_main')
                ->table('intercity_services')
                ->where('enable', true)
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
        /**
     * Search intercity orders for driver
     * GET /api/intercity-orders/search
     */
    public function searchForDriver(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'source_city' => 'required|string',
                'driver_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            // Get driver to check zones
            $driver = DB::connection('mysql_main')
                ->table('drivers')
                ->where('id', $request->driver_id)
                ->first();

            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }

            // Parse zone_ids (stored as JSON array)
            $zoneIds = [];
            if (!empty($driver->zone_ids)) {
                $decoded = json_decode($driver->zone_ids, true);
                $zoneIds = is_array($decoded) ? $decoded : [$driver->zone_ids];
            }

            if (empty($zoneIds)) {
                return response()->json([
                    'success' => true,
                    'orders' => []
                ]);
            }

            // Build query
            $query = DB::connection('mysql_main')
                ->table('intercity_orders')
                ->where('source_city', $request->source_city)
                ->where('status', 'ridePlaced')
                ->whereIn('zone_id', $zoneIds);

            if ($request->has('destination_city') && !empty($request->destination_city)) {
                $query->where('destination_city', $request->destination_city);
            }

            if ($request->has('when_date') && !empty($request->when_date)) {
                $query->where('when_dates', $request->when_date);
            }

            $orders = $query->orderBy('created_at', 'desc')->get();

            // Filter out orders where driver already placed bid
            $filteredOrders = [];
            foreach ($orders as $order) {
                $existingBid = DB::connection('mysql_main')
                    ->table('order_bids')
                    ->where('intercity_order_id', $order->id)
                    ->where('driver_id', $request->driver_id)
                    ->exists();

                if (!$existingBid) {
                    $filteredOrders[] = $order;
                }
            }

            return response()->json([
                'success' => true,
                'orders' => $filteredOrders
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
