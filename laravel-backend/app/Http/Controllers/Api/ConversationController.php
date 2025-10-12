<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConversationController extends Controller
{
    /**
     * Get messages for an order
     * GET /api/conversations?order_id={id}
     */
    public function getMessages(Request $request)
    {
        try {
            $orderId = $request->input('order_id');

            if (!$orderId) {
                return response()->json([
                    'success' => false,
                    'message' => 'order_id is required'
                ], 400);
            }

            $messages = DB::connection('mysql_main')->table('conversations')
                ->where('order_id', $orderId)
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'messages' => $messages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a message
     * POST /api/conversations
     */
    public function sendMessage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|integer',
                'customer_id' => 'required|integer',
                'driver_id' => 'required|integer',
                'sender_type' => 'required|string|in:customer,driver',
                'message_type' => 'required|string|in:text,image,video',
                'message' => 'required|string',
                'file_url' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $messageId = DB::connection('mysql_main')->table('conversations')->insertGetId([
                'order_id' => $request->order_id,
                'customer_id' => $request->customer_id,
                'driver_id' => $request->driver_id,
                'sender_type' => $request->sender_type,
                'message_type' => $request->message_type,
                'message' => $request->message,
                'file_url' => $request->file_url,
                'is_read' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $message = DB::connection('mysql_main')->table('conversations')
                ->where('id', $messageId)
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark messages as read
     * PUT /api/conversations/read
     */
    public function markAsRead(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|integer',
                'user_type' => 'required|string|in:customer,driver',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            // Mark messages as read (sent by the other party)
            $senderType = $request->user_type === 'customer' ? 'driver' : 'customer';

            DB::connection('mysql_main')->table('conversations')
                ->where('order_id', $request->order_id)
                ->where('sender_type', $senderType)
                ->update([
                    'is_read' => 1,
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Messages marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread message count
     * GET /api/conversations/unread?order_id={id}&user_type={customer|driver}
     */
    public function getUnreadCount(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $userType = $request->input('user_type');

            if (!$orderId || !$userType) {
                return response()->json([
                    'success' => false,
                    'message' => 'order_id and user_type are required'
                ], 400);
            }

            // Count unread messages sent by the other party
            $senderType = $userType === 'customer' ? 'driver' : 'customer';

            $count = DB::connection('mysql_main')->table('conversations')
                ->where('order_id', $orderId)
                ->where('sender_type', $senderType)
                ->where('is_read', 0)
                ->count();

            return response()->json([
                'success' => true,
                'unread_count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
