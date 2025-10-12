<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class VehicleTypeController extends Controller
{
    public function index()
    {
        try {
            $services = Service::orderBy('id', 'desc')->get();
            
            // Don't parse title here - let mobile app handle it
            // Just return services as-is from database

            return response()->json([
                'success' => true,
                'data' => $services
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'image' => 'required|string',
            'enable' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $imageUrl = null;
            if ($request->image && str_starts_with($request->image, 'data:image')) {
                $image = $request->image;
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace('data:image/jpg;base64,', '', $image);
                $image = str_replace('data:image/jpeg;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = 'vehicle_' . time() . '.png';
                
                $imageData = base64_decode($image);
                $path = 'vehicle_types/' . $imageName;
                Storage::disk('public')->put($path, $imageData);
                
                $imageUrl = url('/storage/' . $path);
            }

            $service = Service::create([
                'title' => $request->title,
                'image' => $imageUrl,
                'enable' => $request->enable,
                'offer_rate' => 0,
                'intercity_type' => 0,
                'is_ac_non_ac' => 0,
                'ac_charge' => 0,
                'non_ac_charge' => 0,
                'basic_fare' => 0,
                'basic_fare_charge' => 0,
                'holding_minute' => 0,
                'holding_minute_charge' => 0,
                'night_charge' => 0,
                'per_minute_charge' => 0,
                'km_charge' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vehicle type created successfully',
                'data' => $service
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggle(Request $request, $id)
    {
        try {
            $service = Service::find($id);
            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle type not found'
                ], 404);
            }

            $service->enable = $request->enable;
            $service->save();

            return response()->json([
                'success' => true,
                'data' => $service
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $service = Service::find($id);
            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle type not found'
                ], 404);
            }

            // Delete image
            if ($service->image) {
                $imagePath = str_replace(url('/storage/'), '', $service->image);
                Storage::disk('public')->delete($imagePath);
            }

            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Vehicle type deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
