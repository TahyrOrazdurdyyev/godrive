import 'dart:math';
import 'package:customer/constant/collection_name.dart';
import 'package:customer/constant/constant.dart';
import 'package:customer/constant/show_toast_dialog.dart';
import 'package:customer/model/driver_user_model.dart';
import 'package:customer/model/intercity_order_model.dart';
import 'package:customer/model/order_model.dart';
import 'package:customer/model/location_lat_lng.dart';
import 'package:customer/themes/app_colors.dart';
import 'package:customer/utils/fire_store_utils.dart';
import 'package:customer/utils/order_api.dart';
import 'package:customer/utils/intercity_order_api.dart';
import 'package:customer/utils/driver_api.dart';
import 'package:customer/services/websocket_service.dart';
import 'dart:async';
import 'dart:developer';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_polyline_points/flutter_polyline_points.dart';
import 'package:get/get.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';

class LiveTrackingController extends GetxController {
  GoogleMapController? mapController;

  @override
  void onInit() {
    addMarkerSetup();
    getArgument();
    super.onInit();
  }

  @override
  void onClose() {
    ShowToastDialog.closeLoader();
    super.onClose();
  }

  Rx<DriverUserModel> driverUserModel = DriverUserModel().obs;
  Rx<OrderModel> orderModel = OrderModel().obs;
  Rx<InterCityOrderModel> intercityOrderModel = InterCityOrderModel().obs;

  RxBool isLoading = true.obs;
  RxString type = "".obs;

  Timer? _refreshTimer;
  final WebSocketService _wsService = WebSocketService();

  @override
  void dispose() {
    _refreshTimer?.cancel();
    _wsService.disconnect();
    super.dispose();
  }

  getArgument() async {
    print("=====argumentData====");
    dynamic argumentData = Get.arguments;
    print("=====argumentData====${argumentData}");
    if (argumentData != null) {
      type.value = argumentData['type'];

      if (type.value == "orderModel") {
        OrderModel argumentOrderModel = argumentData['orderModel'];
        orderModel.value = argumentOrderModel;
        
        // Connect to WebSocket and subscribe to order updates
        _wsService.connect();
        _wsService.subscribeToChannel('orders', (event, data) {
          log('📦 Order update event in live tracking: $event');
          if (event == 'order.updated' && data != null) {
            try {
              if (data['order'] != null && data['order']['id'].toString() == orderModel.value.id) {
                _refreshOrderData();
              }
            } catch (e) {
              log('❌ Error processing WebSocket event: $e');
            }
          }
        });
        
        // Periodic refresh every 3 seconds
        _refreshTimer = Timer.periodic(const Duration(seconds: 3), (timer) {
          _refreshOrderData();
        });
        
        // Initial load
        _refreshOrderData();
      } else {
        InterCityOrderModel argumentOrderModel = argumentData['interCityOrderModel'];
        intercityOrderModel.value = argumentOrderModel;
        
        // Connect to WebSocket and subscribe to intercity order updates
        _wsService.connect();
        _wsService.subscribeToChannel('intercity-orders', (event, data) {
          log('📦 InterCity order update event in live tracking: $event');
          if (event == 'intercity.order.updated' && data != null) {
            try {
              if (data['order'] != null && data['order']['id'].toString() == intercityOrderModel.value.id) {
                _refreshIntercityOrderData();
              }
            } catch (e) {
              log('❌ Error processing WebSocket event: $e');
            }
          }
        });
        
        // Periodic refresh every 3 seconds
        _refreshTimer = Timer.periodic(const Duration(seconds: 3), (timer) {
          _refreshIntercityOrderData();
        });
        
        // Initial load
        _refreshIntercityOrderData();
      }
    }
    isLoading.value = false;
    update();
  }

  BitmapDescriptor? departureIcon;
  BitmapDescriptor? destinationIcon;
  BitmapDescriptor? driverIcon;

