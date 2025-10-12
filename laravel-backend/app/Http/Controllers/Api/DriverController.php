<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    /**
     * Register a new driver
     */
    public function register(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'uid' => 'required|string',
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:50',
                'country_code' => 'nullable|string|max:10',
                'profile_pic' => 'nullable|string',
                'fcm_token' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            // Check if driver already exists
            $existingDriver = DB::connection('mysql_main')->table('drivers')
                ->where('uid', $request->uid)
                ->first();

            if ($existingDriver) {
                // Update existing driver
                DB::connection('mysql_main')->table('drivers')
                    ->where('uid', $request->uid)
                    ->update([
                        'full_name' => $request->full_name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'country_code' => $request->country_code ?? '+993',
                        'profile_pic' => $request->profile_pic,
                        'fcm_token' => $request->fcm_token,
                        'updated_at' => now(),
                    ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Driver information updated successfully. Waiting for admin approval.'
                ]);
            } else {
                // Insert new driver
                DB::connection('mysql_main')->table('drivers')->insert([
                    'uid' => $request->uid,
                    'full_name' => $request->full_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'country_code' => $request->country_code ?? '+993',
                    'profile_pic' => $request->profile_pic,
                    'fcm_token' => $request->fcm_token,
                    'is_active' => 0, // Pending approval
                    'is_online' => 0,
                    'wallet_amount' => 0.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Driver registered successfully. Waiting for admin approval.'
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
     * Check driver approval status
     * GET /api/driver/status?uid={firebase_uid}
     */
    public function checkStatus(Request $request)
    {
        try {
            $uid = $request->input('uid');

            if (!$uid) {
                return response()->json([
                    'success' => false,
                    'message' => 'UID is required'
                ], 400);
            }

            $driver = DB::connection('mysql_main')->table('drivers')
                ->where('uid', $uid)
                ->first();

            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'driver' => $driver
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get driver profile by firebase_uid
     * GET /api/driver/profile?uid={firebase_uid}
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

            $driver = DB::connection('mysql_main')->table('drivers')
                ->where('uid', $uid)
                ->first();

            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'driver' => $driver
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update driver profile
     * PUT /api/driver/profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'driver_id' => 'nullable|integer',
                'uid' => 'nullable|string',
                'data' => 'required|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            if (!$request->driver_id && !$request->uid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Either driver_id or uid is required'
                ], 400);
            }

            $updateData = $request->data;
            $updateData['updated_at'] = now();

            $query = DB::connection('mysql_main')->table('drivers');
            
            if ($request->driver_id) {
                $query->where('id', $request->driver_id);
            } else {
                $query->where('uid', $request->uid);
            }

            $query->update($updateData);

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
     * Update driver location
     * POST /api/driver/update-location
     */
    public function updateLocation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'uid' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'rotation' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            DB::connection('mysql_main')->table('drivers')
                ->where('uid', $request->uid)
                ->update([
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'rotation' => $request->rotation ?? 0,
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Location updated'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update driver online status
     * POST /api/driver/update-status
     */
    public function updateStatus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'uid' => 'required|string',
                'is_online' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            DB::connection('mysql_main')->table('drivers')
                ->where('uid', $request->uid)
                ->update([
                    'is_online' => $request->is_online,
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update FCM token
     * POST /api/driver/update-fcm
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

            DB::connection('mysql_main')->table('drivers')
                ->where('uid', $request->uid)
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
