<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Events\OrderCreated;
use App\Events\OrderUpdated;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Create a new order
     * POST /api/orders
     */
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'service_id' => 'required|integer',
                'source_lat' => 'required|numeric',
                'source_lng' => 'required|numeric',
                'source_location_name' => 'required|string',
                'destination_lat' => 'required|numeric',
                'destination_lng' => 'required|numeric',
                'destination_location_name' => 'required|string',
                'distance' => 'required|numeric',
                'duration' => 'nullable|string',
                'offer_rate' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $orderId = DB::connection('mysql_main')->table('orders')->insertGetId([
                'user_id' => $request->user_id,
                'service_id' => $request->service_id,
                'source_lat' => $request->source_lat,
                'source_lng' => $request->source_lng,
                'source_location_name' => $request->source_location_name,
                'destination_lat' => $request->destination_lat,
                'destination_lng' => $request->destination_lng,
                'destination_location_name' => $request->destination_location_name,
                'distance' => $request->distance,
                'duration' => $request->duration,
                'offer_rate' => $request->offer_rate,
                'final_rate' => $request->offer_rate,
                'status' => 'pending',
                'payment_type' => $request->payment_type ?? 'cash',
                'payment_status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Get created order with relations
            $order = Order::with(['user', 'service', 'zone'])->find($orderId);
            Log::info('🔥 About to broadcast OrderCreated event', ['order_id' => $order->id]);

            // Dispatch WebSocket event
            try { event(new OrderCreated($order)); } catch (\Exception $e) { Log::error("❌ OrderCreated broadcast failed: " . $e->getMessage()); }
            
            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
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
     * Get order by ID
     * GET /api/orders/{id}
     */
    public function show($id)
    {
        try {
            $order = Order::with(['user', 'driver', 'service', 'zone'])->find($id);

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
     * Get customer orders
     * GET /api/orders/customer?user_id={id}
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

            $orders = Order::with(['driver', 'service', 'zone'])
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
     * Get driver orders
     * GET /api/orders/driver?driver_id={id}
     */
    public function getDriverOrders(Request $request)
    {
        try {
            $driverId = $request->input('driver_id');

            if (!$driverId) {
                return response()->json([
                    'success' => false,
                    'message' => 'driver_id is required'
                ], 400);
            }

            $orders = Order::with(['user', 'service', 'zone'])
                ->where('driver_id', $driverId)
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
     * Get nearby pending orders for drivers
     * GET /api/orders/nearby?latitude={lat}&longitude={lng}&radius={km}
     */
    public function getNearbyOrders(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'radius' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $radius = $request->input('radius', 10); // Default 10km

            // Calculate nearby orders using Haversine formula
            $orders = DB::connection('mysql_main')->select("
                SELECT *,
                    (6371 * acos(
                        cos(radians(?)) * cos(radians(source_lat)) *
                        cos(radians(source_lng) - radians(?)) +
                        sin(radians(?)) * sin(radians(source_lat))
                    )) AS distance
                FROM orders
                WHERE status = 'pending'
                    AND driver_id IS NULL
                    AND deleted_at IS NULL
                HAVING distance < ?
                ORDER BY distance ASC
            ", [$latitude, $longitude, $latitude, $radius]);

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
     * Accept order (driver)
     * POST /api/orders/{id}/accept
     */
    public function acceptOrder(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'driver_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $order = Order::where('id', $id)
                ->where('status', 'pending')
                ->whereNull('driver_id')
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not available'
                ], 404);
            }

            $order->driver_id = $request->driver_id;
            $order->status = 'accepted';
            $order->save();

            // Dispatch WebSocket event
            $order->load(['user', 'driver', 'service', 'zone']);
            event(new OrderUpdated($order));

            return response()->json([
                'success' => true,
                'message' => 'Order accepted successfully',
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
     * Update order
     * PUT /api/orders/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Build update data from request
            $allowedFields = [
                'driver_id', 'zone_id', 'coupon_id',
                'source_location_name', 'destination_location_name',
                'source_lat', 'source_lng', 'destination_lat', 'destination_lng',
                'distance', 'duration', 'offer_rate', 'final_rate',
                'payment_type', 'payment_status', 'status', 'otp',
                'is_ac_selected', 'ac_non_ac_charges', 'total_holding_charges',
                'ride_hold_time_minutes', 'accept_hold_time',
                'accepted_driver_ids', 'rejected_driver_ids',
                'position_data', 'tax_data', 'coupon_data', 'someone_else_data',
                'admin_commission_data', 'vehicle_information_data'
            ];

            $hasUpdates = false;
            foreach ($allowedFields as $field) {
                if ($request->has($field)) {
                    $value = $request->input($field);
                    // Handle JSON fields
                    if (in_array($field, ['accepted_driver_ids', 'rejected_driver_ids', 'position_data', 'tax_data', 'coupon_data', 'someone_else_data', 'admin_commission_data', 'vehicle_information_data'])) {
                        $order->$field = is_string($value) ? $value : json_encode($value);
                    } else {
                        $order->$field = $value;
                    }
                    $hasUpdates = true;
                }
            }

            if (!$hasUpdates) {
                return response()->json([
                    'success' => false,
                    'message' => 'No fields to update'
                ], 400);
            }

            $order->save();

            // Dispatch WebSocket event
            $order->load(['user', 'driver', 'service', 'zone']);
            event(new OrderUpdated($order));

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'order' => $order
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
     * Update order status
     * PUT /api/orders/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|string|in:pending,accepted,arrived,started,completed,cancelled',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            $order->status = $request->status;
            $order->save();

            // Dispatch WebSocket event
            $order->load(['user', 'driver', 'service', 'zone']);
            event(new OrderUpdated($order));

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
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
     * Cancel order
     * POST /api/orders/{id}/cancel
     */
    public function cancelOrder(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cancelled_by' => 'required|string|in:customer,driver',
                'cancellation_reason' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            $order->status = 'cancelled';
            $order->save();

            // Dispatch WebSocket event
            $order->load(['user', 'driver', 'service', 'zone']);
            event(new OrderUpdated($order));

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully',
                'order' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
