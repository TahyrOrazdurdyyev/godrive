<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SubscriptionPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $plans = SubscriptionPlan::orderBy('display_order', 'asc')->get();
        return view("subscription_plans.index")->with('plans', $plans);
    }

    public function save($id = '')
    {
        $plan = null;
        if ($id != '') {
            $plan = SubscriptionPlan::find($id);
        }
        return view("subscription_plans.save")->with('id', $id)->with('plan', $plan);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|in:free,paid',
            'amount' => 'required_if:type,paid|numeric|min:0',
            'duration_days' => 'required|integer|min:-1',
            'total_orders' => 'required|integer|min:-1',
            'description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'plan_points' => 'nullable|array',
            'plan_points.*' => 'string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only([
            'title', 
            'type', 
            'amount', 
            'duration_days', 
            'total_orders', 
            'description', 
            'display_order'
        ]);

        $data['enable'] = $request->has('enable') ? 1 : 0;
        
        if ($request->type == 'free') {
            $data['amount'] = 0;
        }

        $data['plan_points'] = $request->plan_points ?? [];

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('storage/subscription_plans'), $imageName);
            $data['image'] = '/storage/subscription_plans/' . $imageName;
        }

        if ($request->id) {
            // Update existing plan
            $plan = SubscriptionPlan::find($request->id);
            if (!$plan) {
                return redirect()->back()->with('error', 'Subscription plan not found');
            }
            
            // If new image uploaded, delete old one
            if ($request->hasFile('image') && $plan->image) {
                $oldImagePath = public_path($plan->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            } else {
                // Keep old image if no new one uploaded
                unset($data['image']);
            }
            
            $plan->update($data);
            $message = 'Subscription plan updated successfully';
        } else {
            // Create new plan
            SubscriptionPlan::create($data);
            $message = 'Subscription plan created successfully';
        }

        return redirect()->route('subscription-plans.index')->with('success', $message);
    }

    public function toggleStatus(Request $request, $id)
    {
        $plan = SubscriptionPlan::find($id);
        if (!$plan) {
            return response()->json(['success' => false, 'message' => 'Plan not found']);
        }

        $plan->enable = $request->enable;
        $plan->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function destroy($id)
    {
        $plan = SubscriptionPlan::find($id);
        if (!$plan) {
            return response()->json(['success' => false, 'message' => 'Plan not found']);
        }

        // Delete image if exists
        if ($plan->image) {
            $imagePath = public_path($plan->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $plan->delete();

        return response()->json(['success' => true, 'message' => 'Plan deleted successfully']);
    }

    public function getPlansData()
    {
        $plans = SubscriptionPlan::orderBy('display_order', 'asc')->get();
        $data = [];

        foreach ($plans as $plan) {
            $data[] = [
                'id' => $plan->id,
                'title' => $plan->title,
                'type' => $plan->type,
                'amount' => $plan->amount,
                'duration_days' => $plan->duration_days,
                'total_orders' => $plan->total_orders,
                'enable' => $plan->enable,
                'image' => $plan->image ? asset($plan->image) : '',
                'plan_points' => $plan->plan_points ?? [],
                'display_order' => $plan->display_order,
            ];
        }

        return response()->json(['data' => $data]);
    }

    public function getActivePlans()
    {
        $plans = SubscriptionPlan::where('enable', 1)->orderBy('display_order', 'asc')->get();
        
        return response()->json([
            'success' => true,
            'plans' => $plans->map(function($plan) {
                return [
                    'id' => $plan->id,
                    'title' => $plan->title,
                    'type' => $plan->type,
                    'amount' => $plan->amount,
                    'duration_days' => $plan->duration_days,
                    'total_orders' => $plan->total_orders,
                    'description' => $plan->description,
                    'image' => $plan->image ? asset($plan->image) : '',
                    'plan_points' => $plan->plan_points ?? [],
                    'display_order' => $plan->display_order,
                ];
            })
        ]);
    }

    public function SubscriptionHistory()
    {
        return view('subscription_plans.history');
    }

    public function currentSubscriberList($id)
    {
        return view('subscription_plans.current_subscriber')->with('id', $id);
    }
}
