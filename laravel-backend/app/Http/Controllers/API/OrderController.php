<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Driver;
use App\Models\Service;
use App\Models\Zone;
use App\Models\Coupon;
use App\Models\WalletTransaction;
use App\Events\OrderCreated;
use App\Events\OrderUpdated;
use App\Events\DriverLocationUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    // Create new order (Customer)
    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'source_location_name' => 'required|string',
            'destination_location_name' => 'required|string',
            'source_lat' => 'required|numeric|between:-90,90',
            'source_lng' => 'required|numeric|between:-180,180',
            'destination_lat' => 'required|numeric|between:-90,90',
            'destination_lng' => 'required|numeric|between:-180,180',
            'distance' => 'required|numeric|min:0',
            'distance_type' => 'required|in:km,miles',
            'duration' => 'nullable|string',
            'payment_type' => 'required|in:cash,wallet,stripe,razorpay,paypal',
            'is_ac_selected' => 'boolean',
            'someone_else_data' => 'nullable|array',
            'coupon_code' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = $request->user();
            $service = Service::find($request->service_id);

            // Find zone
            $zone = Zone::enabled()->get()->first(function ($zone) use ($request) {
                return $zone->isPointInZone($request->source_lat, $request->source_lng);
            });

            // Calculate fare
            $duration = $this->parseDuration($request->duration ?? '0 mins');
            $isNightTime = $service->isNightTime();
            $fare = $service->calculateFare(
                $request->distance,
                $duration,
                $request->is_ac_selected ?? false,
                $isNightTime
            );

            // Apply coupon if provided
            $coupon = null;
            $couponData = null;
            if ($request->coupon_code) {
                $coupon = Coupon::where('code', $request->coupon_code)->valid()->first();
                if ($coupon && $coupon->canBeUsedBy($user->id, $fare)) {
                    $couponData = [
                        'code' => $coupon->code,
                        'title' => $coupon->title,
                        'discount' => $coupon->discount,
                        'type' => $coupon->discount_type,
                        'calculated_discount' => $coupon->calculateDiscount($fare)
                    ];
                    $fare -= $couponData['calculated_discount'];
                }
            }

            // Generate OTP
            $otp = str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'service_id' => $request->service_id,
                'zone_id' => $zone?->id,
                'coupon_id' => $coupon?->id,
                'source_location_name' => $request->source_location_name,
                'destination_location_name' => $request->destination_location_name,
                'source_lat' => $request->source_lat,
                'source_lng' => $request->source_lng,
                'destination_lat' => $request->destination_lat,
                'destination_lng' => $request->destination_lng,
                'distance' => $request->distance,
                'distance_type' => $request->distance_type,
                'duration' => $request->duration,
                'offer_rate' => $fare,
                'final_rate' => $fare,
                'payment_type' => $request->payment_type,
                'payment_status' => false,
                'status' => 'placed',
                'otp' => $otp,
                'is_ac_selected' => $request->is_ac_selected ?? false,
                'ac_non_ac_charges' => $request->is_ac_selected ? $service->ac_charge : $service->non_ac_charge,
                'someone_else_data' => $request->someone_else_data,
                'coupon_data' => $couponData,
                'admin_commission_data' => $service->admin_commission_data,
                'accept_hold_time' => Carbon::now()->addMinutes(5), // 5 minutes to accept
            ]);

            // Broadcast to nearby drivers
            event(new OrderCreated($order));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order->load(['service', 'zone', 'coupon'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get nearby orders for drivers
    public function getNearbyOrders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_lat' => 'required|numeric|between:-90,90',
            'location_lng' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:50', // km
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $driver = $request->user();
        $radius = $request->radius ?? 10; // Default 10km radius

        // Get orders within radius that driver hasn't rejected
        $orders = Order::with(['user', 'service', 'zone'])
            ->where('status', 'placed')
            ->where('service_id', $driver->service_id)
            ->whereRaw("
                (6371 * acos(cos(radians(?)) * cos(radians(source_lat)) * 
                cos(radians(source_lng) - radians(?)) + sin(radians(?)) * 
                sin(radians(source_lat)))) < ?
            ", [$request->location_lat, $request->location_lng, $request->location_lat, $radius])
            ->whereRaw('NOT JSON_CONTAINS(rejected_driver_ids, ?)', [json_encode($driver->id)])
            ->where('accept_hold_time', '>', now())
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    // Driver accepts order
    public function acceptOrder(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'location_lat' => 'required|numeric|between:-90,90',
            'location_lng' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $driver = $request->user();
            $order = Order::find($orderId);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            if ($order->status !== 'placed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order is no longer available'
                ], 400);
            }

            // Check if driver is already assigned to another active order
            $activeOrder = Order::where('driver_id', $driver->id)
                ->whereIn('status', ['driver_accepted', 'driver_arrived', 'on_ride'])
                ->first();

            if ($activeOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have an active order'
                ], 400);
            }

            // Accept the order
            $order->update([
                'driver_id' => $driver->id,
                'status' => 'driver_accepted',
                'accepted_driver_ids' => array_merge($order->accepted_driver_ids ?? [], [$driver->id])
            ]);

            // Update driver location
            $driver->updateLocation($request->location_lat, $request->location_lng);

            // Broadcast update
            event(new OrderUpdated($order));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order accepted successfully',
                'data' => $order->load(['user', 'service', 'zone'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept order: ' . $e->getMessage()
            ], 500);
        }
    }

    // Driver rejects order
    public function rejectOrder(Request $request, $orderId)
    {
        $driver = $request->user();
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $order->addRejectedDriver($driver->id);

        return response()->json([
            'success' => true,
            'message' => 'Order rejected successfully'
        ]);
    }

    // Update order status
    public function updateOrderStatus(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:driver_arrived,on_ride,completed,cancelled',
            'location_lat' => 'sometimes|numeric|between:-90,90',
            'location_lng' => 'sometimes|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $order = Order::find($orderId);
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Update order status
            $order->update(['status' => $request->status]);

            // Update driver location if provided
            if ($request->has('location_lat') && $request->has('location_lng')) {
                $order->driver->updateLocation($request->location_lat, $request->location_lng);
                event(new DriverLocationUpdated($order->driver, $order));
            }

            // Handle completion
            if ($request->status === 'completed') {
                $this->handleOrderCompletion($order);
            }

            event(new OrderUpdated($order));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'data' => $order->load(['user', 'driver', 'service'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get user's orders
    public function getUserOrders(Request $request)
    {
        $user = $request->user();
        $orders = Order::with(['driver', 'service', 'zone'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    // Get driver's orders
    public function getDriverOrders(Request $request)
    {
        $driver = $request->user();
        $orders = Order::with(['user', 'service', 'zone'])
            ->where('driver_id', $driver->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    // Get order details
    public function getOrderDetails($orderId)
    {
        $order = Order::with(['user', 'driver', 'service', 'zone', 'coupon', 'reviews'])
            ->find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    // Private helper methods
    private function parseDuration($duration)
    {
        // Parse duration string like "15 mins" or "1 hour 30 mins"
        preg_match_all('/(\d+)\s*(hour|min)/', $duration, $matches);
        
        $totalMinutes = 0;
        for ($i = 0; $i < count($matches[1]); $i++) {
            $value = intval($matches[1][$i]);
            $unit = $matches[2][$i];
            
            if ($unit === 'hour') {
                $totalMinutes += $value * 60;
            } else {
                $totalMinutes += $value;
            }
        }
        
        return $totalMinutes;
    }

    private function handleOrderCompletion($order)
    {
        // Mark payment as completed
        $order->update(['payment_status' => true]);

        // Create wallet transaction for driver
        $driverEarning = $order->calculateTotal();
        
        WalletTransaction::create([
            'user_id' => $order->driver_id,
            'amount' => $driverEarning,
            'payment_type' => $order->payment_type,
            'transaction_id' => $order->id,
            'order_type' => 'city',
            'user_type' => 'driver',
            'type' => 'credit',
            'note' => 'Ride amount credited'
        ]);

        // Update driver wallet
        $order->driver->increment('wallet_amount', $driverEarning);

        // Increment coupon usage if used
        if ($order->coupon_id) {
            $order->coupon->incrementUsage();
        }
    }
}
