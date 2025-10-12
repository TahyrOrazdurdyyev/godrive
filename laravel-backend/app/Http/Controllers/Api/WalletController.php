<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    /**
     * Get driver wallet balance
     * GET /api/wallet/driver?driver_id={id}
     */
    public function getDriverBalance(Request $request)
    {
        try {
            $driverId = $request->input('driver_id');

            if (!$driverId) {
                return response()->json([
                    'success' => false,
                    'message' => 'driver_id is required'
                ], 400);
            }

            $driver = DB::connection('mysql_main')->table('drivers')
                ->where('id', $driverId)
                ->first(['wallet_amount']);

            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'balance' => $driver->wallet_amount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer wallet balance
     * GET /api/wallet/customer?user_id={id}
     */
    public function getCustomerBalance(Request $request)
    {
        try {
            $userId = $request->input('user_id');

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'user_id is required'
                ], 400);
            }

            $user = DB::connection('mysql_main')->table('users')
                ->where('id', $userId)
                ->first(['wallet_amount']);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'balance' => $user->wallet_amount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wallet transactions
     * GET /api/wallet/transactions?user_type={driver|customer}&user_id={id}
     */
    public function getTransactions(Request $request)
    {
        try {
            $userType = $request->input('user_type');
            $userId = $request->input('user_id');

            if (!$userType || !$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'user_type and user_id are required'
                ], 400);
            }

            $transactions = DB::connection('mysql_main')->table('wallet_transactions')
                ->where('user_type', $userType)
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'transactions' => $transactions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add money to wallet
     * POST /api/wallet/add
     */
    public function addMoney(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_type' => 'required|string|in:driver,customer',
                'user_id' => 'required|integer',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
                'transaction_id' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            DB::connection('mysql_main')->beginTransaction();

            // Update wallet balance
            $table = $request->user_type === 'driver' ? 'drivers' : 'users';
            DB::connection('mysql_main')->table($table)
                ->where('id', $request->user_id)
                ->increment('wallet_amount', $request->amount);

            // Create transaction record
            DB::connection('mysql_main')->table('wallet_transactions')->insert([
                'user_type' => $request->user_type,
                'user_id' => $request->user_id,
                'amount' => $request->amount,
                'type' => 'credit',
                'payment_method' => $request->payment_method,
                'transaction_id' => $request->transaction_id,
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::connection('mysql_main')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Money added successfully'
            ]);
        } catch (\Exception $e) {
            DB::connection('mysql_main')->rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Withdraw money (driver only)
     * POST /api/wallet/withdraw
     */
    public function withdrawMoney(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'driver_id' => 'required|integer',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
                'note' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            // Check balance
            $driver = DB::connection('mysql_main')->table('drivers')
                ->where('id', $request->driver_id)
                ->first(['wallet_amount']);

            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }

            if ($driver->wallet_amount < $request->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance'
                ], 400);
            }

            DB::connection('mysql_main')->beginTransaction();

            // Create withdrawal request
            $withdrawalId = DB::connection('mysql_main')->table('withdrawals')->insertGetId([
                'driver_id' => $request->driver_id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'note' => $request->note,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Deduct from wallet
            DB::connection('mysql_main')->table('drivers')
                ->where('id', $request->driver_id)
                ->decrement('wallet_amount', $request->amount);

            // Create transaction record
            DB::connection('mysql_main')->table('wallet_transactions')->insert([
                'user_type' => 'driver',
                'user_id' => $request->driver_id,
                'amount' => $request->amount,
                'type' => 'debit',
                'payment_method' => $request->payment_method,
                'transaction_id' => 'WITHDRAW_' . $withdrawalId,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::connection('mysql_main')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully',
                'withdrawal_id' => $withdrawalId
            ]);
        } catch (\Exception $e) {
            DB::connection('mysql_main')->rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
