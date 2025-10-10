import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:driver/constant/collection_name.dart';
import 'package:driver/constant/constant.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/model/service_model.dart';
import 'package:driver/ui/intercity_screen/accepted_intercity_orders.dart';
import 'package:driver/ui/intercity_screen/active_intercity_order_screen.dart';
import 'package:driver/ui/intercity_screen/new_order_intercity_screen.dart';
import 'package:driver/ui/order_intercity_screen/order_intercity_screen.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/driver_api.dart';
import 'package:driver/utils/service_api.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

class HomeIntercityController extends GetxController {
  RxInt selectedIndex = 0.obs;
  List<Widget> widgetOptions = <Widget>[const NewOrderInterCityScreen(), const AcceptedIntercityOrders(), const ActiveIntercityOrderScreen(),const OrderIntercityScreen()];

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
  Rx<ServiceModel> selectedService = ServiceModel().obs;
  RxBool isLoading = true.obs;
  RxBool isDriverActive = true.obs; // Track if driver is approved (is_active = 1)

  getDriver() async {
    await FireStoreUtils.getDriverProfile(FireStoreUtils.getCurrentUid()).then((value) {
      driverModel.value = value!;
      isLoading.value = false;
    });

    if (driverModel.value.serviceId != null) {
      try {
        // Get services from Laravel API
        final servicesResponse = await ServiceApi.getAllServices();
        if (servicesResponse['success'] == true) {
          final services = (servicesResponse['services'] as List)
              .map((s) => ServiceModel.fromJson(s))
              .toList();
          for (var element in services) {
            if (element.id == driverModel.value.serviceId) {
              selectedService.value = element;
            }
          }
        }
      } catch (e) {
        print('❌ Error loading services: $e');
      }
    }
    
    checkDriverApprovalStatus();
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

  getActiveRide() {
    FirebaseFirestore.instance
        .collection(CollectionName.ordersIntercity)
        .where('driverId', isEqualTo: FireStoreUtils.getCurrentUid())
        .where('intercityServiceId', isNotEqualTo: "Kn2VEnPI3ikF58uK8YqY")
        .where('status', whereIn: [Constant.rideInProgress, Constant.rideActive])
        .snapshots()
        .listen((event) {
      isActiveValue.value = event.size;
    });
  }
}
