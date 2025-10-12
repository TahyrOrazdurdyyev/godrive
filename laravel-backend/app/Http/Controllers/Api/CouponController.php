<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    /**
     * Validate and get coupon by code
     * GET /api/coupons/validate?code=CODE&amount=AMOUNT
     */
    public function validate(Request $request)
    {
        try {
            $code = $request->query('code');
            $amount = $request->query('amount', 0);

            if (!$code) {
                return response()->json([
                    'success' => false,
                    'message' => 'Coupon code is required'
                ], 400);
            }

            $coupon = DB::connection('mysql_main')
                ->table('coupons')
                ->where('code', $code)
                ->where('enable', true)
                ->where('expire_at', '>=', now())
                ->whereNull('deleted_at')
                ->first();

            if (!$coupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired coupon'
                ], 404);
            }

            // Check minimum amount
            if ($amount < $coupon->minimum_amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minimum order amount not met',
                    'minimum_amount' => $coupon->minimum_amount
                ], 400);
            }

            // Calculate discount
            $discount = 0;
            if ($coupon->discount_type === 'percentage') {
                $discount = ($amount * $coupon->discount) / 100;
                if ($coupon->maximum_discount && $discount > $coupon->maximum_discount) {
                    $discount = $coupon->maximum_discount;
                }
            } else {
                $discount = $coupon->discount;
            }

            return response()->json([
                'success' => true,
                'coupon' => $coupon,
                'discount' => round($discount, 2),
                'final_amount' => round($amount - $discount, 2)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to validate coupon',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all active coupons
     * GET /api/coupons
     */
    public function getAll()
    {
        try {
            $coupons = DB::connection('mysql_main')
                ->table('coupons')
                ->where('enable', true)
                ->where('expire_at', '>=', now())
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'coupons' => $coupons
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get coupons',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Increment coupon usage
     * POST /api/coupons/use
     */
    public function use(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'coupon_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            DB::connection('mysql_main')
                ->table('coupons')
                ->where('id', $request->coupon_id)
                ->increment('used_count');

            return response()->json([
                'success' => true,
                'message' => 'Coupon usage recorded'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record coupon usage',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
