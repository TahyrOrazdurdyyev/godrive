import 'dart:developer';
import 'package:driver/utils/api_service.dart';

class ZoneApi {
  /// Get all active zones
  static Future<Map<String, dynamic>> getAllZones() async {
    try {
      final response = await ApiService.get('zones');
      return response;
    } catch (e) {
      log('❌ Get Zones Error: $e');
      rethrow;
    }
  }

  /// Get zone by ID
  static Future<Map<String, dynamic>> getZoneById(int zoneId) async {
    try {
      final response = await ApiService.get('zones/$zoneId');
      return response;
    } catch (e) {
      log('❌ Get Zone Error: $e');
      rethrow;
    }
  }
}

