<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
    // Customer Authentication
    public function customerLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firebase_uid' => 'required|string',
            'email' => 'required|email',
            'full_name' => 'required|string',
            'phone_number' => 'nullable|string',
            'country_code' => 'nullable|string',
            'login_type' => 'required|in:email,phone,google,apple',
            'fcm_token' => 'nullable|string',
            'profile_pic' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find or create user
        $user = User::where('firebase_uid', $request->firebase_uid)->first();
        
        if (!$user) {
            $user = User::create([
                'firebase_uid' => $request->firebase_uid,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'country_code' => $request->country_code,
                'login_type' => $request->login_type,
                'profile_pic' => $request->profile_pic,
                'fcm_token' => $request->fcm_token,
                'is_active' => true,
            ]);
        } else {
            // Update existing user
            $user->update([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'country_code' => $request->country_code,
                'fcm_token' => $request->fcm_token,
                'profile_pic' => $request->profile_pic,
            ]);
        }

        $token = $user->createToken('customer-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    // Driver Authentication
    public function driverLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firebase_uid' => 'required|string',
            'email' => 'required|email',
            'full_name' => 'required|string',
            'phone_number' => 'nullable|string',
            'country_code' => 'nullable|string',
            'login_type' => 'required|in:email,phone,google,apple',
            'fcm_token' => 'nullable|string',
            'profile_pic' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find or create driver
        $driver = Driver::where('firebase_uid', $request->firebase_uid)->first();
        
        if (!$driver) {
            $driver = Driver::create([
                'firebase_uid' => $request->firebase_uid,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'country_code' => $request->country_code,
                'login_type' => $request->login_type,
                'profile_pic' => $request->profile_pic,
                'fcm_token' => $request->fcm_token,
                'is_active' => true,
                'is_online' => false,
                'document_verification' => false,
            ]);
        } else {
            // Update existing driver
            $driver->update([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'country_code' => $request->country_code,
                'fcm_token' => $request->fcm_token,
                'profile_pic' => $request->profile_pic,
            ]);
        }

        $token = $driver->createToken('driver-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'driver' => $driver->load(['service', 'subscriptionPlan']),
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    // Update Customer Profile
  public function updateCustomerProfile(Request $request)
{
    $user = $request->user();
    
    $validator = Validator::make($request->all(), [
        'full_name' => 'sometimes|string|max:255',
        'email' => 'sometimes|email|unique:users,email,' . $user->id,
        'phone_number' => 'sometimes|string|max:20',
        'country_code' => 'sometimes|string|max:10',
        'profile_pic' => 'sometimes|string|max:500',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors()
        ], 422);
    }

    // Update user fields
    if ($request->has('full_name')) {
        $user->full_name = $request->full_name;
    }
    if ($request->has('email')) {
        $user->email = $request->email;
    }
    if ($request->has('phone_number')) {
        $user->phone_number = $request->phone_number;
    }
    if ($request->has('country_code')) {
        $user->country_code = $request->country_code;
    }
    if ($request->has('profile_pic')) {
        $user->profile_pic = $request->profile_pic;
    }

    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'Profile updated successfully',
        'data' => $user
    ]);
} 

    // Update Driver Profile
    public function updateDriverProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'sometimes|required|string',
            'phone_number' => 'sometimes|nullable|string',
            'country_code' => 'sometimes|nullable|string',
            'profile_pic' => 'sometimes|nullable|string',
            'fcm_token' => 'sometimes|nullable|string',
            'vehicle_information' => 'sometimes|nullable|array',
            'service_id' => 'sometimes|nullable|exists:services,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $driver = $request->user();
        $driver->update($request->only([
            'full_name', 'phone_number', 'country_code', 'profile_pic', 
            'fcm_token', 'vehicle_information', 'service_id'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $driver->load(['service', 'subscriptionPlan'])
        ]);
    }

    // Update Driver Location
    public function updateDriverLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_lat' => 'required|numeric|between:-90,90',
            'location_lng' => 'required|numeric|between:-180,180',
            'rotation' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $driver = $request->user();
        $driver->updateLocation(
            $request->location_lat,
            $request->location_lng,
            $request->rotation
        );

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully'
        ]);
    }

    // Update Driver Online Status
    public function updateDriverStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_online' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $driver = $request->user();
        $driver->update(['is_online' => $request->is_online]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'data' => ['is_online' => $driver->is_online]
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    // Get Current User/Driver
    public function me(Request $request)
    {
        $user = $request->user();
        
        if ($user instanceof Driver) {
            $user->load(['service', 'subscriptionPlan']);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
}

