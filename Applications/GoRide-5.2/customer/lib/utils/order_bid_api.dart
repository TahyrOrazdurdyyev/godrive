import 'dart:developer';
import 'package:customer/services/api_service.dart';

class OrderBidApi {
  /// Get all bids for an order
  static Future<Map<String, dynamic>> getOrderBids(String orderId) async {
    try {
      final response = await ApiService.get('/api/orders/$orderId/bids');
      return response;
    } catch (e) {
      log('❌ Get Order Bids Error: $e');
      rethrow;
    }
  }

  /// Get accepted bids for an order
  static Future<Map<String, dynamic>> getAcceptedBids(String orderId) async {
    try {
      final response = await ApiService.get('/api/orders/$orderId/bids/accepted');
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
  }) async {
    try {
      final response = await ApiService.get('/api/orders/$orderId/bids/$driverId');
      return response;
    } catch (e) {
      log('❌ Get Bid Error: $e');
      rethrow;
    }
  }

  /// Create or update bid
  static Future<Map<String, dynamic>> createOrUpdateBid({
    required String orderId,
    required int driverId,
    String? status,
    double? offerAmount,
    String? driverNote,
  }) async {
    try {
      final body = {
        'driver_id': driverId,
      };

      if (status != null) body['status'] = status;
      if (offerAmount != null) body['offer_amount'] = offerAmount;
      if (driverNote != null) body['driver_note'] = driverNote;

      final response = await ApiService.post('/api/orders/$orderId/bids', body: body);
      return response;
    } catch (e) {
      log('❌ Create/Update Bid Error: $e');
      rethrow;
    }
  }
}

