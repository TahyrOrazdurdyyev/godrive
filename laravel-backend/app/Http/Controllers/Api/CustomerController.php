<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Register a new customer
     * POST /api/customer/register
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'firebase_uid' => 'required|string',
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone_number' => 'nullable|string|max:255',
                'country_code' => 'nullable|string|max:255',
                'login_type' => 'required|string|in:email,phone,google,apple',
                'profile_pic' => 'nullable|string',
                'fcm_token' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $existingCustomer = DB::connection('mysql_main')->table('users')
                ->where('firebase_uid', $request->firebase_uid)
                ->first();

            if ($existingCustomer) {
                DB::connection('mysql_main')->table('users')
                    ->where('firebase_uid', $request->firebase_uid)
                    ->update([
                        'full_name' => $request->full_name,
                        'email' => $request->email,
                        'phone_number' => $request->phone_number,
                        'country_code' => $request->country_code,
                        'profile_pic' => $request->profile_pic,
                        'fcm_token' => $request->fcm_token,
                        'updated_at' => now(),
                    ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Customer information updated successfully'
                ]);
            } else {
                DB::connection('mysql_main')->table('users')->insert([
                    'firebase_uid' => $request->firebase_uid,
                    'full_name' => $request->full_name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'country_code' => $request->country_code,
                    'login_type' => $request->login_type,
                    'profile_pic' => $request->profile_pic,
                    'fcm_token' => $request->fcm_token,
                    'wallet_amount' => 0.00,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Customer registered successfully'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer profile by firebase_uid
     * GET /api/customer/profile?firebase_uid={uid}
     */
    public function getProfile(Request $request)
    {
        try {
            $firebaseUid = $request->input('firebase_uid');

            if (!$firebaseUid) {
                return response()->json([
                    'success' => false,
                    'message' => 'firebase_uid is required'
                ], 400);
            }

            $customer = DB::connection('mysql_main')->table('users')
                ->where('firebase_uid', $firebaseUid)
                ->first();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'customer' => $customer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update customer profile
     * PUT /api/customer/profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'firebase_uid' => 'required|string',
                'full_name' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone_number' => 'nullable|string|max:255',
                'country_code' => 'nullable|string|max:255',
                'profile_pic' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $updateData = array_filter($request->only([
                'full_name', 'email', 'phone_number', 'country_code', 'profile_pic'
            ]), function($value) {
                return !is_null($value);
            });

            $updateData['updated_at'] = now();

            DB::connection('mysql_main')->table('users')
                ->where('firebase_uid', $request->firebase_uid)
                ->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update FCM token
     * POST /api/customer/update-fcm
     */
    public function updateFcmToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'firebase_uid' => 'required|string',
                'fcm_token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            DB::connection('mysql_main')->table('users')
                ->where('firebase_uid', $request->firebase_uid)
                ->update([
                    'fcm_token' => $request->fcm_token,
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'FCM token updated'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
