import 'dart:developer';
import 'package:driver/utils/api_service.dart';

class SubscriptionApi {
  /// Get all subscription plans
  static Future<Map<String, dynamic>> getAllPlans() async {
    try {
      final response = await ApiService.get('/api/subscriptions/plans');
      return response;
    } catch (e) {
      log('❌ Get All Subscription Plans Error: $e');
      rethrow;
    }
  }

  /// Get subscription plan by ID
  static Future<Map<String, dynamic>> getPlanById(String planId) async {
    try {
      final response = await ApiService.get('/api/subscriptions/plans/$planId');
      return response;
    } catch (e) {
      log('❌ Get Subscription Plan Error: $e');
      rethrow;
    }
  }

  /// Create subscription history
  static Future<Map<String, dynamic>> createHistory({
    required String id,
    required int userId,
    required dynamic subscriptionPlanId,
    required Map<String, dynamic> subscriptionPlanData,
    String? paymentType,
    String? expiryDate,
  }) async {
    try {
      final response = await ApiService.post(
        '/api/subscriptions/history',
        body: {
          'id': id,
          'user_id': userId,
          'subscription_plan_id': subscriptionPlanId,
          'subscription_plan_data': subscriptionPlanData,
          'payment_type': paymentType,
          'expiry_date': expiryDate,
        },
      );
      return response;
    } catch (e) {
      log('❌ Create Subscription History Error: $e');
      rethrow;
    }
  }

  /// Get user subscription history
  static Future<Map<String, dynamic>> getHistory(int userId) async {
    try {
      final response = await ApiService.get('/api/subscriptions/history/$userId');
      return response;
    } catch (e) {
      log('❌ Get Subscription History Error: $e');
      rethrow;
    }
  }
}

