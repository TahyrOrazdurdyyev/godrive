import 'dart:async';
import 'dart:developer';
import 'dart:math';
import 'package:driver/constant/collection_name.dart';
import 'package:driver/constant/constant.dart';
import 'package:driver/constant/show_toast_dialog.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/model/intercity_order_model.dart';
import 'package:driver/model/order_model.dart';
import 'package:driver/services/websocket_service.dart';
import 'package:driver/themes/app_colors.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/order_api.dart';
import 'package:driver/utils/driver_api.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_osm_plugin/flutter_osm_plugin.dart';
import 'package:flutter_polyline_points/flutter_polyline_points.dart';
import 'package:get/get.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';

class LiveTrackingController extends GetxController {
  GoogleMapController? mapController;
  final WebSocketService _wsService = WebSocketService();
  StreamSubscription? _orderSubscription;
  Timer? _pollingTimer;

  @override
  void onInit() {
    if (Constant.selectedMapType == 'osm') {
      ShowToastDialog.showLoader("Please wait");
      mapOsmController = MapController(initPosition: GeoPoint(latitude: 20.9153, longitude: -100.7439), useExternalTracking: false); //OSM
    }
    addMarkerSetup();
    getArgument();
    // playSound();
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

  getArgument() async {
    dynamic argumentData = Get.arguments;
    if (argumentData != null) {
      type.value = argumentData['type'];
      if (type.value == "orderModel") {
        OrderModel argumentOrderModel = argumentData['orderModel'];

        // Subscribe to WebSocket for real-time order updates
        await _subscribeToOrderUpdates(argumentOrderModel.id!);
        
        // Initial load
        await _fetchOrderData(argumentOrderModel.id!);
        
        // Poll every 5 seconds as backup
        _pollingTimer = Timer.periodic(const Duration(seconds: 5), (timer) {
          _fetchOrderData(argumentOrderModel.id!);
        });
      } else {
        InterCityOrderModel argumentOrderModel = argumentData['interCityOrderModel'];
        intercityOrderModel.value = argumentOrderModel;
        
        // Subscribe to WebSocket for intercity order updates
        _wsService.connect();
        _wsService.subscribeToChannel('intercity-orders.${argumentOrderModel.id}');
        _wsService.onEvent('InterCityOrderUpdated', (data) {
          if (data != null) {
            try {
              intercityOrderModel.value = InterCityOrderModel.fromJson(data);
              _updatePolylines(); // Update map when order changes
              if (intercityOrderModel.value.status == Constant.rideComplete) {
                Get.back();
              }
            } catch (e) {
              print('❌ Error parsing intercity order update: $e');
            }
          }
        });
        
        // Periodic refresh for driver location
        _pollingTimer = Timer.periodic(const Duration(seconds: 5), (timer) async {
          try {
            final response = await DriverApi.getProfile(argumentOrderModel.driverId);
            if (response['success'] == true && response['driver'] != null) {
              driverUserModel.value = DriverUserModel.fromJson(response['driver']);
              _updatePolylines();
            }
          } catch (e) {
            print('❌ Error refreshing driver data: $e');
          }
        });
        
        // Initial setup
        _updatePolylines();
      }
    }
    isLoading.value = false;
    update();
  }

  BitmapDescriptor? departureIcon;
  BitmapDescriptor? destinationIcon;
  BitmapDescriptor? driverIcon;

  void _updatePolylines() {
    if (intercityOrderModel.value.id != null && driverUserModel.value.location != null) {
      if (Constant.selectedMapType != 'osm') {
        if (intercityOrderModel.value.status == Constant.rideInProgress) {
          getPolyline(
              sourceLatitude: driverUserModel.value.location!.latitude,
              sourceLongitude: driverUserModel.value.location!.longitude,
              destinationLatitude: intercityOrderModel.value.destinationLocationLAtLng!.latitude,
              destinationLongitude: intercityOrderModel.value.destinationLocationLAtLng!.longitude);
        } else {
          getPolyline(
              sourceLatitude: driverUserModel.value.location!.latitude,
              sourceLongitude: driverUserModel.value.location!.longitude,
              destinationLatitude: intercityOrderModel.value.sourceLocationLAtLng!.latitude,
              destinationLongitude: intercityOrderModel.value.sourceLocationLAtLng!.longitude);
        }
      } else {
        if (intercityOrderModel.value.status == Constant.rideInProgress) {
          getOSMPolyline(
            GeoPoint(latitude: driverUserModel.value.location!.latitude!, longitude: driverUserModel.value.location!.longitude!),
            GeoPoint(
                latitude: intercityOrderModel.value.destinationLocationLAtLng!.latitude!, longitude: intercityOrderModel.value.destinationLocationLAtLng!.longitude!),
          );
          setOsmMarker(
            departure: GeoPoint(
              latitude: intercityOrderModel.value.sourceLocationLAtLng!.latitude ?? 0.0,
              longitude: intercityOrderModel.value.sourceLocationLAtLng!.longitude ?? 0.0,
            ),
            destination: GeoPoint(
                latitude: intercityOrderModel.value.destinationLocationLAtLng!.latitude ?? 0.0,
                longitude: intercityOrderModel.value.destinationLocationLAtLng!.longitude ?? 0.0),
          );
        } else {
          getOSMPolyline(
            GeoPoint(latitude: driverUserModel.value.location!.latitude!, longitude: driverUserModel.value.location!.longitude!),
            GeoPoint(latitude: intercityOrderModel.value.sourceLocationLAtLng!.latitude!, longitude: intercityOrderModel.value.sourceLocationLAtLng!.longitude!),
          );
          setOsmMarker(
            departure: GeoPoint(
              latitude: intercityOrderModel.value.sourceLocationLAtLng!.latitude ?? 0.0,
              longitude: intercityOrderModel.value.sourceLocationLAtLng!.longitude ?? 0.0,
            ),
            destination: GeoPoint(
              latitude: intercityOrderModel.value.destinationLocationLAtLng!.latitude ?? 0.0,
              longitude: intercityOrderModel.value.destinationLocationLAtLng!.longitude ?? 0.0,
            ),
          );
        }
      }
    }
  }

  void getPolyline({required double? sourceLatitude, required double? sourceLongitude, required double? destinationLatitude, required double? destinationLongitude}) async {
    if (sourceLatitude != null && sourceLongitude != null && destinationLatitude != null && destinationLongitude != null) {
      List<LatLng> polylineCoordinates = [];
      PolylineRequest polylineRequest = PolylineRequest(
        origin: PointLatLng(sourceLatitude, sourceLongitude),
        destination: PointLatLng(destinationLatitude, destinationLongitude),
        mode: TravelMode.driving,
      );
      PolylineResult result = await polylinePoints.getRouteBetweenCoordinates(
        googleApiKey: Constant.mapAPIKey,
        request: polylineRequest,
      );
      if (result.points.isNotEmpty) {
        for (var point in result.points) {
          polylineCoordinates.add(LatLng(point.latitude, point.longitude));
        }
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
    if (Constant.selectedMapType == 'google') {
      final Uint8List departure = await Constant().getBytesFromAsset('assets/images/pickup.png', 100);
      final Uint8List destination = await Constant().getBytesFromAsset('assets/images/dropoff.png', 100);
      final Uint8List driver = await Constant().getBytesFromAsset('assets/images/ic_cab.png', 50);
      departureIcon = BitmapDescriptor.fromBytes(departure);
      destinationIcon = BitmapDescriptor.fromBytes(destination);
      driverIcon = BitmapDescriptor.fromBytes(driver);
    } else {
      departureOsmIcon = Image.asset("assets/images/pickup.png", width: 30, height: 30); //OSM
      destinationOsmIcon = Image.asset("assets/images/dropoff.png", width: 30, height: 30); //OSM
      driverOsmIcon = Image.asset("assets/images/ic_cab.png", width: 80, height: 80); //OSM
    }
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

  //OSM
  late MapController mapOsmController;
  Rx<RoadInfo> roadInfo = RoadInfo().obs;
  Map<String, GeoPoint> osmMarkers = <String, GeoPoint>{};
  Image? departureOsmIcon; //OSM
  Image? destinationOsmIcon; //OSM
  Image? driverOsmIcon;

  void getOSMPolyline(
    GeoPoint location,
    GeoPoint destinationlocation,
  ) async {
    try {
      // GeoPoint destinationLocation;
      // if (type.value == "orderModel") {
      //   if (orderModel.value.status == Constant.rideInProgress) {
      //     destinationLocation =
      //         GeoPoint(latitude: orderModel.value.destinationLocationLAtLng!.latitude ?? 0, longitude: orderModel.value.destinationLocationLAtLng!.longitude ?? 0);
      //   } else {
      //     destinationLocation = GeoPoint(latitude: orderModel.value.sourceLocationLAtLng!.latitude ?? 0, longitude: orderModel.value.sourceLocationLAtLng!.longitude ?? 0);
      //   }
      // } else {
      //   if (type.value == "orderModel") {
      //     destinationLocation =
      //         GeoPoint(latitude: intercityOrderModel.value.destinationLocationLAtLng!.latitude ?? 0, longitude: intercityOrderModel.value.destinationLocationLAtLng!.latitude ?? 0);
      //   } else {
      //     destinationLocation =
      //         GeoPoint(latitude: intercityOrderModel.value.sourceLocationLAtLng!.latitude ?? 0, longitude: intercityOrderModel.value.sourceLocationLAtLng!.latitude ?? 0);
      //   }
      // }
      if (destinationlocation != null) {
        await mapOsmController.removeLastRoad();
        roadInfo.value = await mapOsmController.drawRoad(
          GeoPoint(latitude: location.latitude, longitude: location.longitude),
          destinationlocation,
          roadType: RoadType.car,
          roadOption: RoadOption(
            roadWidth: 15,
            roadColor: AppColors.primary,
            zoomInto: false,
          ),
        );
        mapOsmController.moveTo(
          GeoPoint(latitude: location.latitude, longitude: location.longitude),
          animate: true,
        );
      }
    } catch (e) {
      print('Error: $e');
    }
  }

  Future<void> updateOSMCameraLocation({required GeoPoint source, required GeoPoint destination}) async {
    BoundingBox bounds;

    if (source.latitude > destination.latitude && source.longitude > destination.longitude) {
      bounds = BoundingBox(
        north: source.latitude,
        south: destination.latitude,
        east: source.longitude,
        west: destination.longitude,
      );
    } else if (source.longitude > destination.longitude) {
      bounds = BoundingBox(
        north: destination.latitude,
        south: source.latitude,
        east: source.longitude,
        west: destination.longitude,
      );
    } else if (source.latitude > destination.latitude) {
      bounds = BoundingBox(
        north: source.latitude,
        south: destination.latitude,
        east: destination.longitude,
        west: source.longitude,
      );
    } else {
      bounds = BoundingBox(
        north: destination.latitude,
        south: source.latitude,
        east: destination.longitude,
        west: source.longitude,
      );
    }

    await mapOsmController.zoomToBoundingBox(bounds, paddinInPixel: 100);
  }

  setOsmMarker({required GeoPoint departure, required GeoPoint destination}) async {
    if (osmMarkers.containsKey('Source')) {
      await mapOsmController.removeMarker(osmMarkers['Source']!);
    }
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      await mapOsmController
          .addMarker(departure,
              markerIcon: MarkerIcon(iconWidget: departureOsmIcon),
              angle: pi / 3,
              iconAnchor: IconAnchor(
                anchor: Anchor.top,
              ))
          .then((v) {
        osmMarkers['Source'] = departure;
      });

      if (osmMarkers.containsKey('Destination')) {
        await mapOsmController.removeMarker(osmMarkers['Destination']!);
      }

      await mapOsmController
          .addMarker(destination,
              markerIcon: MarkerIcon(iconWidget: destinationOsmIcon),
              angle: pi / 3,
              iconAnchor: IconAnchor(
                anchor: Anchor.top,
              ))
          .then((v) {
        osmMarkers['Destination'] = destination;
      });
    });
  }

  /// Subscribe to WebSocket for order updates
  Future<void> _subscribeToOrderUpdates(String orderId) async {
    try {
      await _wsService.connect();
      final controller = await _wsService.subscribe('orders');
      
      _orderSubscription = controller.stream.listen((data) {
        log('📨 Order update received: $data');
        
        final event = data['event'] as String?;
        if (event == 'order.updated') {
          final orderData = data['data'];
          if (orderData != null && orderData['order'] != null) {
            final updatedOrder = OrderModel.fromJson(orderData['order']);
            if (updatedOrder.id == orderId) {
              _updateOrderData(updatedOrder);
            }
          }
        }
      });
    } catch (e) {
      log('❌ Error subscribing to order updates: $e');
    }
  }

  /// Fetch order data from API
  Future<void> _fetchOrderData(String orderId) async {
    try {
      final response = await OrderApi.getOrderById(int.parse(orderId));
      
      if (response['success'] == true && response['order'] != null) {
        final updatedOrder = OrderModel.fromJson(response['order']);
        _updateOrderData(updatedOrder);
      }
    } catch (e) {
      if (kDebugMode) {
        log('❌ Error fetching order data: $e');
      }
    }
  }

  /// Update order data and refresh polyline
  void _updateOrderData(OrderModel updatedOrder) {
    orderModel.value = updatedOrder;
    
    // Fetch driver data via API
    if (updatedOrder.driverId != null) {
      DriverApi.getProfile(updatedOrder.driverId!).then((response) {
        if (response['success'] == true && response['driver'] != null) {
          driverUserModel.value = DriverUserModel.fromJson(response['driver']);
          _updatePolyline();
        }
      }).catchError((e) {
        log('❌ Error fetching driver data: $e');
      });
    }
    
    // Check if ride is complete
    if (updatedOrder.status == Constant.rideComplete) {
      _cleanup();
      Get.back();
    }
  }

  /// Update polyline based on current order status
  void _updatePolyline() {
    if (Constant.selectedMapType != 'osm') {
      if (orderModel.value.status == Constant.rideInProgress) {
        getPolyline(
          sourceLatitude: driverUserModel.value.location!.latitude,
          sourceLongitude: driverUserModel.value.location!.longitude,
          destinationLatitude: orderModel.value.destinationLocationLAtLng!.latitude,
          destinationLongitude: orderModel.value.destinationLocationLAtLng!.longitude,
        );
      } else {
        getPolyline(
          sourceLatitude: driverUserModel.value.location!.latitude,
          sourceLongitude: driverUserModel.value.location!.longitude,
          destinationLatitude: orderModel.value.sourceLocationLAtLng!.latitude,
          destinationLongitude: orderModel.value.sourceLocationLAtLng!.longitude,
        );
      }
    } else {
      if (orderModel.value.status == Constant.rideInProgress) {
        getOSMPolyline(
          GeoPoint(latitude: driverUserModel.value.location!.latitude!, longitude: driverUserModel.value.location!.longitude!),
          GeoPoint(latitude: orderModel.value.destinationLocationLAtLng!.latitude!, longitude: orderModel.value.destinationLocationLAtLng!.longitude!),
        );
        setOsmMarker(
          departure: GeoPoint(latitude: orderModel.value.sourceLocationLAtLng?.latitude ?? 0.0, longitude: orderModel.value.sourceLocationLAtLng?.longitude ?? 0.0),
          destination: GeoPoint(latitude: orderModel.value.destinationLocationLAtLng?.latitude ?? 0.0, longitude: orderModel.value.destinationLocationLAtLng?.longitude ?? 0.0),
        );
      } else {
        getOSMPolyline(
          GeoPoint(latitude: driverUserModel.value.location!.latitude!, longitude: driverUserModel.value.location!.longitude!),
          GeoPoint(latitude: orderModel.value.sourceLocationLAtLng!.latitude!, longitude: orderModel.value.sourceLocationLAtLng!.longitude!),
        );
        setOsmMarker(
          departure: GeoPoint(latitude: orderModel.value.sourceLocationLAtLng?.latitude ?? 0.0, longitude: orderModel.value.sourceLocationLAtLng?.longitude ?? 0.0),
          destination: GeoPoint(latitude: orderModel.value.destinationLocationLAtLng!.latitude ?? 0.0, longitude: orderModel.value.destinationLocationLAtLng!.longitude ?? 0.0),
        );
      }
    }
  }

  /// Cleanup subscriptions and timers
  void _cleanup() {
    _orderSubscription?.cancel();
    _pollingTimer?.cancel();
    _wsService.disconnect();
  }

  @override
  void onClose() {
    _cleanup();
    super.onClose();
  }
}
