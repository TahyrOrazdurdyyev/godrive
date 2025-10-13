<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OnboardingScreen;
use Illuminate\Support\Facades\Storage;

class OnBoardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $screens = OnboardingScreen::orderBy('display_order', 'asc')->get();
        return view("on-board.index", compact('screens'));
    }

    public function show($id)
    {
        $screen = null;
        if ($id != '0') {
            $screen = OnboardingScreen::find($id);
        }
        return view('on-board.save', compact('id', 'screen'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'app_type' => 'required|in:customer,driver,both',
                'display_order' => 'required|integer|min:0',
                'image' => 'nullable|image|max:2048'
            ]);

            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'] ?? '',
                'app_type' => $validated['app_type'],
                'display_order' => $validated['display_order'],
                'is_active' => $request->has('is_active') ? 1 : 0
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('onboarding', $imageName, 'public');
                $data['image'] = '/storage/' . $imagePath;
            }

            $id = $request->input('id');
            
            if ($id && $id != '0') {
                // Update existing
                $screen = OnboardingScreen::find($id);
                if (!$screen) {
                    return redirect()->back()->with('error', 'Screen not found');
                }
                
                // Delete old image if new one uploaded
                if (isset($data['image']) && $screen->image) {
                    $oldImagePath = str_replace('/storage/', '', $screen->image);
                    Storage::disk('public')->delete($oldImagePath);
                }
                
                $screen->update($data);
            } else {
                // Create new
                OnboardingScreen::create($data);
            }

            return redirect()->route('on-board')->with('success', 'Screen saved successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $screen = OnboardingScreen::find($id);
            
            if (!$screen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Screen not found'
                ], 404);
            }

            $screen->is_active = $request->input('is_active') ? 1 : 0;
            $screen->save();

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
            $screen = OnboardingScreen::find($id);
            
            if (!$screen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Screen not found'
                ], 404);
            }

            // Delete image
            if ($screen->image) {
                $imagePath = str_replace('/storage/', '', $screen->image);
                Storage::disk('public')->delete($imagePath);
            }

            $screen->delete();

            return response()->json([
                'success' => true,
                'message' => 'Screen deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
