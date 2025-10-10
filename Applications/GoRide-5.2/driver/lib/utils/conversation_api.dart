import 'dart:developer';
import 'package:driver/utils/api_service.dart';

class ConversationApi {
  /// Get messages for an order
  static Future<Map<String, dynamic>> getMessages(String orderId, {String orderType = 'city'}) async {
    try {
      final response = await ApiService.get(
        '/api/conversations/$orderId/messages',
        queryParams: {'order_type': orderType},
      );
      return response;
    } catch (e) {
      log('❌ Get Messages Error: $e');
      rethrow;
    }
  }

  /// Send message
  static Future<Map<String, dynamic>> sendMessage({
    required String orderId,
    required String senderId,
    required String senderType,
    required String message,
    String messageType = 'text',
    String orderType = 'city',
  }) async {
    try {
      final response = await ApiService.post('/api/conversations/$orderId/messages', body: {
        'sender_id': senderId,
        'sender_type': senderType,
        'message': message,
        'message_type': messageType,
        'order_type': orderType,
      });
      return response;
    } catch (e) {
      log('❌ Send Message Error: $e');
      rethrow;
    }
  }

  /// Mark messages as read
  static Future<Map<String, dynamic>> markAsRead({
    required String orderId,
    required String userId,
    String orderType = 'city',
  }) async {
    try {
      final response = await ApiService.post('/api/conversations/$orderId/read', body: {
        'user_id': userId,
        'order_type': orderType,
      });
      return response;
    } catch (e) {
      log('❌ Mark As Read Error: $e');
      rethrow;
    }
  }

  /// Get unread count
  static Future<Map<String, dynamic>> getUnreadCount(String userId) async {
    try {
      final response = await ApiService.get('/api/conversations/unread/$userId');
      return response;
    } catch (e) {
      log('❌ Get Unread Count Error: $e');
      rethrow;
    }
  }
}
