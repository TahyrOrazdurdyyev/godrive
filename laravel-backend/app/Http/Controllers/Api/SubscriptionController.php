<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    /**
     * Get all subscription plans
     * GET /api/subscriptions/plans
     */
    public function getAllPlans(Request $request)
    {
        try {
            $query = DB::connection('mysql_main')
                ->table('subscription_plans')
                ->where('enable', 1);

            $plans = $query->orderBy('amount', 'asc')->get();

            return response()->json([
                'success' => true,
                'plans' => $plans
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get subscription plans',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subscription plan by ID
     * GET /api/subscriptions/plans/{id}
     */
    public function getPlanById($id)
    {
        try {
            $plan = DB::connection('mysql_main')
                ->table('subscription_plans')
                ->where('id', $id)
                ->first();

            if (!$plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plan not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'plan' => $plan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get plan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create subscription history
     * POST /api/subscriptions/history
     */
    public function createHistory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|string',
                'user_id' => 'required|integer',
                'subscription_plan_id' => 'required',
                'subscription_plan_data' => 'required',
                'payment_type' => 'nullable|string',
                'expiry_date' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            DB::connection('mysql_main')
                ->table('subscription_history')
                ->insert([
                    'id' => $request->id,
                    'user_id' => $request->user_id,
                    'subscription_plan_id' => $request->subscription_plan_id,
                    'subscription_plan_data' => json_encode($request->subscription_plan_data),
                    'payment_type' => $request->payment_type,
                    'expiry_date' => $request->expiry_date,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription history created'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user subscription history
     * GET /api/subscriptions/history/{userId}
     */
    public function getHistory($userId)
    {
        try {
            $history = DB::connection('mysql_main')
                ->table('subscription_history')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'history' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get history',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
