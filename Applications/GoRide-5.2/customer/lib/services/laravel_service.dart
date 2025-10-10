import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
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

  // Upload customer avatar
  static Future<String?> uploadCustomerAvatar(File imageFile) async {
    try {
      print('🔥 Uploading customer avatar...');
      
      var request = http.MultipartRequest(
        'POST',
        Uri.parse('${ApiService.baseUrl}/customer/upload-avatar'),
      );
      
      // Add authorization header
      String? token = await Preferences.getToken();
      if (token != null) {
        request.headers['Authorization'] = 'Bearer $token';
      }
      
      // Add image file
      request.files.add(await http.MultipartFile.fromPath('avatar', imageFile.path));
      
      var response = await request.send();
      var responseData = await response.stream.bytesToString();
      var jsonResponse = json.decode(responseData);
      
      print('🔥 Upload response: $jsonResponse');
      
      if (response.statusCode == 200 && jsonResponse['success'] == true) {
        return jsonResponse['data']['avatar_url'];
      } else {
        print('🔥 Upload failed: ${jsonResponse['message']}');
        return null;
      }
    } catch (e) {
      print('🔥 Upload error: $e');
      return null;
    }
  }

  // Delete customer avatar
  static Future<bool> deleteCustomerAvatar() async {
    try {
      print('🔥 Deleting customer avatar...');
      
      String? token = await Preferences.getToken();
      if (token == null) {
        print('🔥 No token found');
        return false;
      }
      
      final response = await http.delete(
        Uri.parse('${ApiService.baseUrl}/customer/delete-avatar'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );
      
      var jsonResponse = json.decode(response.body);
      print('🔥 Delete response: $jsonResponse');
      
      return response.statusCode == 200 && jsonResponse['success'] == true;
    } catch (e) {
      print('🔥 Delete error: $e');
      return false;
    }
  }

  // Update customer profile
  static Future<UserModel?> updateCustomerProfile({
    String? fullName,
    String? email,
    String? phoneNumber,
    String? countryCode,
    String? profilePic,
  }) async {
    try {
      print('🔥 Updating customer profile...');
      
      String? token = await Preferences.getToken();
      if (token == null) {
        print('🔥 No token found');
        return null;
      }
      
      Map<String, dynamic> data = {};
      if (fullName != null) data['full_name'] = fullName;
      if (email != null) data['email'] = email;
      if (phoneNumber != null) data['phone_number'] = phoneNumber;
      if (countryCode != null) data['country_code'] = countryCode;
      if (profilePic != null) data['profile_pic'] = profilePic;
      
      final response = await http.put(
        Uri.parse('${ApiService.baseUrl}/customer/profile'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: json.encode(data),
      );
      
      var jsonResponse = json.decode(response.body);
      print('🔥 Update profile response: $jsonResponse');
      
      if (response.statusCode == 200 && jsonResponse['success'] == true) {
        return UserModel.fromJson(jsonResponse['data']);
      }
      return null;
    } catch (e) {
      print('🔥 Update profile error: $e');
      return null;
    }
  }

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
      print('🔥 LaravelService.loginUser called with:');
      print('firebaseUid: $firebaseUid');
      print('email: $email');
      print('fullName: $fullName');
      print('phoneNumber: $phoneNumber');
      print('loginType: $loginType');
      
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

      print('🔥 API Response: $response');

      if (response['success'] == true && response['data'] != null) {
        // Store token
        try {
          await Preferences.setToken(response['data']['token']);
          print('🔥 Token stored: ${response['data']['token']}');
        } catch (e) {
          print('🔥 Error storing token: $e');
        }
        
        // Return user model
        try {
          final user = UserModel.fromJson(response['data']['user']);
          print('🔥 UserModel created: ${user.fullName}, ${user.email}');
          return user;
        } catch (e) {
          print('🔥 Error creating UserModel: $e');
          print('🔥 User data: ${response['data']['user']}');
          return null;
        }
      }
      print('🔥 Response failed - success: ${response['success']}, data: ${response['data']}');
      print('🔥 Full response: $response');
      return null;
    } catch (e) {
      print('Login error: $e');
      if (e is ApiException) {
        print('API Exception: ${e.message}');
        print('Status Code: ${e.statusCode}');
        print('Errors: ${e.errors}');
      }
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
      print('🔥 LaravelService.getServices: Calling API...');
      // Use correct endpoint: /services instead of /services/city
      final response = await _apiService.getServices();
      print('🔥 LaravelService.getServices: Got response: ${response['success']}');
      
      if (response['success'] == true && response['data'] != null) {
        print('🔥 LaravelService.getServices: Data length: ${response['data'].length}');
        List<ServiceModel> services = [];
        for (var serviceData in response['data']) {
          try {
            print('🔥 LaravelService.getServices: Parsing service ID ${serviceData['id']}');
            services.add(ServiceModel.fromJson(serviceData));
          } catch (e) {
            print('❌ Error parsing service: $e');
            print('❌ Service data: $serviceData');
          }
        }
        print('🔥 LaravelService.getServices: Successfully parsed ${services.length} services');
        return services;
      }
      print('❌ LaravelService.getServices: Response success=false or data=null');
      return [];
    } catch (e) {
      print('❌ Get services error: $e');
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
      print('🔥 Calling API getBanners...');
      final response = await _apiService.getBanners();
      print('🔥 API response: $response');
      
      if (response['success'] == true && response['data'] != null) {
        List<BannerModel> banners = [];
        for (var bannerData in response['data']) {
          print('🔥 Parsing banner: $bannerData');
          banners.add(BannerModel.fromJson(bannerData));
        }
        print('🔥 Parsed ${banners.length} banners successfully');
        return banners;
      }
      print('🔥 No banners in response or success=false');
      return [];
    } catch (e) {
      print('🔥 Get banners error: $e');
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
