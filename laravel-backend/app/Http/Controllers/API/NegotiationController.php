<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NegotiationController extends Controller
{
    /**
     * Customer accepts driver's counter offer
     */
    public function acceptCounterOffer(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Verify customer owns this order
        if ($order->user_id != $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        // Update order
        $order->final_rate = $order->driver_counter_price;
        $order->negotiation_status = 'accepted';
        
        // Add to history
        $history = $order->price_negotiation_history ?? [];
        $history[] = [
            'type' => 'customer_accepted_counter',
            'price' => $order->driver_counter_price,
            'timestamp' => now()->toISOString()
        ];
        $order->price_negotiation_history = $history;
        
        $order->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Counter offer accepted',
            'data' => $order
        ]);
    }
    
    /**
     * Customer rejects driver's counter offer
     */
    public function rejectCounterOffer(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Verify customer owns this order
        if ($order->user_id != $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        // Update order
        $order->negotiation_status = 'rejected';
        $order->status = 'cancelled';
        
        // Add to history
        $history = $order->price_negotiation_history ?? [];
        $history[] = [
            'type' => 'customer_rejected_counter',
            'price' => $order->driver_counter_price,
            'timestamp' => now()->toISOString()
        ];
        $order->price_negotiation_history = $history;
        
        $order->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Counter offer rejected',
            'data' => $order
        ]);
    }
    
    /**
     * Driver accepts customer's offer
     */
    public function driverAcceptOffer(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Update order
        $order->driver_id = $request->user()->id;
        $order->final_rate = $order->customer_offer_price;
        $order->negotiation_status = 'accepted';
        $order->status = 'accepted';
        
        // Add to history
        $history = $order->price_negotiation_history ?? [];
        $history[] = [
            'type' => 'driver_accepted',
            'driver_id' => $request->user()->id,
            'price' => $order->customer_offer_price,
            'timestamp' => now()->toISOString()
        ];
        $order->price_negotiation_history = $history;
        
        $order->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Order accepted',
            'data' => $order
        ]);
    }
    
    /**
     * Driver makes counter offer
     */
    public function driverCounterOffer(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'counter_price' => 'required|numeric|min:0'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $order = Order::findOrFail($orderId);
        
        // Update order
        $order->driver_counter_price = $request->counter_price;
        $order->negotiation_status = 'countered';
        
        // Add to history
        $history = $order->price_negotiation_history ?? [];
        $history[] = [
            'type' => 'driver_counter',
            'driver_id' => $request->user()->id,
            'original_price' => $order->customer_offer_price,
            'counter_price' => $request->counter_price,
            'timestamp' => now()->toISOString()
        ];
        $order->price_negotiation_history = $history;
        
        $order->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Counter offer sent',
            'data' => $order
        ]);
    }
}
