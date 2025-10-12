import 'dart:developer';
import 'package:customer/services/api_service.dart';

class DriverApi {
  /// Get driver profile by firebase_uid
  static Future<Map<String, dynamic>> getProfile(String uid) async {
    try {
      final response = await ApiService.get('/api/driver/profile', queryParams: {
        'uid': uid,
      });

      return response;
    } catch (e) {
      log('❌ Get Driver Profile Error: $e');
      rethrow;
    }
  }

  /// Get driver by ID
  static Future<Map<String, dynamic>> getDriverById(int driverId) async {
    try {
      final response = await ApiService.get('/api/driver/$driverId');

      return response;
    } catch (e) {
      log('❌ Get Driver By ID Error: $e');
      rethrow;
    }
  }
}

