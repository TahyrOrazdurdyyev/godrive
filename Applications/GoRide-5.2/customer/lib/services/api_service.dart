import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:customer/utils/Preferences.dart';

class ApiService {
  static const String baseUrl = 'http://localhost:8000/api/v1'; // Change this to your server URL
  
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

  // Authentication APIs
  Future<Map<String, dynamic>> customerLogin(Map<String, dynamic> userData) async {
    return await post('/customer/login', data: userData, requireAuth: false);
  }

  Future<Map<String, dynamic>> updateCustomerProfile(Map<String, dynamic> userData) async {
    return await put('/customer/profile', data: userData);
  }

  // Order APIs
  Future<Map<String, dynamic>> createOrder(Map<String, dynamic> orderData) async {
    return await post('/customer/orders', data: orderData);
  }

  Future<Map<String, dynamic>> getUserOrders({int page = 1}) async {
    return await get('/customer/orders?page=$page');
  }

  Future<Map<String, dynamic>> getOrderDetails(String orderId) async {
    return await get('/customer/orders/$orderId');
  }

  Future<Map<String, dynamic>> updateOrderStatus(String orderId, String status) async {
    return await put('/customer/orders/$orderId/status', data: {'status': status});
  }

  // Service APIs
  Future<Map<String, dynamic>> getServices() async {
    return await get('/services', requireAuth: false);
  }

  Future<Map<String, dynamic>> getCityServices() async {
    return await get('/services/city', requireAuth: false);
  }

  Future<Map<String, dynamic>> calculateFare(Map<String, dynamic> fareData) async {
    return await post('/calculate-fare', data: fareData, requireAuth: false);
  }

  // Zone APIs
  Future<Map<String, dynamic>> getZones() async {
    return await get('/zones', requireAuth: false);
  }

  Future<Map<String, dynamic>> findZone(double lat, double lng) async {
    return await post('/zones/find', data: {'lat': lat, 'lng': lng}, requireAuth: false);
  }

  // Banner APIs
  Future<Map<String, dynamic>> getBanners() async {
    return await get('/banners', requireAuth: false);
  }

  // Coupon APIs
  Future<Map<String, dynamic>> getAvailableCoupons() async {
    return await get('/customer/coupons');
  }

  Future<Map<String, dynamic>> validateCoupon(String couponCode, double amount) async {
    return await post('/customer/coupons/validate', data: {
      'coupon_code': couponCode,
      'order_amount': amount,
    });
  }

  // Wallet APIs
  Future<Map<String, dynamic>> getWalletBalance() async {
    return await get('/customer/wallet/balance');
  }

  Future<Map<String, dynamic>> getWalletTransactions({int page = 1}) async {
    return await get('/customer/wallet/transactions?page=$page');
  }

  Future<Map<String, dynamic>> addMoneyToWallet(Map<String, dynamic> paymentData) async {
    return await post('/customer/wallet/add-money', data: paymentData);
  }

  // Review APIs
  Future<Map<String, dynamic>> createReview(String orderId, Map<String, dynamic> reviewData) async {
    return await post('/customer/orders/$orderId/review', data: reviewData);
  }

  // Chat APIs
  Future<Map<String, dynamic>> getOrderMessages(String orderId) async {
    return await get('/customer/orders/$orderId/messages');
  }

  Future<Map<String, dynamic>> sendMessage(String orderId, Map<String, dynamic> messageData) async {
    return await post('/customer/orders/$orderId/messages', data: messageData);
  }

  // Logout
  Future<Map<String, dynamic>> logout() async {
    return await post('/customer/logout', data: {});
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
