import 'dart:developer';
import 'package:customer/services/api_service.dart';

class ZoneApi {
  /// Get all zones
  static Future<Map<String, dynamic>> getAllZones() async {
    try {
      final response = await ApiService.get('/api/zones');
      return response;
    } catch (e) {
      log('❌ Get All Zones Error: $e');
      rethrow;
    }
  }

  /// Get zone by ID
  static Future<Map<String, dynamic>> getZoneById(int zoneId) async {
    try {
      final response = await ApiService.get('/api/zones/$zoneId');
      return response;
    } catch (e) {
      log('❌ Get Zone By ID Error: $e');
      rethrow;
    }
  }
}

