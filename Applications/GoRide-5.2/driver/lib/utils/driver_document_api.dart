import 'dart:developer';
import 'package:driver/utils/api_service.dart';

class DriverDocumentApi {
  /// Upload driver document
  static Future<Map<String, dynamic>> uploadDocument({
    required int driverId,
    required String documentType,
    required String documentBase64,
    String? documentName,
  }) async {
    try {
      final response = await ApiService.post(
        '/api/driver/documents/upload',
        body: {
          'driver_id': driverId,
          'document_type': documentType,
          'document': documentBase64,
          'document_name': documentName,
        },
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
      final response = await ApiService.get(
        '/api/driver/documents',
        queryParams: {'driver_id': driverId.toString()},
      );
      return response;
    } catch (e) {
      log('❌ Get Documents Error: $e');
      rethrow;
    }
  }

  /// Delete driver document
  static Future<Map<String, dynamic>> deleteDocument(int documentId) async {
    try {
      final response = await ApiService.delete('/api/driver/documents/$documentId');
      return response;
    } catch (e) {
      log('❌ Delete Document Error: $e');
      rethrow;
    }
  }
}

