import 'dart:developer';
import 'package:customer/services/api_service.dart';

class UserApi {
  /// Get user profile by firebase_uid
  static Future<Map<String, dynamic>> getProfile(String uid) async {
    try {
      final response = await ApiService.get('/api/user/profile', queryParams: {
        'uid': uid,
      });

      return response;
    } catch (e) {
      log('❌ Get User Profile Error: $e');
      rethrow;
    }
  }

  /// Register or update user
  static Future<Map<String, dynamic>> register({
    required String uid,
    required String fullName,
    String? email,
    String? phoneNumber,
    String? countryCode,
    String? profilePic,
    String? fcmToken,
    String? loginType,
  }) async {
    try {
      final response = await ApiService.post('/api/user/register', body: {
        'uid': uid,
        'full_name': fullName,
        'email': email,
        'phone_number': phoneNumber,
        'country_code': countryCode,
        'profile_pic': profilePic,
        'fcm_token': fcmToken,
        'login_type': loginType,
      });

      return response;
    } catch (e) {
      log('❌ User Register Error: $e');
      rethrow;
    }
  }

  /// Update user profile
  static Future<Map<String, dynamic>> updateProfile({
    required String uid,
    String? fullName,
    String? email,
    String? phoneNumber,
    String? countryCode,
    String? profilePic,
  }) async {
    try {
      final body = <String, dynamic>{'uid': uid};

      if (fullName != null) body['full_name'] = fullName;
      if (email != null) body['email'] = email;
      if (phoneNumber != null) body['phone_number'] = phoneNumber;
      if (countryCode != null) body['country_code'] = countryCode;
      if (profilePic != null) body['profile_pic'] = profilePic;

      final response = await ApiService.put('/api/user/profile', body: body);

      return response;
    } catch (e) {
      log('❌ Update User Profile Error: $e');
      rethrow;
    }
  }

  /// Update FCM token
  static Future<Map<String, dynamic>> updateFcmToken({
    required String uid,
    required String fcmToken,
  }) async {
    try {
      final response = await ApiService.post('/api/user/update-fcm', body: {
        'uid': uid,
        'fcm_token': fcmToken,
      });

      return response;
    } catch (e) {
      log('❌ Update FCM Token Error: $e');
      rethrow;
    }
  }
}

