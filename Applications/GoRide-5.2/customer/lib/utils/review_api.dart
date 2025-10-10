import 'dart:developer';
import 'package:customer/services/api_service.dart';

class ReviewApi {
  /// Get review by order ID
  static Future<Map<String, dynamic>> getReviewByOrder(String orderId) async {
    try {
      final response = await ApiService.get('/api/reviews/order', queryParams: {
        'order_id': orderId,
      });
      return response;
    } catch (e) {
      log('❌ Get Review By Order Error: $e');
      rethrow;
    }
  }

  /// Get driver reviews
  static Future<Map<String, dynamic>> getDriverReviews(int driverId) async {
    try {
      final response = await ApiService.get('/api/reviews/driver', queryParams: {
        'driver_id': driverId.toString(),
      });
      return response;
    } catch (e) {
      log('❌ Get Driver Reviews Error: $e');
      rethrow;
    }
  }

  /// Create review
  static Future<Map<String, dynamic>> createReview({
    required int userId,
    required int driverId,
    required int orderId,
    required double rating,
    String? comment,
  }) async {
    try {
      final response = await ApiService.post('/api/reviews', body: {
        'user_id': userId,
        'driver_id': driverId,
        'order_id': orderId,
        'rating': rating,
        'comment': comment,
      });
      return response;
    } catch (e) {
      log('❌ Create Review Error: $e');
      rethrow;
    }
  }
}

