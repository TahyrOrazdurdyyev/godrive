import 'dart:developer';
import 'package:customer/services/api_service.dart';

class SosApi {
  /// Get SOS by order ID
  static Future<Map<String, dynamic>> getByOrder({
    required String orderId,
    required String orderType, // 'city' or 'intercity'
  }) async {
    try {
      final response = await ApiService.get('/api/sos/order/$orderId', queryParams: {
        'order_type': orderType,
      });
      return response;
    } catch (e) {
      log('❌ Get SOS Error: $e');
      rethrow;
    }
  }

  /// Get user's SOS history
  static Future<Map<String, dynamic>> getUserSos(int userId) async {
    try {
      final response = await ApiService.get('/api/sos/user/$userId');
      return response;
    } catch (e) {
      log('❌ Get User SOS Error: $e');
      rethrow;
    }
  }

  /// Create SOS
  static Future<Map<String, dynamic>> create({
    required int userId,
    required String orderType, // 'city' or 'intercity'
    String? orderId,
    String? intercityOrderId,
    int? driverId,
    String? status,
    double? latitude,
    double? longitude,
    String? notes,
  }) async {
    try {
      final body = {
        'user_id': userId,
        'order_type': orderType,
      };

      if (orderType == 'city' && orderId != null) {
        body['order_id'] = orderId;
      } else if (orderType == 'intercity' && intercityOrderId != null) {
        body['intercity_order_id'] = intercityOrderId;
      }

      if (driverId != null) body['driver_id'] = driverId;
      if (status != null) body['status'] = status;
      if (latitude != null) body['latitude'] = latitude;
      if (longitude != null) body['longitude'] = longitude;
      if (notes != null) body['notes'] = notes;

      final response = await ApiService.post('/api/sos', body: body);
      return response;
    } catch (e) {
      log('❌ Create SOS Error: $e');
      rethrow;
    }
  }

  /// Update SOS status
  static Future<Map<String, dynamic>> update({
    required String sosId,
    required String status,
    String? notes,
  }) async {
    try {
      final body = {
        'status': status,
      };

      if (notes != null) body['notes'] = notes;

      final response = await ApiService.put('/api/sos/$sosId', body: body);
      return response;
    } catch (e) {
      log('❌ Update SOS Error: $e');
      rethrow;
    }
  }
}

