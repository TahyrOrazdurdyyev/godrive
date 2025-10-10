import 'dart:developer';
import 'package:customer/services/api_service.dart';

class WalletApi {
  /// Get customer wallet balance
  static Future<Map<String, dynamic>> getCustomerBalance(int userId) async {
    try {
      final response = await ApiService.get('/api/wallet/customer', queryParams: {
        'user_id': userId.toString(),
      });
      return response;
    } catch (e) {
      log('❌ Get Customer Balance Error: $e');
      rethrow;
    }
  }

  /// Get wallet transactions
  static Future<Map<String, dynamic>> getTransactions({
    required String userType,
    required int userId,
  }) async {
    try {
      final response = await ApiService.get('/api/wallet/transactions', queryParams: {
        'user_type': userType, // 'customer' or 'driver'
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
    required String userType,
    required int userId,
    required double amount,
    String? paymentMethod,
    String? transactionId,
    String? note,
  }) async {
    try {
      final body = {
        'user_type': userType,
        'user_id': userId,
        'amount': amount,
      };

      if (paymentMethod != null) body['payment_method'] = paymentMethod;
      if (transactionId != null) body['transaction_id'] = transactionId;
      if (note != null) body['note'] = note;

      final response = await ApiService.post('/api/wallet/add', body: body);
      return response;
    } catch (e) {
      log('❌ Add Money Error: $e');
      rethrow;
    }
  }

  /// Withdraw money from wallet
  static Future<Map<String, dynamic>> withdrawMoney({
    required String userType,
    required int userId,
    required double amount,
    String? note,
  }) async {
    try {
      final response = await ApiService.post('/api/wallet/withdraw', body: {
        'user_type': userType,
        'user_id': userId,
        'amount': amount,
        'note': note,
      });
      return response;
    } catch (e) {
      log('❌ Withdraw Money Error: $e');
      rethrow;
    }
  }
}

