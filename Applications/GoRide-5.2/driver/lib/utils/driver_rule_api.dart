import 'dart:developer';
import 'package:driver/utils/api_service.dart';

class DriverRuleApi {
  /// Get all active driver rules
  static Future<Map<String, dynamic>> getAllDriverRules() async {
    try {
      final response = await ApiService.get('/api/v1/driver-rules');
      return response;
    } catch (e) {
      log('❌ Get Driver Rules Error: $e');
      rethrow;
    }
  }

  /// Get driver rule by ID
  static Future<Map<String, dynamic>> getDriverRuleById(int ruleId) async {
    try {
      final response = await ApiService.get('/api/v1/driver-rules/$ruleId');
      return response;
    } catch (e) {
      log('❌ Get Driver Rule Error: $e');
      rethrow;
    }
  }
}

