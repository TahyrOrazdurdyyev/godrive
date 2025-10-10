import 'dart:developer';
import 'package:customer/services/api_service.dart';

class CouponApi {
  /// Validate coupon code
  static Future<Map<String, dynamic>> validateCoupon({
    required String code,
    required double amount,
  }) async {
    try {
      final response = await ApiService.get('/api/coupons/validate', queryParams: {
        'code': code,
        'amount': amount.toString(),
      });
      return response;
    } catch (e) {
      log('❌ Validate Coupon Error: $e');
      rethrow;
    }
  }

  /// Get all active coupons
  static Future<Map<String, dynamic>> getAllCoupons() async {
    try {
      final response = await ApiService.get('/api/coupons');
      return response;
    } catch (e) {
      log('❌ Get Coupons Error: $e');
      rethrow;
    }
  }

  /// Record coupon usage
  static Future<Map<String, dynamic>> useCoupon({
    required int couponId,
  }) async {
    try {
      final response = await ApiService.post('/api/coupons/use', body: {
        'coupon_id': couponId,
      });
      return response;
    } catch (e) {
      log('❌ Use Coupon Error: $e');
      rethrow;
    }
  }
}

