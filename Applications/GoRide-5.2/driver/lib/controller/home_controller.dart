import 'dart:async';
import 'dart:developer';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:driver/constant/collection_name.dart';
import 'package:driver/constant/constant.dart';
import 'package:driver/controller/dash_board_controller.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/model/order/location_lat_lng.dart';
import 'package:driver/model/order/positions.dart';
import 'package:driver/model/order_model.dart';
import 'package:driver/ui/home_screens/accepted_orders.dart';
import 'package:driver/ui/home_screens/active_order_screen.dart';
import 'package:driver/ui/home_screens/new_orders_screen.dart';
import 'package:driver/ui/order_screen/order_screen.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/driver_api.dart';
import 'package:driver/utils/order_api.dart';
import 'package:driver/services/websocket_service.dart';
import 'package:driver/widget/geoflutterfire/src/geoflutterfire.dart';
import 'package:driver/widget/geoflutterfire/src/models/point.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:location/location.dart';

class HomeController extends GetxController {
  RxInt selectedIndex = 0.obs;
  List<Widget> widgetOptions = <Widget>[const NewOrderScreen(), const AcceptedOrders(), const ActiveOrderScreen(),const OrderScreen()];
  DashBoardController dashboardController = Get.put(DashBoardController());

  void onItemTapped(int index) {
    selectedIndex.value = index;
  }

  @override
  void onInit() {
    // TODO: implement onInit
    getDriver();
    getActiveRide();
    super.onInit();
  }

  Rx<DriverUserModel> driverModel = DriverUserModel().obs;
  RxBool isLoading = true.obs;
  RxBool isDriverActive = true.obs; // Track if driver is approved (is_active = 1)

  getDriver() async {
    try {
      final uid = FireStoreUtils.getCurrentUid();
      
      // Get driver profile from API
      final response = await DriverApi.getProfile(uid);
      
      if (response['success'] == true && response['driver'] != null) {
        driverModel.value = DriverUserModel.fromJson(response['driver']);
      }
      
      checkDriverApprovalStatus();
      updateCurrentLocation();
    } catch (e) {
      log('❌ Error getting driver: $e');
    }
  }
  
  // Check driver approval status via Laravel API
  checkDriverApprovalStatus() async {
    try {
      final uid = FireStoreUtils.getCurrentUid();
      final data = await DriverApi.checkStatus(uid);
      
      if (data['success'] == true) {
        isDriverActive.value = data['driver']['is_active'] == 1;
      }
    } catch (e) {
      print('Error checking driver status: $e');
      isDriverActive.value = true; // Default to true to avoid blocking on error
    }
  }

  RxInt isActiveValue = 0.obs;
  final WebSocketService _wsService = WebSocketService();
  StreamSubscription? _ordersSubscription;
  RxList<OrderModel> nearbyOrders = <OrderModel>[].obs;
  RxList<OrderModel> activeOrders = <OrderModel>[].obs;
  Timer? _refreshTimer;

  getActiveRide() async {
    // Get driver ID from MySQL via API
    try {
      final uid = FireStoreUtils.getCurrentUid();
      final driverResponse = await DriverApi.checkStatus(uid);
      
      if (driverResponse['success'] == true) {
        final driverId = driverResponse['driver']['id'];
        
        // Fetch active orders via API
        await _fetchActiveOrders(driverId);
        
        // Subscribe to WebSocket for real-time updates
        await _subscribeToOrderUpdates();
      }
    } catch (e) {
      log('❌ Error getting active rides: $e');
    }
  }

  Future<void> _fetchActiveOrders(int driverId) async {
    try {
      final response = await OrderApi.getDriverOrders(driverId);
      if (response['success'] == true) {
        final orders = (response['orders'] as List)
            .map((o) => OrderModel.fromJson(o))
            .where((o) => o.status == Constant.rideInProgress || o.status == Constant.rideActive)
            .toList();
        
        activeOrders.value = orders;
        isActiveValue.value = orders.length;
      }
    } catch (e) {
      log('❌ Error fetching active orders: $e');
    }
  }

