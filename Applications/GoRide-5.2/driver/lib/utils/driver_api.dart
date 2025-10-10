import 'dart:developer';
import 'package:driver/utils/api_service.dart';

class DriverApi {
  /// Register or update driver
  static Future<Map<String, dynamic>> register({
    required String uid,
    required String fullName,
    required String email,
    required String phone,
    required String countryCode,
    String? profilePic,
    String? fcmToken,
  }) async {
    try {
      final response = await ApiService.post('/api/driver/register', body: {
        'uid': uid,
        'full_name': fullName,
        'email': email,
        'phone': phone,
        'country_code': countryCode,
        'profile_pic': profilePic,
        'fcm_token': fcmToken,
      });

      return response;
    } catch (e) {
      log('❌ Driver Register Error: $e');
      rethrow;
    }
  }

  /// Get driver profile
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

  /// Update driver profile
  static Future<Map<String, dynamic>> updateProfile({
    required String uid,
    String? fullName,
    String? email,
    String? phone,
    String? countryCode,
    String? profilePic,
    String? vehicleNumber,
    String? vehicleType,
  }) async {
    try {
      final body = <String, dynamic>{'uid': uid};

      if (fullName != null) body['full_name'] = fullName;
      if (email != null) body['email'] = email;
      if (phone != null) body['phone'] = phone;
      if (countryCode != null) body['country_code'] = countryCode;
      if (profilePic != null) body['profile_pic'] = profilePic;
      if (vehicleNumber != null) body['vehicle_number'] = vehicleNumber;
      if (vehicleType != null) body['vehicle_type'] = vehicleType;

      final response = await ApiService.put('/api/driver/profile', body: body);

      return response;
    } catch (e) {
      log('❌ Update Driver Profile Error: $e');
      rethrow;
    }
  }

  /// Update driver location
  static Future<Map<String, dynamic>> updateLocation({
    required String uid,
    required double latitude,
    required double longitude,
    double? rotation,
  }) async {
    try {
      final response = await ApiService.post('/api/driver/update-location', body: {
        'uid': uid,
        'latitude': latitude,
        'longitude': longitude,
        'rotation': rotation ?? 0.0,
      });

      return response;
    } catch (e) {
      log('❌ Update Location Error: $e');
      rethrow;
    }
  }

  /// Update driver online status
  static Future<Map<String, dynamic>> updateStatus({
    required String uid,
    required bool isOnline,
  }) async {
    try {
      final response = await ApiService.post('/api/driver/update-status', body: {
        'uid': uid,
        'is_online': isOnline,
      });

      return response;
    } catch (e) {
      log('❌ Update Status Error: $e');
      rethrow;
    }
  }

  /// Update FCM token
  static Future<Map<String, dynamic>> updateFcmToken({
    required String uid,
    required String fcmToken,
  }) async {
    try {
      final response = await ApiService.post('/api/driver/update-fcm', body: {
        'uid': uid,
        'fcm_token': fcmToken,
      });

      return response;
    } catch (e) {
      log('❌ Update FCM Token Error: $e');
      rethrow;
    }
  }

  /// Check driver approval status
  static Future<Map<String, dynamic>> checkStatus(String uid) async {
    try {
      final response = await ApiService.get('/api/driver/check-status', queryParams: {
        'uid': uid,
      });

      return response;
    } catch (e) {
      log('❌ Check Driver Status Error: $e');
      rethrow;
    }
  }

  /// Upload driver avatar
  static Future<Map<String, dynamic>> uploadAvatar({
    required String uid,
    required String imagePath,
  }) async {
    try {
      final response = await ApiService.uploadFile(
        '/api/driver/upload-avatar',
        fields: {'uid': uid},
        files: {'avatar': imagePath},
      );

      return response;
    } catch (e) {
      log('❌ Upload Avatar Error: $e');
      rethrow;
    }
  }

  /// Upload driver document
  static Future<Map<String, dynamic>> uploadDocument({
    required int driverId,
    required String documentType,
    required String documentPath,
  }) async {
    try {
      final response = await ApiService.uploadFile(
        '/api/driver/upload-document',
        fields: {
          'driver_id': driverId.toString(),
          'document_type': documentType,
        },
        files: {'document': documentPath},
      );

      return response;
    } catch (e) {
      log('❌ Upload Document Error: $e');
      rethrow;
    }
  }

  /// Get driver documents
  static Future<Map<String, dynamic>> getDocuments(int driverId) async {
    try {
      final response = await ApiService.get('/api/driver/documents', queryParams: {
        'driver_id': driverId.toString(),
      });

      return response;
    } catch (e) {
      log('❌ Get Documents Error: $e');
      rethrow;
    }
  }

  /// Delete driver document
  static Future<Map<String, dynamic>> deleteDocument(int documentId) async {
    try {
      final response = await ApiService.delete('/api/driver/document/$documentId');

      return response;
    } catch (e) {
      log('❌ Delete Document Error: $e');
      rethrow;
    }
  }
}

