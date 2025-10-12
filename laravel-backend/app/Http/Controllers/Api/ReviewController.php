<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Get review by order ID
     */
    public function getReviewByOrder(Request $request)
    {
        try {
            $orderId = $request->query('order_id');

            if (!$orderId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order ID is required'
                ], 400);
            }

            $review = DB::connection('mysql_main')
                ->table('reviews')
                ->where('order_id', $orderId)
                ->first();

            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'Review not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'review' => $review
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get reviews for a driver
     */
    public function getDriverReviews(Request $request)
    {
        try {
            $driverId = $request->query('driver_id');

            if (!$driverId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver ID is required'
                ], 400);
            }

            $reviews = DB::connection('mysql_main')
                ->table('reviews')
                ->where('driver_id', $driverId)
                ->orderBy('created_at', 'desc')
                ->get();

            $avgRating = DB::connection('mysql_main')
                ->table('reviews')
                ->where('driver_id', $driverId)
                ->avg('rating');

            return response()->json([
                'success' => true,
                'reviews' => $reviews,
                'average_rating' => round($avgRating, 1),
                'total_reviews' => count($reviews)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a review
     */
    public function createReview(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'driver_id' => 'required|integer',
                'order_id' => 'required|integer',
                'rating' => 'required|numeric|min:0|max:5',
                'comment' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if review already exists for this order
            $existingReview = DB::connection('mysql_main')
                ->table('reviews')
                ->where('order_id', $request->order_id)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'success' => false,
                    'message' => 'Review already exists for this order'
                ], 409);
            }

            $reviewId = DB::connection('mysql_main')
                ->table('reviews')
                ->insertGetId([
                    'user_id' => $request->user_id,
                    'driver_id' => $request->driver_id,
                    'order_id' => $request->order_id,
                    'rating' => $request->rating,
                    'comment' => $request->comment,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            // Update driver's average rating
            $this->updateDriverRating($request->driver_id);

            return response()->json([
                'success' => true,
                'message' => 'Review created successfully',
                'review_id' => $reviewId
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update driver's average rating
     */
    private function updateDriverRating($driverId)
    {
        try {
            $avgRating = DB::connection('mysql_main')
                ->table('reviews')
                ->where('driver_id', $driverId)
                ->avg('rating');

            $reviewsCount = DB::connection('mysql_main')
                ->table('reviews')
                ->where('driver_id', $driverId)
                ->count();

            DB::connection('mysql_main')
                ->table('drivers')
                ->where('id', $driverId)
                ->update([
                    'reviews_sum' => round($avgRating, 2),
                    'reviews_count' => $reviewsCount,
                    'updated_at' => now(),
                ]);

        } catch (\Exception $e) {
            \Log::error('Failed to update driver rating: ' . $e->getMessage());
        }
    }
}
