import 'dart:async';
import 'dart:developer';
import 'package:customer/model/intercity_order_model.dart';
import 'package:customer/utils/intercity_order_api.dart';
import 'package:customer/utils/order_bid_api.dart';
import 'package:customer/services/websocket_service.dart';
import 'package:get/get.dart';

class InterCityAcceptOrderController extends GetxController {
  Rx<InterCityOrderModel> orderModel = InterCityOrderModel().obs;
  RxBool isLoading = false.obs;
  RxList<Map<String, dynamic>> acceptedBids = <Map<String, dynamic>>[].obs;
  
  Timer? _refreshTimer;
  final WebSocketService _wsService = WebSocketService();

  @override
  void onInit() {
    super.onInit();
    getArgument();
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
      
      // Load accepted bids
      loadAcceptedBids();
    }
    update();
  }
  
  /// Connect to WebSocket for real-time order updates
  void _connectWebSocket() {
    if (orderModel.value.id == null) return;

    _wsService.connect();
    _wsService.subscribeToChannel('intercity-orders', (event, data) {
      log('📦 InterCity Order update event: $event');
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
      loadAcceptedBids();
    });
  }

  /// Refresh order data from API
  Future<void> refreshOrderData() async {
    if (orderModel.value.id == null) return;

    try {
      final response = await InterCityOrderApi.getIntercityOrderById(orderModel.value.id!);

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
  
  /// Load accepted bids for this order
  Future<void> loadAcceptedBids() async {
    if (orderModel.value.id == null) return;

    try {
      final response = await OrderBidApi.getAcceptedBids(
        orderId: orderModel.value.id!,
        orderType: 'intercity',
      );

      if (response['success'] == true && response['bids'] != null) {
        acceptedBids.value = List<Map<String, dynamic>>.from(response['bids']);
      }
    } catch (e) {
      log('❌ Error loading bids: $e');
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
    
    // Parse accepted_driver_ids if present
    if (orderData['accepted_driver_ids'] != null) {
      try {
        if (orderData['accepted_driver_ids'] is String) {
          // If it's a JSON string, parse it
          order.acceptedDriverId = List<String>.from(
            (orderData['accepted_driver_ids'] as String).split(',')
          );
        } else if (orderData['accepted_driver_ids'] is List) {
          order.acceptedDriverId = List<String>.from(orderData['accepted_driver_ids']);
        }
      } catch (e) {
        log('❌ Error parsing accepted_driver_ids: $e');
      }
    }

    return order;
  }
}
