<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Get user profile by firebase_uid
     * GET /api/user/profile?uid={firebase_uid}
     */
    public function getProfile(Request $request)
    {
        try {
            $uid = $request->input('uid');

            if (!$uid) {
                return response()->json([
                    'success' => false,
                    'message' => 'UID is required'
                ], 400);
            }

            $user = DB::connection('mysql_main')->table('users')
                ->where('firebase_uid', $uid)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create or update user
     * POST /api/user/register
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'uid' => 'required|string',
                'full_name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone_number' => 'nullable|string|max:50',
                'country_code' => 'nullable|string|max:10',
                'profile_pic' => 'nullable|string',
                'fcm_token' => 'nullable|string',
                'login_type' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $existingUser = DB::connection('mysql_main')->table('users')
                ->where('firebase_uid', $request->uid)
                ->first();

            if ($existingUser) {
                DB::connection('mysql_main')->table('users')
                    ->where('firebase_uid', $request->uid)
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
                    'message' => 'User updated successfully'
                ]);
            } else {
                DB::connection('mysql_main')->table('users')->insert([
                    'firebase_uid' => $request->uid,
                    'full_name' => $request->full_name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'country_code' => $request->country_code ?? '+993',
                    'profile_pic' => $request->profile_pic,
                    'fcm_token' => $request->fcm_token,
                    'login_type' => $request->login_type ?? 'phone',
                    'wallet_amount' => 0.00,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'User registered successfully'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user profile
     * PUT /api/user/profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'uid' => 'required|string',
                'full_name' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone_number' => 'nullable|string|max:50',
                'country_code' => 'nullable|string|max:10',
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
                ->where('firebase_uid', $request->uid)
                ->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update FCM token
     * POST /api/user/update-fcm
     */
    public function updateFcmToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'uid' => 'required|string',
                'fcm_token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            DB::connection('mysql_main')->table('users')
                ->where('firebase_uid', $request->uid)
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
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
