import 'dart:developer';
import 'package:driver/utils/api_service.dart';

class OrderBidApi {
  /// Get all bids for an order
  static Future<Map<String, dynamic>> getOrderBids({
    required String orderId,
    String orderType = 'city',
  }) async {
    try {
      final response = await ApiService.get(
        '/api/orders/$orderId/bids',
        queryParams: {'order_type': orderType},
      );
      return response;
    } catch (e) {
      log('❌ Get Order Bids Error: $e');
      rethrow;
    }
  }

  /// Get accepted bids for an order
  static Future<Map<String, dynamic>> getAcceptedBids({
    required String orderId,
    String orderType = 'city',
  }) async {
    try {
      final response = await ApiService.get(
        '/api/orders/$orderId/bids/accepted',
        queryParams: {'order_type': orderType},
      );
      return response;
    } catch (e) {
      log('❌ Get Accepted Bids Error: $e');
      rethrow;
    }
  }

  /// Get specific bid
  static Future<Map<String, dynamic>> getBid({
    required String orderId,
    required String driverId,
    String orderType = 'city',
  }) async {
    try {
      final response = await ApiService.get(
        '/api/orders/$orderId/bids/$driverId',
        queryParams: {'order_type': orderType},
      );
      return response;
    } catch (e) {
      log('❌ Get Bid Error: $e');
      rethrow;
    }
  }

  /// Create or update bid
  static Future<Map<String, dynamic>> createOrUpdateBid({
    required String orderId,
    required String driverId,
    String status = 'pending',
    double? offerAmount,
    String? driverNote,
    String orderType = 'city',
  }) async {
    try {
      final response = await ApiService.post(
        '/api/orders/$orderId/bids',
        queryParams: {'order_type': orderType},
        body: {
          'driver_id': driverId,
          'status': status,
          if (offerAmount != null) 'offer_amount': offerAmount,
          if (driverNote != null) 'driver_note': driverNote,
        },
      );
      return response;
    } catch (e) {
      log('❌ Create/Update Bid Error: $e');
      rethrow;
    }
  }
}

