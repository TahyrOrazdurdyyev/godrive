import 'dart:developer';
import 'package:driver/utils/api_service.dart';

class VehicleTypeApi {
  /// Get all active vehicle types
  static Future<Map<String, dynamic>> getAllVehicleTypes() async {
    try {
      final response = await ApiService.get('/api/v1/vehicle-types');
      return response;
    } catch (e) {
      log('❌ Get Vehicle Types Error: $e');
      rethrow;
    }
  }

  /// Get vehicle type by ID
  static Future<Map<String, dynamic>> getVehicleTypeById(int vehicleTypeId) async {
    try {
      final response = await ApiService.get('/api/v1/vehicle-types/$vehicleTypeId');
      return response;
    } catch (e) {
      log('❌ Get Vehicle Type Error: $e');
      rethrow;
    }
  }
}

