<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    // Get wallet balance
    public function getBalance(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'balance' => $user->wallet_amount,
                'formatted_balance' => number_format($user->wallet_amount, 2)
            ]
        ]);
    }

    // Get wallet transactions
    public function getTransactions(Request $request)
    {
        $user = $request->user();
        $userType = $user instanceof \App\Models\Driver ? 'driver' : 'customer';
        
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->where('user_type', $userType)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    // Add money to wallet
    public function addMoney(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1|max:10000',
            'payment_type' => 'required|in:stripe,razorpay,paypal',
            'payment_id' => 'required|string', // Payment gateway transaction ID
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = $request->user();
            $userType = $user instanceof \App\Models\Driver ? 'driver' : 'customer';

            // Create transaction record
            $transaction = WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'payment_type' => $request->payment_type,
                'transaction_id' => $request->payment_id,
                'order_type' => null,
                'user_type' => $userType,
                'type' => 'credit',
                'note' => 'Money added to wallet via ' . $request->payment_type
            ]);

            // Update user wallet
            $user->increment('wallet_amount', $request->amount);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Money added to wallet successfully',
                'data' => [
                    'transaction' => $transaction,
                    'new_balance' => $user->fresh()->wallet_amount
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add money: ' . $e->getMessage()
            ], 500);
        }
    }

    // Withdraw money from wallet (for drivers)
    public function withdrawMoney(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'bank_details' => 'required|array',
            'bank_details.account_number' => 'required|string',
            'bank_details.account_holder_name' => 'required|string',
            'bank_details.bank_name' => 'required|string',
            'bank_details.ifsc_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $driver = $request->user();
        
        if (!($driver instanceof \App\Models\Driver)) {
            return response()->json([
                'success' => false,
                'message' => 'Only drivers can withdraw money'
            ], 403);
        }

        if ($driver->wallet_amount < $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient wallet balance'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create withdrawal transaction
            $transaction = WalletTransaction::create([
                'user_id' => $driver->id,
                'amount' => $request->amount,
                'payment_type' => 'bank_transfer',
                'transaction_id' => 'WD_' . time() . '_' . $driver->id,
                'order_type' => null,
                'user_type' => 'driver',
                'type' => 'debit',
                'note' => 'Withdrawal request - Bank: ' . $request->bank_details['bank_name']
            ]);

            // Deduct from wallet
            $driver->decrement('wallet_amount', $request->amount);

            // Here you would integrate with your payment gateway to process the withdrawal
            // For now, we'll just create the transaction record

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully',
                'data' => [
                    'transaction' => $transaction,
                    'new_balance' => $driver->fresh()->wallet_amount,
                    'status' => 'pending' // Withdrawal is pending approval
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process withdrawal: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get transaction details
    public function getTransactionDetails($transactionId)
    {
        $transaction = WalletTransaction::find($transactionId);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        // Check if user owns this transaction
        $user = request()->user();
        if ($transaction->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
    }
}

