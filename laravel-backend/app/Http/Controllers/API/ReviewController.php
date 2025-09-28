<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function createReview(Request $request, $orderId)
    {
        return response()->json(['success' => true, 'message' => 'Review created']);
    }

    public function getUserReviews()
    {
        return response()->json(['success' => true, 'data' => []]);
    }

    public function getDriverReviews()
    {
        return response()->json(['success' => true, 'data' => []]);
    }
}
