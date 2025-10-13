<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IntercityService;
use Illuminate\Support\Facades\Storage;

class IntercityServiceController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $services = IntercityService::orderBy('created_at', 'desc')->get();
        return view("intercity_service.index", compact('services'));
    }

    public function create()
    {
        return view('intercity_service.create');
    }

    public function edit($id)
    {
        $service = IntercityService::find($id);
        return view('intercity_service.edit', compact('id', 'service'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'price_per_seat' => 'required|numeric|min:0',
                'price_full_vehicle' => 'required|numeric|min:0',
                'image' => 'nullable|image|max:2048'
            ]);

            $data = [
                'title' => $validated['title'],
                'price_per_seat' => $validated['price_per_seat'],
                'price_full_vehicle' => $validated['price_full_vehicle'],
                'enable' => $request->has('enable') ? 1 : 0
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('intercity_services', $imageName, 'public');
                $data['image'] = '/storage/' . $imagePath;
            }

            $id = $request->input('id');
            
            if ($id && $id != '0') {
                // Update existing
                $service = IntercityService::find($id);
                if (!$service) {
                    return redirect()->back()->with('error', 'Service not found');
                }
                
                // Delete old image if new one uploaded
                if (isset($data['image']) && $service->image) {
                    $oldImagePath = str_replace('/storage/', '', $service->image);
                    Storage::disk('public')->delete($oldImagePath);
                }
                
                $service->update($data);
            } else {
                // Create new
                IntercityService::create($data);
            }

            return redirect()->route('intercity-service')->with('success', 'Service saved successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $service = IntercityService::find($id);
            
            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found'
                ], 404);
            }

            $service->enable = $request->input('enable') ? 1 : 0;
            $service->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
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
            $service = IntercityService::find($id);
            
            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found'
                ], 404);
            }

            // Delete image
            if ($service->image) {
                $imagePath = str_replace('/storage/', '', $service->image);
                Storage::disk('public')->delete($imagePath);
            }

            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ridesList(){
        return view('intercity_service.ride-list');
    }

    public function rideView($id){
        return view('intercity_service.ride-view', compact('id'));
    }
}
