<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Review;
use App\Models\SubscriptionHistory;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use App\Models\Setting;
use App\Models\SubscriptionPlan;

class DriverController extends Controller
{   

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view("drivers.index");
    }

    public function edit($id)
    {
        return view('drivers.edit')->with('id', $id);
    }

    public function view($id)
    {
        return view('drivers.view')->with('id', $id);
    }

    public function getDriverData($id)
    {
        $driver = Driver::with('subscriptionPlan')->where('uid', $id)->first();
        
        if (!$driver) {
            return response()->json(['success' => false, 'message' => 'Driver not found'], 404);
        }
        
        return response()->json([
            'success' => true,
            'driver' => [
                'id' => $driver->id,
                'uid' => $driver->uid,
                'full_name' => $driver->full_name,
                'email' => $driver->email,
                'phone' => $driver->phone,
                'country_code' => $driver->country_code,
                'profile_pic' => $driver->profile_pic,
                'is_active' => $driver->is_active,
                'is_online' => $driver->is_online,
                'wallet_amount' => $driver->wallet_amount,
                'vehicle_number' => $driver->vehicle_number,
                'vehicle_type' => $driver->vehicle_type,
                'subscription_plan' => $driver->subscriptionPlan,
                'subscription_total_orders' => $driver->subscription_total_orders,
                'subscription_expiry_date' => $driver->subscription_expiry_date,
            ]
        ]);
    }

    public function getDriverOrders($id)
    {
        try {
            $orders = Order::with(['user'])
                ->where('driver_id', $id)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            return response()->json([
                'success' => true,
                'orders' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDriverReviews($id)
    {
        try {
            $reviews = Review::with(['user'])
                ->where('driver_id', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'reviews' => $reviews
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDriverSubscriptionHistory($id)
    {
        try {
            $history = SubscriptionHistory::with(['subscriptionPlan'])
                ->where('user_id', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'history' => $history
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDriverWalletTransactions($id)
    {
        try {
            $transactions = WalletTransaction::where('user_id', $id)
                ->where('user_type', 'driver')
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get();

            return response()->json([
                'success' => true,
                'transactions' => $transactions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDriverWithdrawals($id)
    {
        try {
            $withdrawals = Withdrawal::where('driver_id', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'withdrawals' => $withdrawals
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getSettings()
    {
        try {
            $currency = Setting::getValue('currency', [
                'symbol' => '$',
                'decimal_digits' => 2,
                'symbol_at_right' => false
            ]);

            $adminCommission = Setting::getValue('admin_commission', [
                'is_enabled' => false,
                'type' => 'percentage',
                'amount' => 0
            ]);

            $globalSettings = Setting::getValue('global_settings', [
                'subscription_model' => false
            ]);

            return response()->json([
                'success' => true,
                'settings' => [
                    'currency' => $currency,
                    'admin_commission' => $adminCommission,
                    'global_settings' => $globalSettings
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function driverDocuments($id)
    {
        return view('drivers.documentIndex', compact('id'));
    }

    public function rulesIndex()
    {
        return view('drivers.rules.index');
    }

    public function deletedRulesIndex()
    {
        return view('drivers.rules.deletedIndex');
    }

    public function saveRule($id)
    {
        return view('drivers.rules.save', compact('id'));
    }

    public function driverDocumentUpload($driverId, $id)
    {
        return view('drivers.driverDocumentUpload', compact('driverId', 'id'));
    }

    public function getDriversList(Request $request)
    {
        try {
            $type = $request->input('type', 'all');

            $query = DB::connection('mysql_main')->table('drivers');

            if ($type === 'pending') {
                $query->where('is_active', 0);
            } elseif ($type === 'approved') {
                $query->where('is_active', 1);
            }

            $drivers = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'drivers' => $drivers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function approveDriver($id)
    {
        try {
            DB::connection('mysql_main')->table('drivers')
                ->where('id', $id)
                ->update([
                    'is_active' => 1,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Driver approved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function rejectDriver($id)
    {
        try {
            DB::connection('mysql_main')->table('drivers')
                ->where('id', $id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Driver rejected and deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function addWallet(Request $request, $id)
    {
        try {
            $driver = Driver::where('uid', $id)->first();
            
            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }

            $validated = $request->validate([
                'amount' => 'required|numeric|min:0',
                'note' => 'nullable|string'
            ]);

            // Update driver wallet
            $driver->wallet_amount = ($driver->wallet_amount ?? 0) + $validated['amount'];
            $driver->save();

            // Create wallet transaction record
            WalletTransaction::create([
                'user_id' => $driver->id,
                'user_type' => 'driver',
                'amount' => $validated['amount'],
                'type' => 'credit',
                'payment_type' => 'admin_topup',
                'note' => $validated['note'] ?? 'Wallet topup by admin',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Wallet updated successfully',
                'new_balance' => $driver->wallet_amount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating wallet: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignSubscription(Request $request, $id)
    {
        try {
            $driver = Driver::where('uid', $id)->first();
            
            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }

            $validated = $request->validate([
                'subscription_plan_id' => 'required'
            ]);

            $plan = SubscriptionPlan::find($validated['subscription_plan_id']);

            // Calculate expiry date
            $expiryDate = now()->addDays($plan->duration_days);

            // Update driver subscription
            $driver->subscription_plan_id = $plan->id;
            $driver->subscription_expiry_date = $expiryDate;
            $driver->save();

            // Create subscription history record
            SubscriptionHistory::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'user_id' => $driver->id,
                'subscription_plan_id' => $plan->id,
                'subscription_plan_data' => [
                    'title' => $plan->title,
                    'amount' => $plan->amount,
                    'duration_days' => $plan->duration_days,
                    'total_orders' => $plan->total_orders
                ],
                'expiry_date' => $expiryDate,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription assigned successfully',
                'expiry_date' => $expiryDate->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning subscription: ' . $e->getMessage()
            ], 500);
        }
    }

}
