import 'dart:convert';
import 'package:customer/services/api_service.dart';
import 'package:customer/model/user_model.dart';
import 'package:customer/model/order_model.dart';
import 'package:customer/model/service_model.dart';
import 'package:customer/model/zone_model.dart';
import 'package:customer/model/banner_model.dart';
import 'package:customer/model/coupon_model.dart';
import 'package:customer/model/wallet_transaction_model.dart';
import 'package:customer/utils/Preferences.dart';

class LaravelService {
  static final ApiService _apiService = ApiService();

  // Authentication
  static Future<UserModel?> loginUser({
    required String firebaseUid,
    required String email,
    required String fullName,
    String? phoneNumber,
    String? countryCode,
    required String loginType,
    String? fcmToken,
    String? profilePic,
  }) async {
    try {
      final response = await _apiService.customerLogin({
        'firebase_uid': firebaseUid,
        'email': email,
        'full_name': fullName,
        'phone_number': phoneNumber,
        'country_code': countryCode,
        'login_type': loginType,
        'fcm_token': fcmToken,
        'profile_pic': profilePic,
      });

      if (response['success'] == true && response['data'] != null) {
        // Store token
        await Preferences.setToken(response['data']['token']);
        
        // Return user model
        return UserModel.fromJson(response['data']['user']);
      }
      return null;
    } catch (e) {
      print('Login error: $e');
      return null;
    }
  }

  static Future<UserModel?> getUserProfile(String userId) async {
    try {
      final response = await _apiService.get('/customer/profile');
      
      if (response['success'] == true && response['data'] != null) {
        return UserModel.fromJson(response['data']);
      }
      return null;
    } catch (e) {
      print('Get user profile error: $e');
      return null;
    }
  }

  static Future<bool> updateUserProfile(UserModel user) async {
    try {
      final response = await _apiService.updateCustomerProfile(user.toJson());
      return response['success'] == true;
    } catch (e) {
      print('Update user profile error: $e');
      return false;
    }
  }

  // Services
  static Future<List<ServiceModel>> getServices() async {
    try {
      final response = await _apiService.getCityServices();
      
      if (response['success'] == true && response['data'] != null) {
        List<ServiceModel> services = [];
        for (var serviceData in response['data']) {
          services.add(ServiceModel.fromJson(serviceData));
        }
        return services;
      }
      return [];
    } catch (e) {
      print('Get services error: $e');
      return [];
    }
  }

  // Zones
  static Future<List<ZoneModel>> getZones() async {
    try {
      final response = await _apiService.getZones();
      
      if (response['success'] == true && response['data'] != null) {
        List<ZoneModel> zones = [];
        for (var zoneData in response['data']) {
          zones.add(ZoneModel.fromJson(zoneData));
        }
        return zones;
      }
      return [];
    } catch (e) {
      print('Get zones error: $e');
      return [];
    }
  }

  static Future<ZoneModel?> findZoneByLocation(double lat, double lng) async {
    try {
      final response = await _apiService.findZone(lat, lng);
      
      if (response['success'] == true && response['data'] != null) {
        return ZoneModel.fromJson(response['data']);
      }
      return null;
    } catch (e) {
      print('Find zone error: $e');
      return null;
    }
  }

  // Banners
  static Future<List<BannerModel>> getBanners() async {
    try {
      final response = await _apiService.getBanners();
      
      if (response['success'] == true && response['data'] != null) {
        List<BannerModel> banners = [];
        for (var bannerData in response['data']) {
          banners.add(BannerModel.fromJson(bannerData));
        }
        return banners;
      }
      return [];
    } catch (e) {
      print('Get banners error: $e');
      return [];
    }
  }

  // Orders
  static Future<bool> createOrder(OrderModel order) async {
    try {
      final orderData = {
        'service_id': order.serviceId,
        'source_location_name': order.sourceLocationName,
        'destination_location_name': order.destinationLocationName,
        'source_lat': order.sourceLocationLAtLng?.latitude,
        'source_lng': order.sourceLocationLAtLng?.longitude,
        'destination_lat': order.destinationLocationLAtLng?.latitude,
        'destination_lng': order.destinationLocationLAtLng?.longitude,
        'distance': double.tryParse(order.distance ?? '0') ?? 0,
        'distance_type': order.distanceType ?? 'km',
        'duration': order.duration,
        'payment_type': order.paymentType,
        'is_ac_selected': order.isAcSelected ?? false,
        'someone_else_data': order.someOneElse?.toJson(),
        'coupon_code': order.coupon?.code,
      };

      final response = await _apiService.createOrder(orderData);
      return response['success'] == true;
    } catch (e) {
      print('Create order error: $e');
      return false;
    }
  }

