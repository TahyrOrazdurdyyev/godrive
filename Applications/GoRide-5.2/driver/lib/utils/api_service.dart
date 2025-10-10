import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:driver/constant/constant.dart';

class ApiService {
  // Base URL from Constant
  static String get baseUrl => Constant.baseUrl;

  // Headers
  static Map<String, String> get headers => {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      };

  /// GET Request
  static Future<dynamic> get(String endpoint, {Map<String, String>? queryParams}) async {
    try {
      Uri uri = Uri.parse('$baseUrl$endpoint');
      if (queryParams != null && queryParams.isNotEmpty) {
        uri = uri.replace(queryParameters: queryParams);
      }

      log('🌐 GET Request: $uri');

      final response = await http.get(uri, headers: headers).timeout(
            const Duration(seconds: 30),
          );

      log('📥 Response Status: ${response.statusCode}');
      log('📥 Response Body: ${response.body}');

      return _handleResponse(response);
    } catch (e) {
      log('❌ GET Request Error: $e');
      throw Exception('Network error: $e');
    }
  }

  /// POST Request
  static Future<dynamic> post(String endpoint, {Map<String, dynamic>? body}) async {
    try {
      final uri = Uri.parse('$baseUrl$endpoint');
      log('🌐 POST Request: $uri');
      log('📤 Request Body: ${json.encode(body)}');

      final response = await http
          .post(
            uri,
            headers: headers,
            body: json.encode(body),
          )
          .timeout(
            const Duration(seconds: 30),
          );

      log('📥 Response Status: ${response.statusCode}');
      log('📥 Response Body: ${response.body}');

      return _handleResponse(response);
    } catch (e) {
      log('❌ POST Request Error: $e');
      throw Exception('Network error: $e');
    }
  }

  /// PUT Request
  static Future<dynamic> put(String endpoint, {Map<String, dynamic>? body}) async {
    try {
      final uri = Uri.parse('$baseUrl$endpoint');
      log('🌐 PUT Request: $uri');
      log('📤 Request Body: ${json.encode(body)}');

      final response = await http
          .put(
            uri,
            headers: headers,
            body: json.encode(body),
          )
          .timeout(
            const Duration(seconds: 30),
          );

      log('📥 Response Status: ${response.statusCode}');
      log('📥 Response Body: ${response.body}');

      return _handleResponse(response);
    } catch (e) {
      log('❌ PUT Request Error: $e');
      throw Exception('Network error: $e');
    }
  }

  /// DELETE Request
  static Future<dynamic> delete(String endpoint) async {
    try {
      final uri = Uri.parse('$baseUrl$endpoint');
      log('🌐 DELETE Request: $uri');

      final response = await http.delete(uri, headers: headers).timeout(
            const Duration(seconds: 30),
          );

      log('📥 Response Status: ${response.statusCode}');
      log('📥 Response Body: ${response.body}');

      return _handleResponse(response);
    } catch (e) {
      log('❌ DELETE Request Error: $e');
      throw Exception('Network error: $e');
    }
  }

  /// Multipart Request (for file uploads)
  static Future<dynamic> uploadFile(
    String endpoint, {
    required Map<String, String> fields,
    required Map<String, String> files, // filename -> file path
  }) async {
    try {
      final uri = Uri.parse('$baseUrl$endpoint');
      log('🌐 Upload Request: $uri');

      var request = http.MultipartRequest('POST', uri);

      // Add fields
      request.fields.addAll(fields);

      // Add files
      for (var entry in files.entries) {
        request.files.add(await http.MultipartFile.fromPath(
          entry.key,
          entry.value,
        ));
      }

      log('📤 Upload Fields: $fields');
      log('📤 Upload Files: ${files.keys}');

      final streamedResponse = await request.send().timeout(
            const Duration(seconds: 60),
          );
      final response = await http.Response.fromStream(streamedResponse);

      log('📥 Response Status: ${response.statusCode}');
      log('📥 Response Body: ${response.body}');

      return _handleResponse(response);
    } catch (e) {
      log('❌ Upload Request Error: $e');
      throw Exception('Upload error: $e');
    }
  }

  /// Handle HTTP Response
  static dynamic _handleResponse(http.Response response) {
    if (response.statusCode >= 200 && response.statusCode < 300) {
      try {
        return json.decode(response.body);
      } catch (e) {
        log('⚠️ JSON Decode Error: $e');
        return {'success': true, 'data': response.body};
      }
    } else if (response.statusCode == 404) {
      throw Exception('Not found: ${response.body}');
    } else if (response.statusCode == 401) {
      throw Exception('Unauthorized: ${response.body}');
    } else if (response.statusCode >= 500) {
      throw Exception('Server error: ${response.body}');
    } else {
      throw Exception('Request failed (${response.statusCode}): ${response.body}');
    }
  }
}

