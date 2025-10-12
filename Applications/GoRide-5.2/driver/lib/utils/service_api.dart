import 'dart:developer';
import 'package:driver/utils/api_service.dart';

class ServiceApi {
  /// Get all active services
  static Future<Map<String, dynamic>> getAllServices() async {
    try {
      final response = await ApiService.get('/api/services');

      return response;
    } catch (e) {
      log('❌ Get All Services Error: $e');
      rethrow;
    }
  }

  /// Get service by ID
  static Future<Map<String, dynamic>> getServiceById(int serviceId) async {
    try {
      final response = await ApiService.get('/api/services/$serviceId');

      return response;
    } catch (e) {
      log('❌ Get Service Error: $e');
      rethrow;
    }
  }

  /// Get intercity services
  static Future<Map<String, dynamic>> getIntercityServices() async {
    try {
      final response = await ApiService.get('/api/services/intercity');

      return response;
    } catch (e) {
      log('❌ Get Intercity Services Error: $e');
      rethrow;
    }
  }
}