  Future<void> _subscribeToOrderUpdates() async {
    try {
      await _wsService.connect();
      final controller = await _wsService.subscribe('drivers');
      
      _ordersSubscription = controller.stream.listen((data) {
        log('📨 Received order event: $data');
        
        final event = data['event'] as String?;
        if (event == 'order.created') {
          // New order created - refresh nearby orders
          _refreshNearbyOrders();
        } else if (event == 'order.updated') {
          // Order updated - refresh active orders
          final uid = FireStoreUtils.getCurrentUid();
          DriverApi.checkStatus(uid).then((driverResponse) {
            if (driverResponse['success'] == true) {
              _fetchActiveOrders(driverResponse['driver']['id']);
            }
          });
        }
      });
    } catch (e) {
      log('❌ Error subscribing to orders: $e');
    }
  }

  void _refreshNearbyOrders() {
    if (Constant.currentLocation != null) {
      OrderApi.getNearbyOrders(
        latitude: Constant.currentLocation!.latitude!,
        longitude: Constant.currentLocation!.longitude!,
        radius: 10.0,
      ).then((response) {
        if (response['success'] == true) {
          nearbyOrders.value = (response['orders'] as List)
              .map((o) => OrderModel.fromJson(o))
              .toList();
        }
      }).catchError((e) {
        log('❌ Error refreshing nearby orders: $e');
      });
    }
  }

  /// Fetch nearby orders (for manual refresh or initial load)
  Future<List<OrderModel>> fetchNearbyOrders() async {
    if (Constant.currentLocation == null) {
      return [];
    }

    try {
      final response = await OrderApi.getNearbyOrders(
        latitude: Constant.currentLocation!.latitude!,
        longitude: Constant.currentLocation!.longitude!,
        radius: 10.0,
      );

      if (response['success'] == true) {
        final orders = (response['orders'] as List)
            .map((o) => OrderModel.fromJson(o))
            .toList();
        nearbyOrders.value = orders;
        return orders;
      }
    } catch (e) {
      log('❌ Error fetching nearby orders: $e');
    }

    return [];
  }

  /// Stream of nearby orders (combines WebSocket updates with polling)
  Stream<List<OrderModel>> getNearbyOrdersStream() async* {
    // Initial load
    yield await fetchNearbyOrders();

    // Poll every 10 seconds as backup (in case WebSocket misses updates)
    _refreshTimer?.cancel();
    _refreshTimer = Timer.periodic(const Duration(seconds: 10), (timer) {
      fetchNearbyOrders();
    });

    // Listen to nearby orders changes
    await for (final orders in nearbyOrders.stream) {
      yield orders;
    }
  }

  @override
  void onClose() {
    _ordersSubscription?.cancel();
    _wsService.disconnect();
    _refreshTimer?.cancel();
    super.onClose();
  }

  Location location = Location();

  updateCurrentLocation() async {
    PermissionStatus permissionStatus = await location.hasPermission();
    if (permissionStatus == PermissionStatus.granted) {
      location.enableBackgroundMode(enable: true);
      location.changeSettings(accuracy: LocationAccuracy.high, distanceFilter: double.parse(Constant.driverLocationUpdate.toString()),interval: 2000);
      location.onLocationChanged.listen((locationData) {
        Constant.currentLocation = LocationLatLng(latitude: locationData.latitude, longitude: locationData.longitude);
        
        // Update location via API if driver is online
        if (driverModel.value.isOnline == true) {
          DriverApi.updateLocation(
            uid: FireStoreUtils.getCurrentUid(),
            latitude: locationData.latitude!,
            longitude: locationData.longitude!,
            rotation: locationData.heading,
          ).catchError((e) {
            log('❌ Error updating location: $e');
          });
        }
      });
    } else {
      location.requestPermission().then((permissionStatus) {
        if (permissionStatus == PermissionStatus.granted) {
          location.enableBackgroundMode(enable: true);
          location.changeSettings(accuracy: LocationAccuracy.high, distanceFilter: double.parse(Constant.driverLocationUpdate.toString()),interval: 2000);
          location.onLocationChanged.listen((locationData) async {
            Constant.currentLocation = LocationLatLng(latitude: locationData.latitude, longitude: locationData.longitude);

            // Update location via API if driver is online
            if (driverModel.value.isOnline == true) {
              DriverApi.updateLocation(
                uid: FireStoreUtils.getCurrentUid(),
                latitude: locationData.latitude!,
                longitude: locationData.longitude!,
                rotation: locationData.heading,
              ).catchError((e) {
                log('❌ Error updating location: $e');
              });
            }
          });
        }
      });
    }
    isLoading.value = false;
    update();
  }

}
