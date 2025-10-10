import 'dart:developer';
import 'package:driver/utils/api_service.dart';

class WalletApi {
  /// Get driver wallet balance
  static Future<Map<String, dynamic>> getDriverBalance(int driverId) async {
    try {
      final response = await ApiService.get('/api/wallet/driver', queryParams: {
        'driver_id': driverId.toString(),
      });

      return response;
    } catch (e) {
      log('❌ Get Driver Balance Error: $e');
      rethrow;
    }
  }

  /// Get wallet transactions
  static Future<Map<String, dynamic>> getTransactions({
    required String userType, // driver or customer
    required int userId,
  }) async {
    try {
      final response = await ApiService.get('/api/wallet/transactions', queryParams: {
        'user_type': userType,
        'user_id': userId.toString(),
      });

      return response;
    } catch (e) {
      log('❌ Get Transactions Error: $e');
      rethrow;
    }
  }

  /// Add money to wallet
  static Future<Map<String, dynamic>> addMoney({
    required String userType, // driver or customer
    required int userId,
    required double amount,
    required String paymentMethod,
    String? transactionId,
  }) async {
    try {
      final response = await ApiService.post('/api/wallet/add', body: {
        'user_type': userType,
        'user_id': userId,
        'amount': amount,
        'payment_method': paymentMethod,
        'transaction_id': transactionId,
      });

      return response;
    } catch (e) {
      log('❌ Add Money Error: $e');
      rethrow;
    }
  }

  /// Withdraw money (driver only)
  static Future<Map<String, dynamic>> withdrawMoney({
    required int driverId,
    required double amount,
    required String paymentMethod,
    String? note,
  }) async {
    try {
      final response = await ApiService.post('/api/wallet/withdraw', body: {
        'driver_id': driverId,
        'amount': amount,
        'payment_method': paymentMethod,
        'note': note,
      });

      return response;
    } catch (e) {
      log('❌ Withdraw Money Error: $e');
      rethrow;
    }
  }
}

