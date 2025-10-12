import 'dart:developer';
import 'package:driver/utils/api_service.dart';

class InterCityOrderApi {
  /// Get intercity order by ID
  static Future<Map<String, dynamic>> getById(String orderId) async {
    try {
      final response = await ApiService.get('/api/intercity-orders/$orderId');
      return response;
    } catch (e) {
      log('❌ Get InterCity Order Error: $e');
      rethrow;
    }
  }

  /// Get driver's intercity orders
  static Future<Map<String, dynamic>> getDriverOrders(String driverId) async {
    try {
      final response = await ApiService.get(
        '/api/intercity-orders/driver',
        queryParams: {'driver_id': driverId},
      );
      return response;
    } catch (e) {
      log('❌ Get Driver InterCity Orders Error: $e');
      rethrow;
    }
  }

  /// Update intercity order
  static Future<Map<String, dynamic>> update({
    required String orderId,
    required Map<String, dynamic> data,
  }) async {
    try {
      final response = await ApiService.put('/api/intercity-orders/$orderId', body: data);
      return response;
    } catch (e) {
      log('❌ Update InterCity Order Error: $e');
      rethrow;
    }
  }

  /// Cancel intercity order
  static Future<Map<String, dynamic>> cancel({
    required String orderId,
    String? reason,
  }) async {
    try {
      final response = await ApiService.post(
        '/api/intercity-orders/$orderId/cancel',
        body: {'reason': reason ?? 'Cancelled by driver'},
      );
      return response;
    } catch (e) {
      log('❌ Cancel InterCity Order Error: $e');
      rethrow;
    }
  }

  /// Search intercity orders for driver
  static Future<Map<String, dynamic>> searchForDriver({
    required int driverId,
    required String sourceCity,
    String? destinationCity,
    String? whenDate,
  }) async {
    try {
      final queryParams = {
        'driver_id': driverId.toString(),
        'source_city': sourceCity,
        if (destinationCity != null && destinationCity.isNotEmpty) 'destination_city': destinationCity,
        if (whenDate != null && whenDate.isNotEmpty) 'when_date': whenDate,
      };
      
      final response = await ApiService.get('/api/intercity-orders/search', queryParams: queryParams);
      return response;
    } catch (e) {
      log('❌ Search InterCity Orders Error: $e');
      rethrow;
    }
  }
}

