import 'dart:developer';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:driver/constant/collection_name.dart';
import 'package:driver/constant/constant.dart';
import 'package:driver/constant/send_notification.dart';
import 'package:driver/constant/show_toast_dialog.dart';
import 'package:driver/controller/dash_board_controller.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/model/intercity_order_model.dart';
import 'package:driver/model/order/driverId_accept_reject.dart';
import 'package:driver/model/order/location_lat_lng.dart';
import 'package:driver/model/order/positions.dart';
import 'package:driver/ui/freight/accepted_freight_orders.dart';
import 'package:driver/ui/freight/active_freight_order_screen.dart';
import 'package:driver/ui/freight/new_orders_freight_screen.dart';
import 'package:driver/ui/freight/order_freight_screen.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/driver_api.dart';
import 'package:driver/utils/customer_api.dart';
import 'package:driver/utils/order_bid_api.dart';
import 'package:driver/utils/intercity_order_api.dart';
import 'package:driver/widget/geoflutterfire/src/geoflutterfire.dart';
import 'package:driver/widget/geoflutterfire/src/models/point.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:location/location.dart';

class FreightController extends GetxController {
  RxInt selectedIndex = 0.obs;
  List<Widget> widgetOptions = <Widget>[const NewOrderFreightScreen(), const AcceptedFreightOrders(), const ActiveFreightOrderScreen(),const OrderFreightScreen()];
  DashBoardController dashboardController = Get.put(DashBoardController());


  Rx<TextEditingController> whenController = TextEditingController().obs;
  Rx<TextEditingController> suggestedTimeController = TextEditingController().obs;
  DateTime? suggestedTime = DateTime.now();
  DateTime? dateAndTime = DateTime.now();
  RxString newAmount = "0.0".obs;
  Rx<TextEditingController> enterOfferRateController = TextEditingController().obs;


  void onItemTapped(int index) {
    selectedIndex.value = index;
  }


  @override
  void onInit() {
    // TODO: implement onInit
    getDriver();
    getActiveRide();
    // getLocation();
    super.onInit();
  }

  acceptOrder(InterCityOrderModel orderModel) async {
    if (double.parse(driverModel.value.walletAmount.toString()) >= double.parse(Constant.minimumAmountToWithdrawal)) {
      ShowToastDialog.showLoader("Please wait".tr);
      
      try {
        // Create bid via API
        await OrderBidApi.createOrUpdateBid(
          orderId: orderModel.id!,
          driverId: FireStoreUtils.getCurrentUid(),
          status: 'pending',
          offerAmount: double.parse(newAmount.value),
          driverNote: 'Suggested date: ${orderModel.whenDates}, time: ${DateFormat("HH:mm").format(suggestedTime!)}',
          orderType: 'intercity',
        );

        // Send notification to customer
        final customerResponse = await CustomerApi.getCustomerProfile(orderModel.userId.toString());
        if (customerResponse['success'] == true && customerResponse['customer'] != null) {
          final customer = customerResponse['customer'];
          if (customer['fcm_token'] != null) {
            await SendNotification.sendOneNotification(
              token: customer['fcm_token'].toString(),
              title: 'New Bids'.tr,
              body: 'Driver requested your ride.'.tr,
              payload: {}
            );
          }
        }

        // Update driver subscription if needed
        if (driverModel.value.subscriptionTotalOrders != "-1") {
          driverModel.value.subscriptionTotalOrders = (int.parse(driverModel.value.subscriptionTotalOrders.toString()) - 1).toString();
          await DriverApi.updateProfile(
            driverId: driverModel.value.id!,
            data: {'subscription_total_orders': driverModel.value.subscriptionTotalOrders},
          );
          getDriver();
        }

        ShowToastDialog.closeLoader();
        ShowToastDialog.showToast("Ride Accepted".tr);
        Get.back();
        selectedIndex.value = 1;
        
      } catch (e) {
        ShowToastDialog.closeLoader();
        log('❌ Accept order error: $e');
        ShowToastDialog.showToast("Failed to accept order".tr);
      }
    } else {
      ShowToastDialog.showToast(
          "You have to minimum ${Constant.amountShow(amount: Constant.minimumDepositToRideAccept)} wallet amount to Accept Order and place a bid".tr);
    }
  }
  Rx<DriverUserModel> driverModel = DriverUserModel().obs;
  RxBool isLoading = true.obs;

  getDriver() async {
    updateCurrentLocation();
    FireStoreUtils.fireStore.collection(CollectionName.driverUsers).doc(FireStoreUtils.getCurrentUid()).snapshots().listen((event) {
      if (event.exists) {
        driverModel.value = DriverUserModel.fromJson(event.data()!);
      }
    });
  }

  RxInt isActiveValue = 0.obs;

  getActiveRide() {
    FirebaseFirestore.instance
        .collection(CollectionName.ordersIntercity)
        .where('driverId', isEqualTo: FireStoreUtils.getCurrentUid())
        .where('status', whereIn: [Constant.rideInProgress, Constant.rideActive])
        .snapshots()
        .listen((event) {
      isActiveValue.value = event.size;
        });
  }

  Location location = Location();

  updateCurrentLocation() async {
    PermissionStatus permissionStatus = await location.hasPermission();
    if (permissionStatus == PermissionStatus.granted) {
      location.enableBackgroundMode(enable: true);
      location.changeSettings(accuracy: LocationAccuracy.high, distanceFilter: double.parse(Constant.driverLocationUpdate.toString()),interval: 2000);
      location.onLocationChanged.listen((locationData) async {
        print("------>");
        print(locationData);
        Constant.currentLocation = LocationLatLng(latitude: locationData.latitude, longitude: locationData.longitude);
        
        try {
          // Update location via API
          await DriverApi.updateLocation(
            driverId: driverModel.value.id!,
            latitude: locationData.latitude!,
            longitude: locationData.longitude!,
            rotation: locationData.heading ?? 0.0,
          );
        } catch (e) {
          log('❌ Update location error: $e');
        }
      });
    } else {
      location.requestPermission().then((permissionStatus) {
        if (permissionStatus == PermissionStatus.granted) {
          location.enableBackgroundMode(enable: true);
          location.changeSettings(accuracy: LocationAccuracy.high, distanceFilter: double.parse(Constant.driverLocationUpdate.toString()),interval: 2000);
          location.onLocationChanged.listen((locationData) async {
            Constant.currentLocation = LocationLatLng(latitude: locationData.latitude, longitude: locationData.longitude);

            try {
              // Update location via API
              await DriverApi.updateLocation(
                driverId: driverModel.value.id!,
                latitude: locationData.latitude!,
                longitude: locationData.longitude!,
                rotation: locationData.heading ?? 0.0,
              );
            } catch (e) {
              log('❌ Update location error: $e');
            }
          });
        }
      });
    }
    isLoading.value = false;
    update();
  }

// Location location = Location();
// RxBool isLocation = false.obs;
//
// getLocation() async {
//   bool serviceEnabled;
//   PermissionStatus permissionGranted;
//
//   serviceEnabled = await location.serviceEnabled();
//   if (!serviceEnabled) {
//     serviceEnabled = await location.requestService();
//     if (!serviceEnabled) {
//       return;
//     }
//   }
//
//   permissionGranted = await location.hasPermission();
//   if (permissionGranted == PermissionStatus.denied) {
//     permissionGranted = await location.requestPermission();
//     if (permissionGranted != PermissionStatus.granted) {
//       return;
//     }
//   }
//
//   await location.getLocation().then((value) {
//     print("location-->${value.toString()}");
//     Constant.currentLocation = value;
//     isLocation.value = true;
//     update();
//   });
// }
}