  void getPolyline({required double? sourceLatitude, required double? sourceLongitude, required double? destinationLatitude, required double? destinationLongitude}) async {
    if (sourceLatitude != null && sourceLongitude != null && destinationLatitude != null && destinationLongitude != null) {
      List<LatLng> polylineCoordinates = [];
      PolylineRequest polylineRequest = PolylineRequest(
        origin: PointLatLng(sourceLatitude, sourceLongitude),
        destination: PointLatLng(destinationLatitude, destinationLongitude),
        mode: TravelMode.driving,
      );

      PolylineResult result = await polylinePoints.getRouteBetweenCoordinates(
        request: polylineRequest,
        googleApiKey: Constant.mapAPIKey,
      );
      if (result.points.isNotEmpty) {
        for (var point in result.points) {
          polylineCoordinates.add(LatLng(point.latitude, point.longitude));
        }
      } else {
        print(result.errorMessage.toString());
      }

      if (type.value == "orderModel") {
        addMarker(
            latitude: orderModel.value.sourceLocationLAtLng!.latitude,
            longitude: orderModel.value.sourceLocationLAtLng!.longitude,
            id: "Departure",
            descriptor: departureIcon!,
            rotation: 0.0);
        addMarker(
            latitude: orderModel.value.destinationLocationLAtLng!.latitude,
            longitude: orderModel.value.destinationLocationLAtLng!.longitude,
            id: "Destination",
            descriptor: destinationIcon!,
            rotation: 0.0);
        addMarker(
            latitude: driverUserModel.value.location!.latitude,
            longitude: driverUserModel.value.location!.longitude,
            id: "Driver",
            descriptor: driverIcon!,
            rotation: driverUserModel.value.rotation);

        _addPolyLine(polylineCoordinates);
      } else {
        addMarker(
            latitude: intercityOrderModel.value.sourceLocationLAtLng!.latitude,
            longitude: intercityOrderModel.value.sourceLocationLAtLng!.longitude,
            id: "Departure",
            descriptor: departureIcon!,
            rotation: 0.0);
        addMarker(
            latitude: intercityOrderModel.value.destinationLocationLAtLng!.latitude,
            longitude: intercityOrderModel.value.destinationLocationLAtLng!.longitude,
            id: "Destination",
            descriptor: destinationIcon!,
            rotation: 0.0);
        addMarker(
            latitude: driverUserModel.value.location!.latitude,
            longitude: driverUserModel.value.location!.longitude,
            id: "Driver",
            descriptor: driverIcon!,
            rotation: driverUserModel.value.rotation);

        _addPolyLine(polylineCoordinates);
      }
    }
  }

  RxMap<MarkerId, Marker> markers = <MarkerId, Marker>{}.obs;

  addMarker({required double? latitude, required double? longitude, required String id, required BitmapDescriptor descriptor, required double? rotation}) {
    MarkerId markerId = MarkerId(id);
    Marker marker = Marker(markerId: markerId, icon: descriptor, position: LatLng(latitude ?? 0.0, longitude ?? 0.0), rotation: rotation ?? 0.0);
    markers[markerId] = marker;
  }

  addMarkerSetup() async {
    final Uint8List departure = await Constant().getBytesFromAsset('assets/images/pickup.png', 100);
    final Uint8List destination = await Constant().getBytesFromAsset('assets/images/dropoff.png', 100);
    final Uint8List driver = await Constant().getBytesFromAsset('assets/images/ic_cab.png', 50);
    departureIcon = BitmapDescriptor.fromBytes(departure);
    destinationIcon = BitmapDescriptor.fromBytes(destination);
    driverIcon = BitmapDescriptor.fromBytes(driver);
  }

  RxMap<PolylineId, Polyline> polyLines = <PolylineId, Polyline>{}.obs;
  PolylinePoints polylinePoints = PolylinePoints();

  _addPolyLine(List<LatLng> polylineCoordinates) {
    PolylineId id = const PolylineId("poly");
    Polyline polyline = Polyline(
      polylineId: id,
      points: polylineCoordinates,
      consumeTapEvents: true,
      startCap: Cap.roundCap,
      width: 6,
    );
    polyLines[id] = polyline;
    updateCameraLocation(polylineCoordinates.first, polylineCoordinates.last, mapController);
  }

