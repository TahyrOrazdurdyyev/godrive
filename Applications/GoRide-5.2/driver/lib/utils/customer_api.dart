import 'dart:developer';
import 'package:driver/utils/api_service.dart';

class CustomerApi {
  /// Get customer profile by Firebase UID
  static Future<Map<String, dynamic>> getProfile({required String firebaseUid}) async {
    try {
      final response = await ApiService.get('/api/customer/profile', queryParams: {
        'uid': firebaseUid,
      });
      return response;
    } catch (e) {
      log('❌ Get Customer Profile Error: $e');
      rethrow;
    }
  }

  /// Get customer by user ID (from orders)
  static Future<Map<String, dynamic>> getCustomerByUserId(String userId) async {
    try {
      final response = await ApiService.get('/api/customer/profile', queryParams: {
        'uid': userId,
      });
      return response;
    } catch (e) {
      log('❌ Get Customer By User ID Error: $e');
      rethrow;
    }
  }

  /// Alias method for compatibility
  static Future<Map<String, dynamic>> getCustomerProfile(String userId) async {
    return await getCustomerByUserId(userId);
  }
}
