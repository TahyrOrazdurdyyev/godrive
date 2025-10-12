<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DriverWalletController extends Controller
{
    /**
     * Get driver wallet balance
     * GET /api/wallet/driver?driver_id=X
     */
    public function getBalance(Request $request)
    {
        try {
            $driverId = $request->query('driver_id');
            
            if (!$driverId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver ID is required'
                ], 400);
            }

            $driver = DB::connection('mysql_main')
                ->table('drivers')
                ->where('id', $driverId)
                ->select('wallet_amount')
                ->first();

            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'balance' => $driver->wallet_amount ?? '0.00'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get balance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add/subtract money to/from driver wallet
     * POST /api/wallet/driver/transaction
     */
    public function addTransaction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'driver_id' => 'required|integer',
                'amount' => 'required|numeric',
                'order_id' => 'required|string',
                'order_type' => 'required|in:city,intercity',
                'note' => 'nullable|string',
                'payment_type' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            // Get current wallet amount
            $driver = DB::connection('mysql_main')
                ->table('drivers')
                ->where('id', $request->driver_id)
                ->first();

            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }

            $currentBalance = floatval($driver->wallet_amount ?? 0);
            $newBalance = $currentBalance + floatval($request->amount);

            // Update driver wallet
            DB::connection('mysql_main')
                ->table('drivers')
                ->where('id', $request->driver_id)
                ->update([
                    'wallet_amount' => $newBalance,
                    'updated_at' => now()
                ]);

            // Create transaction record
            DB::connection('mysql_main')
                ->table('wallet_transactions')
                ->insert([
                    'user_id' => $request->driver_id,
                    'user_type' => 'driver',
                    'amount' => $request->amount,
                    'order_id' => $request->order_id,
                    'order_type' => $request->order_type,
                    'payment_type' => $request->payment_type ?? 'wallet',
                    'note' => $request->note ?? '',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaction completed',
                'new_balance' => number_format($newBalance, 2, '.', '')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get driver transactions
     * GET /api/wallet/driver/transactions?driver_id=X
     */
    public function getTransactions(Request $request)
    {
        try {
            $driverId = $request->query('driver_id');
            
            if (!$driverId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver ID is required'
                ], 400);
            }

            $transactions = DB::connection('mysql_main')
                ->table('wallet_transactions')
                ->where('user_id', $driverId)
                ->where('user_type', 'driver')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'transactions' => $transactions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get transactions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
