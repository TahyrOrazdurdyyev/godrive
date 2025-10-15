import 'dart:async';
import 'dart:developer';
import 'package:customer/model/intercity_order_model.dart';
import 'package:customer/utils/fire_store_utils.dart';
import 'package:customer/utils/intercity_order_api.dart';
import 'package:customer/utils/user_api.dart';
import 'package:customer/services/websocket_service.dart';
import 'package:get/get.dart';

class InterCityOrderScreenController extends GetxController {
  RxList<InterCityOrderModel> activeOrders = <InterCityOrderModel>[].obs;
  RxList<InterCityOrderModel> completedOrders = <InterCityOrderModel>[].obs;
  RxList<InterCityOrderModel> cancelledOrders = <InterCityOrderModel>[].obs;
  
  RxBool isLoadingActive = true.obs;
  RxBool isLoadingCompleted = true.obs;
  RxBool isLoadingCancelled = true.obs;

  Timer? _refreshTimer;
  final WebSocketService _wsService = WebSocketService();

  @override
  void onInit() {
    super.onInit();
    loadAllOrders();
    _connectWebSocket();
    _startPeriodicRefresh();
  }

  @override
  void onClose() {
    _refreshTimer?.cancel();
    _wsService.disconnect();
    super.onClose();
  }

  /// Connect to WebSocket for real-time order updates
  void _connectWebSocket() {
    _wsService.connect();
    _wsService.subscribeToChannel('orders', (event, data) {
      log('📦 InterCity Order event received: $event');
      if (event == 'order.updated' || event == 'order.created') {
        loadAllOrders();
      }
    });
  }

  /// Periodic refresh every 10 seconds
  void _startPeriodicRefresh() {
    _refreshTimer = Timer.periodic(const Duration(seconds: 10), (timer) {
      loadAllOrders();
    });
  }

  /// Load all orders
  Future<void> loadAllOrders() async {
    await Future.wait([
      loadActiveOrders(),
      loadCompletedOrders(),
      loadCancelledOrders(),
    ]);
  }

  /// Load active orders
  Future<void> loadActiveOrders() async {
    try {
      isLoadingActive.value = true;
      
      final uid = FireStoreUtils.getCurrentUid();
      final userResponse = await UserApi.getProfile(uid);
      final userId = userResponse['user']['id'];
      
      final response = await InterCityOrderApi.getCustomerOrders(userId);
      
      if (response['success'] == true && response['orders'] != null) {
        final List<dynamic> ordersData = response['orders'];
        
        // Filter active orders
        activeOrders.value = ordersData
            .map((order) => _parseOrderFromApi(order))
            .where((order) => 
                order.status != 'completed' && 
                order.status != 'cancelled' &&
                (order.paymentStatus == false || order.paymentStatus == null))
            .toList();
      }
    } catch (e) {
      log('❌ Error loading active intercity orders: $e');
    } finally {
      isLoadingActive.value = false;
    }
  }

  /// Load completed orders
  Future<void> loadCompletedOrders() async {
    try {
      isLoadingCompleted.value = true;
      
      final uid = FireStoreUtils.getCurrentUid();
      final userResponse = await UserApi.getProfile(uid);
      final userId = userResponse['user']['id'];
      
      final response = await InterCityOrderApi.getCustomerOrders(userId);
      
      if (response['success'] == true && response['orders'] != null) {
        final List<dynamic> ordersData = response['orders'];
        
        // Filter completed orders
        completedOrders.value = ordersData
            .map((order) => _parseOrderFromApi(order))
            .where((order) => 
                order.status == 'completed' && 
                order.paymentStatus == true)
            .toList();
      }
    } catch (e) {
      log('❌ Error loading completed intercity orders: $e');
    } finally {
      isLoadingCompleted.value = false;
    }
  }

  /// Load cancelled orders
  Future<void> loadCancelledOrders() async {
    try {
      isLoadingCancelled.value = true;
      
      final uid = FireStoreUtils.getCurrentUid();
      final userResponse = await UserApi.getProfile(uid);
      final userId = userResponse['user']['id'];
      
      final response = await InterCityOrderApi.getCustomerOrders(userId);
      
      if (response['success'] == true && response['orders'] != null) {
        final List<dynamic> ordersData = response['orders'];
        
        // Filter cancelled orders
        cancelledOrders.value = ordersData
            .map((order) => _parseOrderFromApi(order))
            .where((order) => order.status == 'cancelled')
            .toList();
      }
    } catch (e) {
      log('❌ Error loading cancelled intercity orders: $e');
    } finally {
      isLoadingCancelled.value = false;
    }
  }

  /// Parse order from API response
  InterCityOrderModel _parseOrderFromApi(Map<String, dynamic> orderData) {
    InterCityOrderModel order = InterCityOrderModel();
    
    order.id = orderData['id']?.toString();
    order.userId = orderData['user_id']?.toString();
    order.driverId = orderData['driver_id']?.toString();
    order.serviceId = orderData['service_id']?.toString();
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
    
    // Parse and format created_at in local timezone
    if (orderData['created_at'] != null) {
      try {
        DateTime utcTime = DateTime.parse(orderData['created_at']);
        DateTime localTime = utcTime.toLocal();
        order.createdAt = '${localTime.year}-${localTime.month.toString().padLeft(2, '0')}-${localTime.day.toString().padLeft(2, '0')} ${localTime.hour.toString().padLeft(2, '0')}:${localTime.minute.toString().padLeft(2, '0')}';
      } catch (e) {
        log('❌ Error parsing created_at: $e');
        order.createdAt = orderData['created_at'];
      }
    }
    
    if (orderData['updated_at'] != null) {
      try {
        DateTime utcTime = DateTime.parse(orderData['updated_at']);
        DateTime localTime = utcTime.toLocal();
        order.updatedAt = '${localTime.year}-${localTime.month.toString().padLeft(2, '0')}-${localTime.day.toString().padLeft(2, '0')} ${localTime.hour.toString().padLeft(2, '0')}:${localTime.minute.toString().padLeft(2, '0')}';
      } catch (e) {
        log('❌ Error parsing updated_at: $e');
        order.updatedAt = orderData['updated_at'];
      }
    }
    
    return order;
  }
}



