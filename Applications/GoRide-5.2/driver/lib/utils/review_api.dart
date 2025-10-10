import 'dart:developer';
import 'package:driver/utils/api_service.dart';

class ReviewApi {
  /// Get review by order ID
  static Future<Map<String, dynamic>> getReviewByOrder(String orderId, {String orderType = 'city'}) async {
    try {
      final response = await ApiService.get(
        '/api/reviews/order/$orderId',
        queryParams: {'order_type': orderType},
      );
      return response;
    } catch (e) {
      log('❌ Get Review Error: $e');
      rethrow;
    }
  }

  /// Get customer reviews
  static Future<Map<String, dynamic>> getCustomerReviews(String customerId) async {
    try {
      final response = await ApiService.get('/api/reviews/customer/$customerId');
      return response;
    } catch (e) {
      log('❌ Get Customer Reviews Error: $e');
      rethrow;
    }
  }

  /// Create review for customer (driver reviews customer)
  static Future<Map<String, dynamic>> createReview({
    required String orderId,
    required String orderType,
    required String driverId,
    required String customerId,
    required double rating,
    String? comment,
  }) async {
    try {
      final response = await ApiService.post('/api/reviews', body: {
        'order_id': orderId,
        'order_type': orderType,
        'driver_id': driverId,
        'customer_id': customerId,
        'rating': rating,
        if (comment != null && comment.isNotEmpty) 'comment': comment,
      });
      return response;
    } catch (e) {
      log('❌ Create Review Error: $e');
      rethrow;
    }
  }
}

