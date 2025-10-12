import 'dart:async';
import 'dart:developer';
import 'package:customer/model/order_model.dart';
import 'package:customer/model/user_model.dart';
import 'package:customer/utils/fire_store_utils.dart';
import 'package:customer/utils/user_api.dart';
import 'package:customer/utils/order_api.dart';
import 'package:customer/services/websocket_service.dart';
import 'package:get/get.dart';

class OrderDetailsController extends GetxController {
  Rx<UserModel> userModel = UserModel().obs;
  Rx<OrderModel> orderModel = OrderModel().obs;
  RxBool isLoading = false.obs;
  
  Timer? _refreshTimer;
  final WebSocketService _wsService = WebSocketService();

  @override
  void onInit() {
    super.onInit();
    getArgument();
    getUser();
  }

  @override
  void onClose() {
    _refreshTimer?.cancel();
    _wsService.disconnect();
    super.onClose();
  }

  getArgument() async {
    dynamic argumentData = Get.arguments;
    if (argumentData != null) {
      orderModel.value = argumentData['orderModel'];
      
      // Start WebSocket subscription and periodic refresh
      _connectWebSocket();
      _startPeriodicRefresh();
    }
    update();
  }

  /// Connect to WebSocket for real-time order updates
  void _connectWebSocket() {
    if (orderModel.value.id == null) return;
    
    _wsService.connect();
    _wsService.subscribeToChannel('orders', (event, data) {
      log('📦 Order update event: $event');
      if (event == 'order.updated' && data != null) {
        try {
          // Check if this update is for our order
          if (data['order'] != null && data['order']['id'].toString() == orderModel.value.id) {
            // Refresh order data from API
            refreshOrderData();
          }
        } catch (e) {
          log('❌ Error processing WebSocket event: $e');
        }
      }
    });
  }

  /// Periodic refresh every 5 seconds as fallback
  void _startPeriodicRefresh() {
    _refreshTimer = Timer.periodic(const Duration(seconds: 5), (timer) {
      refreshOrderData();
    });
  }

  /// Refresh order data from API
  Future<void> refreshOrderData() async {
    if (orderModel.value.id == null) return;
    
    try {
      final response = await OrderApi.getOrderById(orderModel.value.id!);
      
      if (response['success'] == true && response['order'] != null) {
        // Parse updated order
        final orderData = response['order'];
        orderModel.value = _parseOrderFromApi(orderData);
        update();
      }
    } catch (e) {
      log('❌ Error refreshing order: $e');
    }
  }

  /// Parse order from API response
  OrderModel _parseOrderFromApi(Map<String, dynamic> orderData) {
    OrderModel order = OrderModel();
    
    order.id = orderData['id']?.toString();
    order.userId = orderData['user_id']?.toString();
    order.driverId = orderData['driver_id']?.toString();
    order.serviceId = orderData['service_id']?.toString();
    order.zoneId = orderData['zone_id']?.toString();
    order.sourceLocationName = orderData['source_location_name'];
    order.destinationLocationName = orderData['destination_location_name'];
    order.distance = orderData['distance']?.toString();
    order.distanceType = orderData['distance_type'] ?? 'km';
    order.duration = orderData['duration'];
    order.offerRate = orderData['offer_rate']?.toString();
    order.finalRate = orderData['final_rate']?.toString();
    order.paymentType = orderData['payment_type'];
    order.paymentStatus = orderData['payment_status'] == 1 || orderData['payment_status'] == true;
    order.status = orderData['status'];
    order.otp = orderData['otp'];
    order.isAcSelected = orderData['is_ac_selected'] == 1 || orderData['is_ac_selected'] == true;
    
    return order;
  }

  getUser() async {
    try {
      final uid = FireStoreUtils.getCurrentUid();
      final response = await UserApi.getProfile(uid);
      
      if (response['success'] == true && response['user'] != null) {
        userModel.value = UserModel.fromJson(response['user']);
      }
    } catch (e) {
      log('❌ Error getting user: $e');
    }
  }
}