  static Future<List<OrderModel>> getUserOrders(String userId) async {
    try {
      final response = await _apiService.getUserOrders();
      
      if (response['success'] == true && response['data'] != null) {
        List<OrderModel> orders = [];
        for (var orderData in response['data']['data']) {
          orders.add(OrderModel.fromJson(orderData));
        }
        return orders;
      }
      return [];
    } catch (e) {
      print('Get user orders error: $e');
      return [];
    }
  }

  static Future<OrderModel?> getOrderById(String orderId) async {
    try {
      final response = await _apiService.getOrderDetails(orderId);
      
      if (response['success'] == true && response['data'] != null) {
        return OrderModel.fromJson(response['data']);
      }
      return null;
    } catch (e) {
      print('Get order details error: $e');
      return null;
    }
  }

  static Future<bool> updateOrder(OrderModel order) async {
    try {
      final response = await _apiService.updateOrderStatus(
        order.id!,
        order.status!,
      );
      return response['success'] == true;
    } catch (e) {
      print('Update order error: $e');
      return false;
    }
  }

  // Coupons
  static Future<List<CouponModel>> getAvailableCoupons() async {
    try {
      final response = await _apiService.getAvailableCoupons();
      
      if (response['success'] == true && response['data'] != null) {
        List<CouponModel> coupons = [];
        for (var couponData in response['data']) {
          coupons.add(CouponModel.fromJson(couponData));
        }
        return coupons;
      }
      return [];
    } catch (e) {
      print('Get coupons error: $e');
      return [];
    }
  }

  static Future<Map<String, dynamic>?> validateCoupon(String couponCode, double amount) async {
    try {
      final response = await _apiService.validateCoupon(couponCode, amount);
      
      if (response['success'] == true) {
        return response['data'];
      }
      return null;
    } catch (e) {
      print('Validate coupon error: $e');
      return null;
    }
  }

  // Wallet
  static Future<double> getWalletBalance() async {
    try {
      final response = await _apiService.getWalletBalance();
      
      if (response['success'] == true && response['data'] != null) {
        return double.tryParse(response['data']['balance'].toString()) ?? 0.0;
      }
      return 0.0;
    } catch (e) {
      print('Get wallet balance error: $e');
      return 0.0;
    }
  }

  static Future<List<WalletTransactionModel>> getWalletTransactions() async {
    try {
      final response = await _apiService.getWalletTransactions();
      
      if (response['success'] == true && response['data'] != null) {
        List<WalletTransactionModel> transactions = [];
        for (var transactionData in response['data']['data']) {
          transactions.add(WalletTransactionModel.fromJson(transactionData));
        }
        return transactions;
      }
      return [];
    } catch (e) {
      print('Get wallet transactions error: $e');
      return [];
    }
  }

  static Future<bool> addMoneyToWallet({
    required double amount,
    required String paymentType,
    required String paymentId,
  }) async {
    try {
      final response = await _apiService.addMoneyToWallet({
        'amount': amount,
        'payment_type': paymentType,
        'payment_id': paymentId,
      });
      return response['success'] == true;
    } catch (e) {
      print('Add money to wallet error: $e');
      return false;
    }
  }

  // Calculate Fare
  static Future<Map<String, dynamic>?> calculateFare({
    required String serviceId,
    required double distance,
    String? duration,
    bool isAcSelected = false,
    String? couponCode,
  }) async {
    try {
      final response = await _apiService.calculateFare({
        'service_id': serviceId,
        'distance': distance,
        'duration': duration,
        'is_ac_selected': isAcSelected,
        'coupon_code': couponCode,
      });
      
      if (response['success'] == true) {
        return response['data'];
      }
      return null;
    } catch (e) {
      print('Calculate fare error: $e');
      return null;
    }
  }

  // Logout
  static Future<bool> logout() async {
    try {
      await _apiService.logout();
      await Preferences.clearToken();
      return true;
    } catch (e) {
      print('Logout error: $e');
      // Clear token anyway
      await Preferences.clearToken();
      return false;
    }
  }

  // Check if user is logged in
  static bool isLoggedIn() {
    return Preferences.hasToken();
  }
}
