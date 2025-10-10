import 'dart:developer';
import 'package:customer/services/api_service.dart';

class ConversationApi {
  /// Get messages for an order
  static Future<Map<String, dynamic>> getMessages(String orderId) async {
    try {
      final response = await ApiService.get('/api/conversations', queryParams: {
        'order_id': orderId,
      });
      return response;
    } catch (e) {
      log('❌ Get Messages Error: $e');
      rethrow;
    }
  }

  /// Send a message
  static Future<Map<String, dynamic>> sendMessage({
    required int orderId,
    required int customerId,
    required int driverId,
    required String senderType, // 'customer' or 'driver'
    required String message,
    String messageType = 'text', // 'text', 'image', 'video'
    String? fileUrl,
  }) async {
    try {
      final response = await ApiService.post('/api/conversations', body: {
        'order_id': orderId,
        'customer_id': customerId,
        'driver_id': driverId,
        'sender_type': senderType,
        'message_type': messageType,
        'message': message,
        'file_url': fileUrl,
      });
      return response;
    } catch (e) {
      log('❌ Send Message Error: $e');
      rethrow;
    }
  }

  /// Mark messages as read
  static Future<Map<String, dynamic>> markAsRead({
    required int orderId,
    required String userType, // 'customer' or 'driver'
  }) async {
    try {
      final response = await ApiService.put('/api/conversations/read', body: {
        'order_id': orderId,
        'user_type': userType,
      });
      return response;
    } catch (e) {
      log('❌ Mark As Read Error: $e');
      rethrow;
    }
  }

  /// Get unread message count
  static Future<Map<String, dynamic>> getUnreadCount({
    required int orderId,
    required String userType, // 'customer' or 'driver'
  }) async {
    try {
      final response = await ApiService.get('/api/conversations/unread', queryParams: {
        'order_id': orderId.toString(),
        'user_type': userType,
      });
      return response;
    } catch (e) {
      log('❌ Get Unread Count Error: $e');
      rethrow;
    }
  }
}

