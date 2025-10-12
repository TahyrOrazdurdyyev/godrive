<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $banners = Banner::orderBy('position')->get();
        return view("banners.index", compact('banners'));
    }

    public function save($id)
    {
        $banner = null;
        if ($id != 0) {
            $banner = Banner::find($id);
        }
        return view('banners.save', compact('id', 'banner'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'position' => 'required|integer|min:1',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = 'banner_' . time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('banners', $filename, 'public');
                $imageUrl = url('/storage/' . $imagePath);
            }

            // Create or update banner
            if ($request->id && $request->id != 0) {
                // Update existing banner
                $banner = Banner::find($request->id);
                if ($banner) {
                    // Delete old image if new one uploaded
                    if ($imagePath && $banner->image) {
                        $oldPath = str_replace(url('/storage/'), '', $banner->image);
                        Storage::disk('public')->delete($oldPath);
                    }

                    $banner->update([
                        'position' => $request->position,
                        'image' => $imageUrl ?? $banner->image,
                        'enable' => $request->has('enable') ? 1 : 0,
                    ]);
                }
            } else {
                // Create new banner
                Banner::create([
                    'position' => $request->position,
                    'image' => $imageUrl,
                    'enable' => $request->has('enable') ? 1 : 0,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Banner saved successfully',
                'redirect' => route('banners')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving banner: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deletedIndex()
    {
        return view("banners.deleted-banners-index");
    }

    public function destroy($id)
    {
        try {
            $banner = Banner::find($id);
            if ($banner) {
                // Delete image file if exists
                if ($banner->image) {
                    $imagePath = str_replace(url('/storage/'), '', $banner->image);
                    Storage::disk('public')->delete($imagePath);
                }

                $banner->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Banner deleted successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Banner not found'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting banner: ' . $e->getMessage()
            ], 500);
        }
    }
}
