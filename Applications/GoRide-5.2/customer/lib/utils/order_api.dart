import 'dart:developer';
import 'package:customer/services/api_service.dart';

class OrderApi {
  /// Create new order
  static Future<Map<String, dynamic>> createOrder({
    required int userId,
    required int serviceId,
    required double sourceLat,
    required double sourceLng,
    required String sourceLocationName,
    required double destinationLat,
    required double destinationLng,
    required String destinationLocationName,
    required double distance,
    required String duration,
    required double offerRate,
    int? zoneId,
    int? couponId,
    String? paymentType,
    bool? isAcSelected,
    Map<String, dynamic>? someoneElseData,
  }) async {
    try {
      final body = {
        'user_id': userId,
        'service_id': serviceId,
        'source_lat': sourceLat,
        'source_lng': sourceLng,
        'source_location_name': sourceLocationName,
        'destination_lat': destinationLat,
        'destination_lng': destinationLng,
        'destination_location_name': destinationLocationName,
        'distance': distance,
        'duration': duration,
        'offer_rate': offerRate,
      };

      if (zoneId != null) body['zone_id'] = zoneId;
      if (couponId != null) body['coupon_id'] = couponId;
      if (paymentType != null) body['payment_type'] = paymentType;
      if (isAcSelected != null) body['is_ac_selected'] = isAcSelected;
      if (someoneElseData != null) body['someone_else_data'] = someoneElseData;

      final response = await ApiService.post('/api/orders', body: body);
      return response;
    } catch (e) {
      log('❌ Create Order Error: $e');
      rethrow;
    }
  }

  /// Get order by ID
  static Future<Map<String, dynamic>> getOrderById(String orderId) async {
    try {
      final response = await ApiService.get('/api/orders/$orderId');
      return response;
    } catch (e) {
      log('❌ Get Order Error: $e');
      rethrow;
    }
  }

  /// Get customer orders
  static Future<Map<String, dynamic>> getCustomerOrders(int userId) async {
    try {
      final response = await ApiService.get('/api/orders/customer', queryParams: {
        'user_id': userId.toString(),
      });
      return response;
    } catch (e) {
      log('❌ Get Customer Orders Error: $e');
      rethrow;
    }
  }

  /// Cancel order
  static Future<Map<String, dynamic>> cancelOrder({
    required String orderId,
    String? cancellationReason,
  }) async {
    try {
      final response = await ApiService.post('/api/orders/$orderId/cancel', body: {
        'cancelled_by': 'customer',
        'cancellation_reason': cancellationReason,
      });
      return response;
    } catch (e) {
      log('❌ Cancel Order Error: $e');
      rethrow;
    }
  }

  /// Update order status
  static Future<Map<String, dynamic>> updateOrderStatus({
    required String orderId,
    required String status,
  }) async {
    try {
      final response = await ApiService.put('/api/orders/$orderId/status', body: {
        'status': status,
      });
      return response;
    } catch (e) {
      log('❌ Update Order Status Error: $e');
      rethrow;
    }
  }

  /// Update order (generic update)
  static Future<Map<String, dynamic>> updateOrder({
    required String orderId,
    required Map<String, dynamic> data,
  }) async {
    try {
      final response = await ApiService.put('/api/orders/$orderId', body: data);
      return response;
    } catch (e) {
      log('❌ Update Order Error: $e');
      rethrow;
    }
  }
}

