<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function getOrderMessages($orderId)
    {
        return response()->json([
            'success' => true, 
            'data' => []
        ]);
    }

    public function sendMessage(Request $request, $orderId)
    {
        return response()->json([
            'success' => true, 
            'message' => 'Message sent'
        ]);
    }
}
