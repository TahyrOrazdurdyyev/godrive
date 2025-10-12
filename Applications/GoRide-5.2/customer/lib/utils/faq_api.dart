import 'dart:developer';
import 'package:customer/services/api_service.dart';

class FaqApi {
  /// Get all active FAQs
  static Future<Map<String, dynamic>> getAllFaqs() async {
    try {
      final response = await ApiService.get('/api/faqs');
      return response;
    } catch (e) {
      log('❌ Get FAQs Error: $e');
      rethrow;
    }
  }

  /// Get FAQ by ID
  static Future<Map<String, dynamic>> getFaqById(String id) async {
    try {
      final response = await ApiService.get('/api/faqs/$id');
      return response;
    } catch (e) {
      log('❌ Get FAQ Error: $e');
      rethrow;
    }
  }
}

