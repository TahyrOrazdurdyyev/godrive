import 'dart:developer';
import 'api_service.dart';

class DriverWalletApi {
  /// Get driver wallet balance
  static Future<Map<String, dynamic>> getBalance(String driverId) async {
    try {
      final response = await ApiService.get('/api/wallet/driver?driver_id=$driverId');
      return response;
    } catch (e) {
      log('❌ Get Driver Balance Error: $e');
      rethrow;
    }
  }

  /// Add transaction to driver wallet
  static Future<Map<String, dynamic>> addTransaction({
    required String driverId,
    required double amount,
    required String orderId,
    required String orderType,
    String? note,
    String? paymentType,
  }) async {
    try {
      final response = await ApiService.post(
        '/api/wallet/driver/transaction',
        body: {
          'driver_id': driverId,
          'amount': amount,
          'order_id': orderId,
          'order_type': orderType,
          'note': note,
          'payment_type': paymentType,
        },
      );
      return response;
    } catch (e) {
      log('❌ Add Transaction Error: $e');
      rethrow;
    }
  }

  /// Get driver transactions
  static Future<Map<String, dynamic>> getTransactions(String driverId) async {
    try {
      final response = await ApiService.get('/api/wallet/driver/transactions?driver_id=$driverId');
      return response;
    } catch (e) {
      log('❌ Get Transactions Error: $e');
      rethrow;
    }
  }
}

