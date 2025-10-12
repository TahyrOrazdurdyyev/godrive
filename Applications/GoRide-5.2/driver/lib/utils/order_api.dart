import 'dart:developer';
import 'package:driver/utils/api_service.dart';

class OrderApi {
  /// Get nearby pending orders for drivers
  static Future<Map<String, dynamic>> getNearbyOrders({
    required double latitude,
    required double longitude,
    double radius = 10.0, // Default 10km
  }) async {
    try {
      final response = await ApiService.get('/api/orders/nearby', queryParams: {
        'latitude': latitude.toString(),
        'longitude': longitude.toString(),
        'radius': radius.toString(),
      });

      return response;
    } catch (e) {
      log('❌ Get Nearby Orders Error: $e');
      rethrow;
    }
  }

  /// Get driver orders
  static Future<Map<String, dynamic>> getDriverOrders(int driverId) async {
    try {
      final response = await ApiService.get('/api/orders/driver', queryParams: {
        'driver_id': driverId.toString(),
      });

      return response;
    } catch (e) {
      log('❌ Get Driver Orders Error: $e');
      rethrow;
    }
  }

  /// Get order by ID
  static Future<Map<String, dynamic>> getOrderById(int orderId) async {
    try {
      final response = await ApiService.get('/api/orders/$orderId');

      return response;
    } catch (e) {
      log('❌ Get Order Error: $e');
      rethrow;
    }
  }

  /// Accept order (driver)
  static Future<Map<String, dynamic>> acceptOrder({
    required int orderId,
    required int driverId,
  }) async {
    try {
      final response = await ApiService.post('/api/orders/$orderId/accept', body: {
        'driver_id': driverId,
      });

      return response;
    } catch (e) {
      log('❌ Accept Order Error: $e');
      rethrow;
    }
  }

  /// Update order status
  static Future<Map<String, dynamic>> updateOrderStatus({
    required int orderId,
    required String status, // pending, accepted, arrived, started, completed, cancelled
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

  /// Cancel order
  static Future<Map<String, dynamic>> cancelOrder({
    required int orderId,
    required String cancelledBy, // customer or driver
    String? cancellationReason,
  }) async {
    try {
      final response = await ApiService.post('/api/orders/$orderId/cancel', body: {
        'cancelled_by': cancelledBy,
        'cancellation_reason': cancellationReason,
      });

      return response;
    } catch (e) {
      log('❌ Cancel Order Error: $e');
      rethrow;
    }
  }

  /// Update order (generic update)
  static Future<Map<String, dynamic>> updateOrder({
    required int orderId,
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

