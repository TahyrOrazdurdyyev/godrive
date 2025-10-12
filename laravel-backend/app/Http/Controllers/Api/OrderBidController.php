<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderBidController extends Controller
{
    /**
     * Get bids for an order with driver details
     * GET /api/orders/{orderId}/bids?order_type=city|intercity
     */
    public function getOrderBids(Request $request, $orderId)
    {
        try {
            $orderType = $request->query('order_type', 'city');
            
            $query = DB::connection('mysql_main')
                ->table('order_bids')
                ->join('drivers', 'order_bids.driver_id', '=', 'drivers.id');
            
            if ($orderType === 'city') {
                $query->where('order_bids.order_id', $orderId);
            } else {
                $query->where('order_bids.intercity_order_id', $orderId);
            }
            
            $bids = $query->select(
                    'order_bids.*',
                    'drivers.full_name as driver_name',
                    'drivers.profile_pic as driver_profile_pic',
                    'drivers.phone as driver_phone',
                    'drivers.email as driver_email',
                    'drivers.reviews_sum',
                    'drivers.reviews_count'
                )
                ->orderBy('order_bids.created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'bids' => $bids
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get bids',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get accepted bids for an order
     * GET /api/orders/{orderId}/bids/accepted?order_type=city|intercity
     */
    public function getAcceptedBids(Request $request, $orderId)
    {
        try {
            $orderType = $request->query('order_type', 'city');
            
            $query = DB::connection('mysql_main')
                ->table('order_bids')
                ->join('drivers', 'order_bids.driver_id', '=', 'drivers.id');
            
            if ($orderType === 'city') {
                $query->where('order_bids.order_id', $orderId);
            } else {
                $query->where('order_bids.intercity_order_id', $orderId);
            }
            
            $bids = $query->where('order_bids.status', 'accepted')
                ->select(
                    'order_bids.*',
                    'drivers.full_name as driver_name',
                    'drivers.profile_pic as driver_profile_pic',
                    'drivers.phone as driver_phone',
                    'drivers.email as driver_email',
                    'drivers.reviews_sum',
                    'drivers.reviews_count'
                )
                ->orderBy('order_bids.created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'bids' => $bids
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get accepted bids',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific bid
     * GET /api/orders/{orderId}/bids/{driverId}?order_type=city|intercity
     */
    public function getBid(Request $request, $orderId, $driverId)
    {
        try {
            $orderType = $request->query('order_type', 'city');
            
            $query = DB::connection('mysql_main')
                ->table('order_bids')
                ->where('driver_id', $driverId);
            
            if ($orderType === 'city') {
                $query->where('order_id', $orderId);
            } else {
                $query->where('intercity_order_id', $orderId);
            }
            
            $bid = $query->first();

            if (!$bid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bid not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'bid' => $bid
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get bid',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create or update bid
     * POST /api/orders/{orderId}/bids?order_type=city|intercity
     */
    public function createOrUpdateBid(Request $request, $orderId)
    {
        try {
            $orderType = $request->query('order_type', $request->order_type ?? 'city');
            
            $validator = Validator::make($request->all(), [
                'driver_id' => 'required|integer',
                'status' => 'nullable|in:pending,accepted,rejected',
                'offer_amount' => 'nullable|numeric',
                'driver_note' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $whereClause = ['driver_id' => $request->driver_id];
            if ($orderType === 'city') {
                $whereClause['order_id'] = $orderId;
            } else {
                $whereClause['intercity_order_id'] = $orderId;
            }

            $existing = DB::connection('mysql_main')
                ->table('order_bids')
                ->where($whereClause)
                ->first();

            $data = [
                'status' => $request->status ?? 'pending',
                'offer_amount' => $request->offer_amount,
                'driver_note' => $request->driver_note,
                'updated_at' => now(),
            ];

            if ($existing) {
                DB::connection('mysql_main')
                    ->table('order_bids')
                    ->where($whereClause)
                    ->update($data);
            } else {
                $data['driver_id'] = $request->driver_id;
                if ($orderType === 'city') {
                    $data['order_id'] = $orderId;
                } else {
                    $data['intercity_order_id'] = $orderId;
                }
                $data['created_at'] = now();
                
                DB::connection('mysql_main')
                    ->table('order_bids')
                    ->insert($data);
            }

            return response()->json([
                'success' => true,
                'message' => 'Bid saved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save bid',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
