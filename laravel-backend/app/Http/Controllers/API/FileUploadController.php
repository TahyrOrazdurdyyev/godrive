<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    /**
     * Upload customer avatar
     */
    public function uploadCustomerAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            
            // Delete old avatar if exists
            if ($user->profile_pic) {
                $oldPath = str_replace(url('/storage/'), '', $user->profile_pic);
                Storage::disk('public')->delete($oldPath);
            }

            // Generate unique filename
            $filename = 'customer_' . $user->id . '_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
            
            // Process and resize image
            $image = Image::make($request->file('avatar'))
                ->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('jpg', 80);

            // Store image
            $path = 'avatars/customers/' . $filename;
            Storage::disk('public')->put($path, $image);

            // Generate full URL
            $avatarUrl = url('/storage/' . $path);

            // Update user profile
            $user->update(['profile_pic' => $avatarUrl]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar uploaded successfully',
                'data' => [
                    'avatar_url' => $avatarUrl,
                    'user' => $user
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload driver avatar
     */
    public function uploadDriverAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $driver = $request->user();
            
            // Delete old avatar if exists
            if ($driver->profile_pic) {
                $oldPath = str_replace(url('/storage/'), '', $driver->profile_pic);
                Storage::disk('public')->delete($oldPath);
            }

            // Generate unique filename
            $filename = 'driver_' . $driver->id . '_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
            
            // Process and resize image
            $image = Image::make($request->file('avatar'))
                ->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('jpg', 80);

            // Store image
            $path = 'avatars/drivers/' . $filename;
            Storage::disk('public')->put($path, $image);

            // Generate full URL
            $avatarUrl = url('/storage/' . $path);

            // Update driver profile
            $driver->update(['profile_pic' => $avatarUrl]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar uploaded successfully',
                'data' => [
                    'avatar_url' => $avatarUrl,
                    'driver' => $driver->load(['service', 'subscriptionPlan'])
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete customer avatar
     */
    public function deleteCustomerAvatar(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user->profile_pic) {
                $oldPath = str_replace(url('/storage/'), '', $user->profile_pic);
                Storage::disk('public')->delete($oldPath);
                
                $user->update(['profile_pic' => null]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Avatar deleted successfully',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete driver avatar
     */
    public function deleteDriverAvatar(Request $request)
    {
        try {
            $driver = $request->user();
            
            if ($driver->profile_pic) {
                $oldPath = str_replace(url('/storage/'), '', $driver->profile_pic);
                Storage::disk('public')->delete($oldPath);
                
                $driver->update(['profile_pic' => null]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Avatar deleted successfully',
                'data' => $driver->load(['service', 'subscriptionPlan'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