  Future<void> updateCameraLocation(
    LatLng source,
    LatLng destination,
    GoogleMapController? mapController,
  ) async {
    if (mapController == null) return;

    LatLngBounds bounds;

    if (source.latitude > destination.latitude && source.longitude > destination.longitude) {
      bounds = LatLngBounds(southwest: destination, northeast: source);
    } else if (source.longitude > destination.longitude) {
      bounds = LatLngBounds(southwest: LatLng(source.latitude, destination.longitude), northeast: LatLng(destination.latitude, source.longitude));
    } else if (source.latitude > destination.latitude) {
      bounds = LatLngBounds(southwest: LatLng(destination.latitude, source.longitude), northeast: LatLng(source.latitude, destination.longitude));
    } else {
      bounds = LatLngBounds(southwest: source, northeast: destination);
    }

    CameraUpdate cameraUpdate = CameraUpdate.newLatLngBounds(bounds, 10);

    return checkCameraLocation(cameraUpdate, mapController);
  }

  Future<void> checkCameraLocation(CameraUpdate cameraUpdate, GoogleMapController mapController) async {
    mapController.animateCamera(cameraUpdate);
    LatLngBounds l1 = await mapController.getVisibleRegion();
    LatLngBounds l2 = await mapController.getVisibleRegion();

    if (l1.southwest.latitude == -90 || l2.southwest.latitude == -90) {
      return checkCameraLocation(cameraUpdate, mapController);
    }
  }

  /// Refresh order data from API
  Future<void> _refreshOrderData() async {
    if (orderModel.value.id == null) return;

    try {
      final response = await OrderApi.getOrderById(orderModel.value.id!);

      if (response['success'] == true && response['order'] != null) {
        final orderData = response['order'];
        orderModel.value = _parseOrderFromApi(orderData);
        
        // Get driver data if driver is assigned
        if (orderModel.value.driverId != null) {
          await _refreshDriverData(orderModel.value.driverId!);
          
          // Update polyline based on order status
          if (driverUserModel.value.location != null) {
            if (orderModel.value.status == Constant.rideInProgress) {
              getPolyline(
                sourceLatitude: driverUserModel.value.location!.latitude,
                sourceLongitude: driverUserModel.value.location!.longitude,
                destinationLatitude: orderModel.value.destinationLocationLAtLng!.latitude,
                destinationLongitude: orderModel.value.destinationLocationLAtLng!.longitude
              );
            } else {
              getPolyline(
                sourceLatitude: driverUserModel.value.location!.latitude,
                sourceLongitude: driverUserModel.value.location!.longitude,
                destinationLatitude: orderModel.value.sourceLocationLAtLng!.latitude,
                destinationLongitude: orderModel.value.sourceLocationLAtLng!.longitude
              );
            }
          }
        }
        
        // Close screen if ride is complete
        if (orderModel.value.status == Constant.rideComplete) {
          Get.back();
        }
        
        update();
      }
    } catch (e) {
      log('❌ Error refreshing order: $e');
    }
  }

  /// Refresh intercity order data from API
  Future<void> _refreshIntercityOrderData() async {
    if (intercityOrderModel.value.id == null) return;

    try {
      final response = await InterCityOrderApi.getOrderById(intercityOrderModel.value.id!);

      if (response['success'] == true && response['order'] != null) {
        final orderData = response['order'];
        intercityOrderModel.value = _parseIntercityOrderFromApi(orderData);
        
        // Get driver data if driver is assigned
        if (intercityOrderModel.value.driverId != null) {
          await _refreshDriverData(intercityOrderModel.value.driverId!);
          
          // Update polyline based on order status
          if (driverUserModel.value.location != null) {
            if (intercityOrderModel.value.status == Constant.rideInProgress) {
              getPolyline(
                sourceLatitude: driverUserModel.value.location!.latitude,
                sourceLongitude: driverUserModel.value.location!.longitude,
                destinationLatitude: intercityOrderModel.value.destinationLocationLAtLng!.latitude,
                destinationLongitude: intercityOrderModel.value.destinationLocationLAtLng!.longitude
              );
            } else {
              getPolyline(
                sourceLatitude: driverUserModel.value.location!.latitude,
                sourceLongitude: driverUserModel.value.location!.longitude,
                destinationLatitude: intercityOrderModel.value.sourceLocationLAtLng!.latitude,
                destinationLongitude: intercityOrderModel.value.sourceLocationLAtLng!.longitude
              );
            }
          }
        }
        
        // Close screen if ride is complete
        if (intercityOrderModel.value.status == Constant.rideComplete) {
          Get.back();
        }
        
        update();
      }
    } catch (e) {
      log('❌ Error refreshing intercity order: $e');
    }
  }

