import 'dart:developer';
import 'package:customer/services/api_service.dart';

class ServiceApi {
  /// Get all services
  static Future<Map<String, dynamic>> getServices() async {
    try {
      final response = await ApiService.get('/api/v1/services');
      return response;
    } catch (e) {
      log('❌ Get Services Error: $e');
      rethrow;
    }
  }

  /// Get city services
  static Future<Map<String, dynamic>> getCityServices() async {
    try {
      final response = await ApiService.get('/api/v1/services/city');
      return response;
    } catch (e) {
      log('❌ Get City Services Error: $e');
      rethrow;
    }
  }

  /// Get intercity services
  static Future<Map<String, dynamic>> getIntercityServices() async {
    try {
      final response = await ApiService.get('/api/v1/services/intercity');
      return response;
    } catch (e) {
      log('❌ Get Intercity Services Error: $e');
      rethrow;
    }
  }

  /// Get service by ID
  static Future<Map<String, dynamic>> getService(String id) async {
    try {
      final response = await ApiService.get('/api/v1/services/$id');
      return response;
    } catch (e) {
      log('❌ Get Service Error: $e');
      rethrow;
    }
  }

  /// Get banners
  static Future<Map<String, dynamic>> getBanners() async {
    try {
      final response = await ApiService.get('/api/v1/banners');
      return response;
    } catch (e) {
      log('❌ Get Banners Error: $e');
      rethrow;
    }
  }

  /// Get zones
  static Future<Map<String, dynamic>> getZones() async {
    try {
      final response = await ApiService.get('/api/v1/zones');
      return response;
    } catch (e) {
      log('❌ Get Zones Error: $e');
      rethrow;
    }
  }
}
