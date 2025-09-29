import 'dart:convert';
import 'package:driver/services/api_service.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/model/order_model.dart';
import 'package:driver/model/service_model.dart';
import 'package:driver/model/zone_model.dart';
import 'package:driver/model/wallet_transaction_model.dart';
import 'package:driver/utils/Preferences.dart';

class LaravelService {
  static final ApiService _apiService = ApiService();

  // Authentication
  static Future<DriverUserModel?> loginDriver({
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
      final response = await _apiService.driverLogin({
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
        
        // Return driver model
        return DriverUserModel.fromJson(response['data']['driver']);
      }
      return null;
    } catch (e) {
      print('Driver login error: $e');
      return null;
    }
  }

  static Future<DriverUserModel?> getDriverProfile() async {
    try {
      final response = await _apiService.get('/driver/profile');
      
      if (response['success'] == true && response['data'] != null) {
        return DriverUserModel.fromJson(response['data']);
      }
      return null;
    } catch (e) {
      print('Get driver profile error: $e');
      return null;
    }
  }

  static Future<bool> updateDriverProfile(DriverUserModel driver) async {
    try {
      final response = await _apiService.updateDriverProfile(driver.toJson());
      return response['success'] == true;
    } catch (e) {
      print('Update driver profile error: $e');
      return false;
    }
  }

  // Location and Status
  static Future<bool> updateDriverLocation({
    required double latitude,
    required double longitude,
    required double bearing,
    double? speed,
  }) async {
    try {
      final response = await _apiService.updateDriverLocation({
        'latitude': latitude,
        'longitude': longitude,
        'bearing': bearing,
        'speed': speed,
      });
      return response['success'] == true;
    } catch (e) {
      print('Update driver location error: $e');
      return false;
    }
  }

  static Future<bool> updateDriverStatus({
    required String status,
    bool? isOnline,
  }) async {
    try {
      final response = await _apiService.updateDriverStatus({
        'status': status,
        'is_online': isOnline,
      });
      return response['success'] == true;
    } catch (e) {
      print('Update driver status error: $e');
      return false;
    }
  }

  // Orders
  static Future<List<OrderModel>> getNearbyOrders() async {
    try {
      final response = await _apiService.getNearbyOrders();
      
      if (response['success'] == true && response['data'] != null) {
        List<OrderModel> orders = [];
        for (var orderData in response['data']) {
          orders.add(OrderModel.fromJson(orderData));
        }
        return orders;
      }
      return [];
    } catch (e) {
      print('Get nearby orders error: $e');
      return [];
    }
  }

  static Future<bool> acceptOrder(String orderId) async {
    try {
      final response = await _apiService.acceptOrder(orderId);
      return response['success'] == true;
    } catch (e) {
      print('Accept order error: $e');
      return false;
    }
  }

  static Future<bool> rejectOrder(String orderId) async {
    try {
      final response = await _apiService.rejectOrder(orderId);
      return response['success'] == true;
    } catch (e) {
      print('Reject order error: $e');
      return false;
    }
  }

  static Future<List<OrderModel>> getDriverOrders() async {
    try {
      final response = await _apiService.getDriverOrders();
      
      if (response['success'] == true && response['data'] != null) {
        List<OrderModel> orders = [];
        for (var orderData in response['data']['data']) {
          orders.add(OrderModel.fromJson(orderData));
        }
        return orders;
      }
      return [];
    } catch (e) {
      print('Get driver orders error: $e');
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

  static Future<bool> updateOrderStatus(String orderId, String status) async {
    try {
      final response = await _apiService.updateOrderStatus(orderId, status);
      return response['success'] == true;
    } catch (e) {
      print('Update order status error: $e');
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

  static Future<List<ServiceModel>> getIntercityServices() async {
    try {
      final response = await _apiService.getIntercityServices();
      
      if (response['success'] == true && response['data'] != null) {
        List<ServiceModel> services = [];
        for (var serviceData in response['data']) {
          services.add(ServiceModel.fromJson(serviceData));
        }
        return services;
      }
      return [];
    } catch (e) {
      print('Get intercity services error: $e');
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

  static Future<bool> withdrawMoney({
    required double amount,
    required String bankAccount,
    required String note,
  }) async {
    try {
      final response = await _apiService.withdrawMoney({
        'amount': amount,
        'bank_account': bankAccount,
        'note': note,
      });
      return response['success'] == true;
    } catch (e) {
      print('Withdraw money error: $e');
      return false;
    }
  }

  // Chat
  static Future<List<Map<String, dynamic>>> getOrderMessages(String orderId) async {
    try {
      final response = await _apiService.getOrderMessages(orderId);
      
      if (response['success'] == true && response['data'] != null) {
        return List<Map<String, dynamic>>.from(response['data']);
      }
      return [];
    } catch (e) {
      print('Get order messages error: $e');
      return [];
    }
  }

  static Future<bool> sendMessage(String orderId, {
    required String message,
    String? type,
    String? mediaUrl,
  }) async {
    try {
      final response = await _apiService.sendMessage(orderId, {
        'message': message,
        'type': type ?? 'text',
        'media_url': mediaUrl,
      });
      return response['success'] == true;
    } catch (e) {
      print('Send message error: $e');
      return false;
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

  // Check if driver is logged in
  static bool isLoggedIn() {
    return Preferences.hasToken();
  }
}