  /// Refresh driver data from API
  Future<void> _refreshDriverData(String driverId) async {
    try {
      final response = await DriverApi.getProfile(driverId);

      if (response['success'] == true && response['driver'] != null) {
        driverUserModel.value = DriverUserModel.fromJson(response['driver']);
      }
    } catch (e) {
      log('❌ Error refreshing driver: $e');
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
    
    // Parse lat/lng
    if (orderData['source_lat'] != null && orderData['source_lng'] != null) {
      order.sourceLocationLAtLng = LocationLatLng(
        latitude: double.tryParse(orderData['source_lat'].toString()),
        longitude: double.tryParse(orderData['source_lng'].toString()),
      );
    }
    
    if (orderData['destination_lat'] != null && orderData['destination_lng'] != null) {
      order.destinationLocationLAtLng = LocationLatLng(
        latitude: double.tryParse(orderData['destination_lat'].toString()),
        longitude: double.tryParse(orderData['destination_lng'].toString()),
      );
    }
    
    order.distance = orderData['distance']?.toString();
    order.distanceType = orderData['distance_type'] ?? 'km';
    order.duration = orderData['duration'];
    order.offerRate = orderData['offer_rate']?.toString();
    order.finalRate = orderData['final_rate']?.toString();
    order.paymentType = orderData['payment_type'];
    order.paymentStatus = orderData['payment_status'] == 1 || orderData['payment_status'] == true;
    order.status = orderData['status'];
    order.otp = orderData['otp'];
    
    return order;
  }

  /// Parse intercity order from API response
  InterCityOrderModel _parseIntercityOrderFromApi(Map<String, dynamic> orderData) {
    InterCityOrderModel order = InterCityOrderModel();
    
    order.id = orderData['id']?.toString();
    order.userId = orderData['user_id']?.toString();
    order.driverId = orderData['driver_id']?.toString();
    order.intercityServiceId = orderData['intercity_service_id']?.toString();
    order.zoneId = orderData['zone_id']?.toString();
    order.sourceCity = orderData['source_city'];
    order.sourceLocationName = orderData['source_location_name'];
    order.destinationCity = orderData['destination_city'];
    order.destinationLocationName = orderData['destination_location_name'];
    
    // Parse lat/lng
    if (orderData['source_lat'] != null && orderData['source_lng'] != null) {
      order.sourceLocationLAtLng = LocationLatLng(
        latitude: double.tryParse(orderData['source_lat'].toString()),
        longitude: double.tryParse(orderData['source_lng'].toString()),
      );
    }
    
    if (orderData['destination_lat'] != null && orderData['destination_lng'] != null) {
      order.destinationLocationLAtLng = LocationLatLng(
        latitude: double.tryParse(orderData['destination_lat'].toString()),
        longitude: double.tryParse(orderData['destination_lng'].toString()),
      );
    }
    
    order.distance = orderData['distance']?.toString();
    order.distanceType = orderData['distance_type'] ?? 'km';
    order.offerRate = orderData['offer_rate']?.toString();
    order.finalRate = orderData['final_rate']?.toString();
    order.paymentType = orderData['payment_type'];
    order.paymentStatus = orderData['payment_status'] == 1 || orderData['payment_status'] == true;
    order.status = orderData['status'];
    order.otp = orderData['otp'];
    
    return order;
  }

}
