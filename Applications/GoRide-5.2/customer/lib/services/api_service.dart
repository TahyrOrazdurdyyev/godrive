import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:customer/utils/Preferences.dart';

class ApiService {
  static const String baseUrl = 'http://185.10.16.248'; // Production server URL

  // HTTP client with common headers
  static Map<String, String> _getHeaders({bool requireAuth = true}) {
    Map<String, String> headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };

    if (requireAuth) {
      String? token = Preferences.getToken();
      if (token != null) {
        headers['Authorization'] = 'Bearer $token';
      }
    }

    return headers;
  }

  // Handle API response
  static Map<String, dynamic> _handleResponse(http.Response response) {
    final responseBody = json.decode(response.body);
    
    if (response.statusCode >= 200 && response.statusCode < 300) {
      return responseBody;
    } else {
      throw ApiException(
        message: responseBody['message'] ?? 'Something went wrong',
        statusCode: response.statusCode,
        errors: responseBody['errors'],
      );
    }
  }

  // GET request
  static Future<Map<String, dynamic>> get(String endpoint, {Map<String, dynamic>? queryParams, bool requireAuth = true}) async {
    try {
      final headers = _getHeaders(requireAuth: requireAuth);
      Uri uri = Uri.parse('$baseUrl$endpoint');
      
      if (queryParams != null && queryParams.isNotEmpty) {
        uri = uri.replace(queryParameters: queryParams.map((key, value) => MapEntry(key, value.toString())));
      }
      
      final response = await http.get(uri, headers: headers);
      return _handleResponse(response);
    } on SocketException {
      throw ApiException(message: 'No internet connection');
    } catch (e) {
      if (e is ApiException) rethrow;
      throw ApiException(message: 'Network error: ${e.toString()}');
    }
  }

  // POST request
  static Future<Map<String, dynamic>> post(String endpoint, {
    Map<String, dynamic>? body,
    bool requireAuth = true,
  }) async {
    try {
      final headers = _getHeaders(requireAuth: requireAuth);
      final response = await http.post(
        Uri.parse('$baseUrl$endpoint'),
        headers: headers,
        body: body != null ? json.encode(body) : null,
      );
      return _handleResponse(response);
    } on SocketException {
      throw ApiException(message: 'No internet connection');
    } catch (e) {
      if (e is ApiException) rethrow;
      throw ApiException(message: 'Network error: ${e.toString()}');
    }
  }

  // PUT request
  static Future<Map<String, dynamic>> put(String endpoint, {
    Map<String, dynamic>? body,
    bool requireAuth = true,
  }) async {
    try {
      final headers = _getHeaders(requireAuth: requireAuth);
      final response = await http.put(
        Uri.parse('$baseUrl$endpoint'),
        headers: headers,
        body: body != null ? json.encode(body) : null,
      );
      return _handleResponse(response);
    } on SocketException {
      throw ApiException(message: 'No internet connection');
    } catch (e) {
      if (e is ApiException) rethrow;
      throw ApiException(message: 'Network error: ${e.toString()}');
    }
  }

  // DELETE request
  static Future<Map<String, dynamic>> delete(String endpoint, {bool requireAuth = true}) async {
    try {
      final headers = _getHeaders(requireAuth: requireAuth);
      final response = await http.delete(
        Uri.parse('$baseUrl$endpoint'),
        headers: headers,
      );
      return _handleResponse(response);
    } on SocketException {
      throw ApiException(message: 'No internet connection');
    } catch (e) {
      if (e is ApiException) rethrow;
      throw ApiException(message: 'Network error: ${e.toString()}');
    }
  }
}

// Custom exception class
class ApiException implements Exception {
  final String message;
  final int? statusCode;
  final Map<String, dynamic>? errors;

  ApiException({
    required this.message,
    this.statusCode,
    this.errors,
  });

  @override
  String toString() {
    return 'ApiException: $message';
  }
}
