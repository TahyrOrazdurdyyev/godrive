<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Zone;
use App\Models\Banner;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    // Get all enabled services
    public function getServices()
    {
        $services = Service::enabled()->get();

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    // Get city services (non-intercity)
    public function getCityServices()
    {
        $services = Service::enabled()->city()->get();

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    // Get intercity services
    public function getIntercityServices()
    {
        $services = Service::enabled()->intercity()->get();

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    // Get service by ID
    public function getService($serviceId)
    {
        $service = Service::enabled()->find($serviceId);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $service
        ]);
    }

    // Calculate fare estimate
    public function calculateFare(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'distance' => 'required|numeric|min:0',
            'duration' => 'nullable|string',
            'is_ac_selected' => 'boolean',
            'coupon_code' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $service = Service::find($request->service_id);
        $duration = $this->parseDuration($request->duration ?? '0 mins');
        $isNightTime = $service->isNightTime();

        $fare = $service->calculateFare(
            $request->distance,
            $duration,
            $request->is_ac_selected ?? false,
            $isNightTime
        );

        $response = [
            'base_fare' => $service->basic_fare_charge,
            'distance_fare' => $request->distance * $service->km_charge,
            'time_fare' => $duration * $service->per_minute_charge,
            'ac_charges' => $request->is_ac_selected ? $service->ac_charge : $service->non_ac_charge,
            'night_charges' => $isNightTime ? $service->night_charge : 0,
            'subtotal' => $fare,
            'total' => $fare,
            'is_night_time' => $isNightTime,
        ];

        // Apply coupon if provided
        if ($request->coupon_code && $request->user()) {
            $coupon = Coupon::where('code', $request->coupon_code)->valid()->first();
            if ($coupon && $coupon->canBeUsedBy($request->user()->id, $fare)) {
                $discount = $coupon->calculateDiscount($fare);
                $response['coupon'] = [
                    'code' => $coupon->code,
                    'title' => $coupon->title,
                    'discount' => $discount,
                    'type' => $coupon->discount_type
                ];
                $response['total'] = $fare - $discount;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    // Get all enabled zones
    public function getZones()
    {
        $zones = Zone::enabled()->get();

        return response()->json([
            'success' => true,
            'data' => $zones
        ]);
    }

    // Find zone by coordinates
    public function findZone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $zone = Zone::enabled()->get()->first(function ($zone) use ($request) {
            return $zone->isPointInZone($request->lat, $request->lng);
        });

        if (!$zone) {
            return response()->json([
                'success' => false,
                'message' => 'No service available in this area'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $zone
        ]);
    }

    // Get enabled banners
    public function getBanners()
    {
        $banners = Banner::enabled()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $banners
        ]);
    }

    // Validate coupon
    public function validateCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string',
            'order_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $coupon = Coupon::where('code', $request->coupon_code)->valid()->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired coupon'
            ], 404);
        }

        $user = $request->user();
        if (!$coupon->canBeUsedBy($user->id, $request->order_amount)) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon cannot be used for this order'
            ], 400);
        }

        $discount = $coupon->calculateDiscount($request->order_amount);

        return response()->json([
            'success' => true,
            'message' => 'Coupon is valid',
            'data' => [
                'coupon' => $coupon,
                'discount' => $discount,
                'final_amount' => $request->order_amount - $discount
            ]
        ]);
    }

    // Get available coupons for user
    public function getAvailableCoupons(Request $request)
    {
        $user = $request->user();
        $coupons = Coupon::valid()->get()->filter(function ($coupon) use ($user) {
            return $coupon->canBeUsedBy($user->id, 0); // Check basic eligibility
        });

        return response()->json([
            'success' => true,
            'data' => $coupons->values()
        ]);
    }

    // Private helper method
    private function parseDuration($duration)
    {
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
}

