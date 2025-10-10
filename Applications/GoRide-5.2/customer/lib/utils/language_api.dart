import 'dart:developer';
import 'package:customer/services/api_service.dart';

class LanguageApi {
  /// Get all active languages
  static Future<Map<String, dynamic>> getAllLanguages() async {
    try {
      final response = await ApiService.get('/api/languages');
      return response;
    } catch (e) {
      log('❌ Get Languages Error: $e');
      rethrow;
    }
  }

  /// Get language by code
  static Future<Map<String, dynamic>> getLanguageByCode(String code) async {
    try {
      final response = await ApiService.get('/api/languages/$code');
      return response;
    } catch (e) {
      log('❌ Get Language Error: $e');
      rethrow;
    }
  }
}

