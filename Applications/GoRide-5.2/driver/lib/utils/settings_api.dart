import 'dart:developer';
import 'package:driver/utils/api_service.dart';

class SettingsApi {
  /// Get application settings
  static Future<Map<String, dynamic>> getSettings() async {
    try {
      final response = await ApiService.get('/api/settings');
      return response;
    } catch (e) {
      log('❌ Get Settings Error: $e');
      rethrow;
    }
  }
}

