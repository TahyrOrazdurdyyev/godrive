import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:driver/utils/Preferences.dart';

class ApiService {
  static const String baseUrl = 'http://185.10.16.248/api/v1'; // Production server URL
  
  // Singleton pattern
  static final ApiService _instance = ApiService._internal();
  factory ApiService() => _instance;
  ApiService._internal();

  // HTTP client with common headers
  Map<String, String> _getHeaders({bool requireAuth = true}) {
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
  Map<String, dynamic> _handleResponse(http.Response response) {
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
  Future<Map<String, dynamic>> get(String endpoint, {bool requireAuth = true}) async {
    try {
      final headers = _getHeaders(requireAuth: requireAuth);
      final response = await http.get(
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

  // POST request
  Future<Map<String, dynamic>> post(String endpoint, {
    Map<String, dynamic>? data,
    bool requireAuth = true,
  }) async {
    try {
      final headers = _getHeaders(requireAuth: requireAuth);
      final response = await http.post(
        Uri.parse('$baseUrl$endpoint'),
        headers: headers,
        body: data != null ? json.encode(data) : null,
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
  Future<Map<String, dynamic>> put(String endpoint, {
    Map<String, dynamic>? data,
    bool requireAuth = true,
  }) async {
    try {
      final headers = _getHeaders(requireAuth: requireAuth);
      final response = await http.put(
        Uri.parse('$baseUrl$endpoint'),
        headers: headers,
        body: data != null ? json.encode(data) : null,
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
  Future<Map<String, dynamic>> delete(String endpoint, {bool requireAuth = true}) async {
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

  // Driver Authentication APIs
  Future<Map<String, dynamic>> driverLogin(Map<String, dynamic> userData) async {
    return await post('/driver/login', data: userData, requireAuth: false);
  }

  Future<Map<String, dynamic>> updateDriverProfile(Map<String, dynamic> userData) async {
    return await put('/driver/profile', data: userData);
  }

  // Driver Location APIs
  Future<Map<String, dynamic>> updateDriverLocation(Map<String, dynamic> locationData) async {
    return await post('/driver/location', data: locationData);
  }

  Future<Map<String, dynamic>> updateDriverStatus(Map<String, dynamic> statusData) async {
    return await post('/driver/status', data: statusData);
  }

  // Order APIs for Drivers
  Future<Map<String, dynamic>> getNearbyOrders() async {
    return await get('/driver/orders/nearby');
  }

  Future<Map<String, dynamic>> acceptOrder(String orderId) async {
    return await post('/driver/orders/$orderId/accept', data: {});
  }

  Future<Map<String, dynamic>> rejectOrder(String orderId) async {
    return await post('/driver/orders/$orderId/reject', data: {});
  }

  Future<Map<String, dynamic>> getDriverOrders({int page = 1}) async {
    return await get('/driver/orders?page=$page');
  }

  Future<Map<String, dynamic>> getOrderDetails(String orderId) async {
    return await get('/driver/orders/$orderId');
  }

  Future<Map<String, dynamic>> updateOrderStatus(String orderId, String status) async {
    return await put('/driver/orders/$orderId/status', data: {'status': status});
  }

  // Service APIs (public)
  Future<Map<String, dynamic>> getServices() async {
    return await get('/services', requireAuth: false);
  }

  Future<Map<String, dynamic>> getCityServices() async {
    return await get('/services/city', requireAuth: false);
  }

  Future<Map<String, dynamic>> getIntercityServices() async {
    return await get('/services/intercity', requireAuth: false);
  }

  // Zone APIs
  Future<Map<String, dynamic>> getZones() async {
    return await get('/zones', requireAuth: false);
  }

  Future<Map<String, dynamic>> findZone(double lat, double lng) async {
    return await post('/zones/find', data: {'lat': lat, 'lng': lng}, requireAuth: false);
  }

  // Driver Wallet APIs
  Future<Map<String, dynamic>> getWalletBalance() async {
    return await get('/driver/wallet/balance');
  }

  Future<Map<String, dynamic>> getWalletTransactions({int page = 1}) async {
    return await get('/driver/wallet/transactions?page=$page');
  }

  Future<Map<String, dynamic>> withdrawMoney(Map<String, dynamic> withdrawData) async {
    return await post('/driver/wallet/withdraw', data: withdrawData);
  }

  Future<Map<String, dynamic>> getTransactionDetails(String transactionId) async {
    return await get('/driver/wallet/transactions/$transactionId');
  }

  // Driver Reviews
  Future<Map<String, dynamic>> getDriverReviews({int page = 1}) async {
    return await get('/driver/reviews?page=$page');
  }

  // Chat APIs
  Future<Map<String, dynamic>> getOrderMessages(String orderId) async {
    return await get('/driver/orders/$orderId/messages');
  }

  Future<Map<String, dynamic>> sendMessage(String orderId, Map<String, dynamic> messageData) async {
    return await post('/driver/orders/$orderId/messages', data: messageData);
  }

  // Logout
  Future<Map<String, dynamic>> logout() async {
    return await post('/driver/logout', data: {});
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
