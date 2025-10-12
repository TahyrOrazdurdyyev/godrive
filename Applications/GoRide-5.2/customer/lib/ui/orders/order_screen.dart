import 'package:cloud_firestore/cloud_firestore.dart';
// Google Fonts replaced with local fonts
import 'package:customer/constant/collection_name.dart';
// Google Fonts replaced with local fonts
import 'package:customer/constant/constant.dart';
// Google Fonts replaced with local fonts
import 'package:customer/constant/show_toast_dialog.dart';
// Google Fonts replaced with local fonts
import 'package:customer/controller/timer_controller.dart';
// Google Fonts replaced with local fonts
import 'package:customer/model/driver_user_model.dart';
// Google Fonts replaced with local fonts
import 'package:customer/model/order_model.dart';
// Google Fonts replaced with local fonts
import 'package:customer/model/sos_model.dart';
// Google Fonts replaced with local fonts
import 'package:customer/model/user_model.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/app_colors.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/button_them.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/responsive.dart';
// Google Fonts replaced with local fonts
import 'package:customer/ui/chat_screen/chat_screen.dart';
// Google Fonts replaced with local fonts
import 'package:customer/ui/hold_timer/hold_timer_screen.dart';
// Google Fonts replaced with local fonts
import 'package:customer/ui/orders/complete_order_screen.dart';
// Google Fonts replaced with local fonts
import 'package:customer/ui/orders/live_tracking_screen.dart';
// Google Fonts replaced with local fonts
import 'package:customer/ui/orders/order_details_screen.dart';
// Google Fonts replaced with local fonts
import 'package:customer/ui/orders/payment_order_screen.dart';
// Google Fonts replaced with local fonts
import 'package:customer/ui/review/review_screen.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/DarkThemeProvider.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/fire_store_utils.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/utils.dart';
// Google Fonts replaced with local fonts
import 'package:customer/controller/order_screen_controller.dart';
import 'package:customer/utils/user_api.dart';
import 'package:customer/utils/driver_api.dart';
import 'package:customer/utils/order_api.dart';
import 'package:customer/utils/sos_api.dart';
import 'package:get/get.dart';
import 'dart:developer';
// Google Fonts replaced with local fonts
import 'package:customer/widget/driver_view.dart';
// Google Fonts replaced with local fonts
import 'package:customer/widget/location_view.dart';
// Google Fonts replaced with local fonts
import 'package:flutter/material.dart';
// Google Fonts replaced with local fonts
import 'package:get/get.dart';
// Google Fonts replaced with local fonts

import 'package:provider/provider.dart';
// Google Fonts replaced with local fonts
import 'package:share_plus/share_plus.dart';
// Google Fonts replaced with local fonts

class OrderScreen extends StatelessWidget {
  const OrderScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);
    return GetX<OrderScreenController>(
      init: OrderScreenController(),
      builder: (controller) {
        return Scaffold(
      backgroundColor: AppColors.primary,
      body: Column(
        children: [
          Container(
            height: Responsive.width(10, context),
            width: Responsive.width(100, context),
            color: AppColors.primary,
          ),
          Expanded(
            child: Container(
              decoration: BoxDecoration(
                  color: Theme.of(context).colorScheme.background, borderRadius: const BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25))),
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 10),
                child: Padding(
                  padding: const EdgeInsets.only(top: 10),
                  child: DefaultTabController(
                    length: 3,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        TabBar(
                          indicatorColor: AppColors.darkModePrimary,
                          tabs: [
                            Tab(
                                child: Text(
                              "Active Rides".tr,
                              textAlign: TextAlign.center,
                              style: TextStyle(),
                            )),
                            Tab(
                                child: Text(
                              "Completed Rides".tr,
                              textAlign: TextAlign.center,
                              style: TextStyle(),
                            )),
                            Tab(
                                child: Text(
                              "Canceled Rides".tr,
                              textAlign: TextAlign.center,
                              style: TextStyle(),
                            )),
                          ],
                        ),
                        Expanded(
                          child: TabBarView(
                            children: [
                              // Active Orders from API
                              controller.isLoadingActive.value
                                  ? Center(child: Constant.loader())
                                  : controller.activeOrders.isEmpty
                                      ? Center(child: Text("No active rides found".tr))
                                      : ListView.builder(
                                          itemCount: controller.activeOrders.length,
                                          scrollDirection: Axis.vertical,
                                          shrinkWrap: true,
                                          itemBuilder: (context, index) {
                                            OrderModel orderModel = controller.activeOrders[index];

                                            return InkWell(
                                              onTap: () {
                                                Get.to(const CompleteOrderScreen(), arguments: {
                                                  "orderModel": orderModel,
                                                });
                                              },
                                              child: Padding(
                                                padding: const EdgeInsets.all(10),
                                                child: Container(
                                                  decoration: BoxDecoration(
                                                    color: themeChange.getThem() ? AppColors.darkContainerBackground : AppColors.containerBackground,
                                                    borderRadius: const BorderRadius.all(Radius.circular(10)),
                                                    border: Border.all(color: themeChange.getThem() ? AppColors.darkContainerBorder : AppColors.containerBorder, width: 0.5),
                                                    boxShadow: themeChange.getThem()
                                                        ? null
                                                        : [
                                                            BoxShadow(
                                                              color: Colors.black.withOpacity(0.10),
                                                              blurRadius: 5,
                                                              offset: const Offset(0, 4), // changes position of shadow
                                                            ),
                                                          ],
                                                  ),
                                                  child: Padding(
                                                    padding: const EdgeInsets.all(12.0),
                                                    child: Column(
                                                      crossAxisAlignment: CrossAxisAlignment.start,
                                                      children: [
                                                        orderModel.status == Constant.rideComplete || orderModel.status == Constant.rideActive
                                                            ? const SizedBox()
                                                            : Row(
                                                                children: [
                                                                  Expanded(
                                                                    child: Text(
                                                                      orderModel.status.toString(),
                                                                      style: TextStyle(fontWeight: FontWeight.w500),
                                                                    ),
                                                                  ),
                                                                  Text(
                                                                    orderModel.status == Constant.ridePlaced
                                                                        ? Constant.amountShow(
                                                                            amount: double.parse(orderModel.offerRate.toString())
                                                                                .toStringAsFixed(Constant.currencyModel!.decimalDigits!))
                                                                        : Constant.amountShow(
                                                                            amount: double.parse(orderModel.finalRate.toString())
                                                                                .toStringAsFixed(Constant.currencyModel!.decimalDigits!)),
                                                                    style: TextStyle(fontWeight: FontWeight.bold),
                                                                  ),
                                                                ],
                                                              ),
                                                        orderModel.status == Constant.rideComplete || orderModel.status == Constant.rideActive
                                                            ? Padding(
                                                                padding: const EdgeInsets.symmetric(vertical: 10),
                                                                child: DriverView(
                                                                  driverId: orderModel.driverId.toString(),
                                                                ),
                                                              )
                                                            : Container(),
                                                        const SizedBox(
                                                          height: 10,
                                                        ),
                                                        LocationView(
                                                          sourceLocation: orderModel.sourceLocationName.toString(),
                                                          destinationLocation: orderModel.destinationLocationName.toString(),
                                                        ),
                                                        const SizedBox(
                                                          height: 5,
                                                        ),
                                                        orderModel.someOneElse != null
                                                            ? Container(
                                                                decoration: BoxDecoration(
                                                                    color: themeChange.getThem() ? AppColors.darkGray : AppColors.gray,
                                                                    borderRadius: const BorderRadius.all(Radius.circular(10))),
                                                                child: Padding(
                                                                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
                                                                    child: Row(
                                                                      mainAxisAlignment: MainAxisAlignment.center,
                                                                      crossAxisAlignment: CrossAxisAlignment.center,
                                                                      children: [
                                                                        Expanded(
                                                                          child: Row(
                                                                            children: [
                                                                              Text(orderModel.someOneElse!.fullName.toString().tr, style: TextStyle()),
                                                                              Text(orderModel.someOneElse!.contactNumber.toString().tr,
                                                                                  style: TextStyle(fontWeight: FontWeight.w600, fontSize: 12)),
                                                                            ],
                                                                          ),
                                                                        ),
                                                                        InkWell(
                                                                            onTap: () async {
                                                                              await Share.share(
                                                                                subject: 'Ride Booked'.tr,
                                                                                'Your ride is booked. and you enjoy this ride and here is a otp to conform this ride ${orderModel.otp}'
                                                                                    .tr,
                                                                              );
                                                                            },
                                                                            child: const Icon(Icons.share))
                                                                      ],
                                                                    )),
                                                              )
                                                            : const SizedBox(),
                                                        if (orderModel.acceptHoldTime != null && orderModel.status == Constant.rideHoldAccepted)
                                                          HoldTimerWidget(
                                                            acceptHoldTime: orderModel.acceptHoldTime!,
                                                            holdingMinuteCharge: orderModel.service!.holdingMinuteCharge.toString(),
                                                            holdingMinute: orderModel.service!.holdingMinute.toString(),
                                                            orderId: orderModel.id!,
                                                            orderModel: orderModel,
                                                          ),
                                                        Padding(
                                                          padding: const EdgeInsets.symmetric(vertical: 10),
                                                          child: Container(
                                                            decoration: BoxDecoration(
                                                                color: themeChange.getThem() ? AppColors.darkGray : AppColors.gray,
                                                                borderRadius: const BorderRadius.all(Radius.circular(10))),
                                                            child: Padding(
                                                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
                                                                child: Row(
                                                                  mainAxisAlignment: MainAxisAlignment.center,
                                                                  crossAxisAlignment: CrossAxisAlignment.center,
                                                                  children: [
                                                                    Expanded(
                                                                      child: orderModel.status == Constant.rideInProgress ||
                                                                              orderModel.status == Constant.ridePlaced ||
                                                                              orderModel.status == Constant.rideComplete
                                                                          ? Text(orderModel.status.toString())
                                                                          : Row(
                                                                              children: [
                                                                                Text("OTP".tr, style: TextStyle()),
                                                                                Text(" : ${orderModel.otp}", style: TextStyle(fontWeight: FontWeight.w600, fontSize: 12)),
                                                                              ],
                                                                            ),
                                                                    ),
                                                                    Text(Constant().formatTimestamp(orderModel.createdDate), style: TextStyle(fontSize: 12)),
                                                                  ],
                                                                )),
                                                          ),
                                                        ),
                                                        Visibility(
                                                            visible: orderModel.status == Constant.ridePlaced,
                                                            child: ButtonThem.buildButton(
                                                              context,
                                                              title: "View bids (${orderModel.acceptedDriverId != null ? orderModel.acceptedDriverId!.length.toString() : "0"})".tr,
                                                              btnHeight: 44,
                                                              onPress: () async {
                                                                Get.to(const OrderDetailsScreen(), arguments: {
                                                                  "orderModel": orderModel,
                                                                });
                                                                // paymentMethodDialog(context, controller, orderModel);
                                                              },
                                                            )),
                                                        Visibility(
                                                            visible: orderModel.status != Constant.ridePlaced,
                                                            child: Row(
                                                              children: [
                                                                Expanded(
                                                                  child: InkWell(
                                                                    onTap: () async {
                                                                      try {
                                                                        // Get customer data
                                                                        final uid = FireStoreUtils.getCurrentUid();
                                                                        final userResponse = await UserApi.getProfile(uid);
                                                                        final customer = UserModel.fromJson(userResponse['user']);
                                                                        
                                                                        // Get driver data
                                                                        final driverResponse = await DriverApi.getProfile(orderModel.driverId ?? '0');
                                                                        final driver = DriverUserModel.fromJson(driverResponse['driver']);

                                                                        Get.to(ChatScreens(
                                                                          driverId: driver.id,
                                                                          customerId: customer.id,
                                                                          customerName: customer.fullName,
                                                                        customerProfileImage: customer.profilePic,
                                                                        driverName: driver.fullName,
                                                                        driverProfileImage: driver.profilePic,
                                                                        orderId: orderModel.id,
                                                                        token: driver.fcmToken,
                                                                      ));
                                                                      } catch (e) {
                                                                        log('❌ Error loading chat data: $e');
                                                                      }
                                                                    },
                                                                    child: Container(
                                                                      height: 44,
                                                                      decoration: BoxDecoration(
                                                                          color: themeChange.getThem() ? AppColors.darkModePrimary : AppColors.primary,
                                                                          borderRadius: BorderRadius.circular(5)),
                                                                      child: Icon(Icons.chat, color: themeChange.getThem() ? Colors.black : Colors.white),
                                                                    ),
                                                                  ),
                                                                ),
                                                                const SizedBox(
                                                                  width: 10,
                                                                ),
                                                                Expanded(
                                                                  child: InkWell(
                                                                    onTap: () async {
                                                                      try {
                                                                        final driverResponse = await DriverApi.getProfile(orderModel.driverId ?? '0');
                                                                        final driver = DriverUserModel.fromJson(driverResponse['driver']);
                                                                        Constant.makePhoneCall("${driver.countryCode}${driver.phoneNumber}");
                                                                      } catch (e) {
                                                                        log('❌ Error loading driver: $e');
                                                                      }
                                                                    },
                                                                    child: Container(
                                                                      height: 44,
                                                                      decoration: BoxDecoration(
                                                                          color: themeChange.getThem() ? AppColors.darkModePrimary : AppColors.primary,
                                                                          borderRadius: BorderRadius.circular(5)),
                                                                      child: Icon(Icons.call, color: themeChange.getThem() ? Colors.black : Colors.white),
                                                                    ),
                                                                  ),
                                                                ),
                                                                const SizedBox(
                                                                  width: 10,
                                                                ),
                                                                Expanded(
                                                                  child: InkWell(
                                                                    onTap: () async {
                                                                      if (Constant.mapType == "inappmap") {
                                                                        if (orderModel.status == Constant.rideActive || orderModel.status == Constant.rideInProgress) {
                                                                          Get.to(const LiveTrackingScreen(), arguments: {
                                                                            "orderModel": orderModel,
                                                                            "type": "orderModel",
                                                                          });
                                                                        }
                                                                      } else {
                                                                        Utils.redirectMap(
                                                                            latitude: orderModel.destinationLocationLAtLng!.latitude!,
                                                                            longLatitude: orderModel.destinationLocationLAtLng!.longitude!,
                                                                            name: orderModel.destinationLocationName.toString());
                                                                      }
                                                                    },
                                                                    child: Container(
                                                                      height: 44,
                                                                      decoration: BoxDecoration(
                                                                          color: themeChange.getThem() ? AppColors.darkModePrimary : AppColors.primary,
                                                                          borderRadius: BorderRadius.circular(5)),
                                                                      child: Icon(Icons.map, color: themeChange.getThem() ? Colors.black : Colors.white),
                                                                    ),
                                                                  ),
                                                                )
                                                              ],
                                                            )),
                                                        const SizedBox(
                                                          height: 10,
                                                        ),
                                                        Visibility(
                                                            visible: orderModel.status == Constant.rideInProgress ||
                                                                orderModel.status == Constant.rideHold ||
                                                                orderModel.status == Constant.rideHoldAccepted,
                                                            child: ButtonThem.buildButton(
                                                              context,
                                                              title: "SOS".tr,
                                                              btnHeight: 44,
                                                              onPress: () async {
                                                                try {
                                                                  // Check if SOS already exists for this order
                                                                  final response = await SosApi.getByOrder(
                                                                    orderId: orderModel.id.toString(),
                                                                    orderType: 'city',
                                                                  );
                                                                  
                                                                  if (response['success'] == true && response['sos'] != null) {
                                                                    ShowToastDialog.showToast("Your request is ${response['sos']['status']}");
                                                                  }
                                                                } catch (e) {
                                                                  // SOS doesn't exist, create new one
                                                                  try {
                                                                    final uid = FireStoreUtils.getCurrentUid();
                                                                    final userResponse = await UserApi.getProfile(uid);
                                                                    
                                                                    if (userResponse['success'] == true && userResponse['user'] != null) {
                                                                      await SosApi.create(
                                                                        userId: userResponse['user']['id'],
                                                                        orderType: 'city',
                                                                        orderId: orderModel.id,
                                                                        status: 'initiated',
                                                                      );
                                                                      ShowToastDialog.showToast("SOS initiated successfully");
                                                                    }
                                                                  } catch (createError) {
                                                                    log('❌ Error creating SOS: $createError');
                                                                    ShowToastDialog.showToast("Failed to initiate SOS");
                                                                  }
                                                                }
                                                              },
                                                            )),
                                                        orderModel.status == Constant.rideInProgress
                                                            ? const SizedBox(
                                                                height: 10,
                                                              )
                                                            : SizedBox.shrink(),
                                                        Visibility(
                                                            visible: orderModel.status == Constant.rideInProgress &&
                                                                (orderModel.totalHoldingCharges == null || orderModel.totalHoldingCharges == "0.0"),
                                                            child: ButtonThem.buildButton(
                                                              context,
                                                              title: "HOLD".tr,
                                                              btnHeight: 44,
                                                              onPress: () async {
                                                                showDialog(
                                                                  context: context,
                                                                  builder: (BuildContext context) {
                                                                    return AlertDialog(
                                                                      title: Text('Are you sure you want to hold the ride?'.tr),
                                                                      actions: [
                                                                        SizedBox(
                                                                          height: 5,
                                                                        ),
                                                                        TextButton(
                                                                          onPressed: () async {
                                                                            Navigator.of(context).pop();
                                                                          },
                                                                          child: Container(
                                                                              height: 40,
                                                                              width: 70,
                                                                              color: Colors.black,
                                                                              child: Padding(
                                                                                padding: const EdgeInsets.only(top: 12.0),
                                                                                child: Text(
                                                                                  'No',
                                                                                  textAlign: TextAlign.center,
                                                                                  style: TextStyle(
                                                                                    color: Colors.white,
                                                                                  ),
                                                                                ),
                                                                              )),
                                                                        ),
                                                                        TextButton(
                                                                          onPressed: () async {
                                                                            try {
                                                                              ShowToastDialog.showLoader("Please wait...".tr);
                                                                              
                                                                              await OrderApi.updateOrderStatus(
                                                                                orderId: orderModel.id ?? '',
                                                                                status: 'hold'
                                                                              );
                                                                              
                                                                              ShowToastDialog.closeLoader();
                                                                              ShowToastDialog.showToast("Ride on Hold".tr);
                                                                              Get.back();
                                                                            } catch (e) {
                                                                              ShowToastDialog.closeLoader();
                                                                              log('❌ Error updating order: $e');
                                                                            }
                                                                          },
                                                                          child: Container(
                                                                              height: 40,
                                                                              width: 70,
                                                                              color: Colors.black,
                                                                              child: Padding(
                                                                                padding: const EdgeInsets.only(top: 12.0),
                                                                                child: Text('Yes'.tr, textAlign: TextAlign.center, style: TextStyle(color: Colors.white)),
                                                                              )),
                                                                        ),
                                                                      ],
                                                                    );
                                                                  },
                                                                );
                                                              },
                                                            )),
                                                        const SizedBox(
                                                          height: 10,
                                                        ),
                                                        Visibility(
                                                            visible: orderModel.status == Constant.rideComplete &&
                                                                (orderModel.paymentStatus == null || orderModel.paymentStatus == false),
                                                            child: ButtonThem.buildButton(
                                                              context,
                                                              title: "Pay".tr,
                                                              btnHeight: 44,
                                                              onPress: () async {
                                                                Get.to(const PaymentOrderScreen(), arguments: {
                                                                  "orderModel": orderModel,
                                                                });
                                                                // paymentMethodDialog(context, controller, orderModel);
                                                              },
                                                            )),
                                                      ],
                                                    ),
                                                  ),
                                                ),
                                              ),
                                            );
                                          }),
                              // Completed Orders from API
                              controller.isLoadingCompleted.value
                                  ? Center(child: Constant.loader())
                                  : controller.completedOrders.isEmpty
                                      ? Center(child: Text("No completed rides found".tr))
                                      : ListView.builder(
                                          itemCount: controller.completedOrders.length,
                                          scrollDirection: Axis.vertical,
                                          shrinkWrap: true,
                                          itemBuilder: (context, index) {
                                            OrderModel orderModel = controller.completedOrders[index];
                                            return Padding(
                                              padding: const EdgeInsets.all(10),
                                              child: Container(
                                                decoration: BoxDecoration(
                                                  color: themeChange.getThem() ? AppColors.darkContainerBackground : AppColors.containerBackground,
                                                  borderRadius: const BorderRadius.all(Radius.circular(10)),
                                                  border: Border.all(color: themeChange.getThem() ? AppColors.darkContainerBorder : AppColors.containerBorder, width: 0.5),
                                                  boxShadow: themeChange.getThem()
                                                      ? null
                                                      : [
                                                          BoxShadow(
                                                            color: Colors.black.withOpacity(0.10),
                                                            blurRadius: 5,
                                                            offset: const Offset(0, 4), // changes position of shadow
                                                          ),
                                                        ],
                                                ),
                                                child: InkWell(
                                                    onTap: () {
                                                      Get.to(const CompleteOrderScreen(), arguments: {
                                                        "orderModel": orderModel,
                                                      });
                                                    },
                                                    child: Padding(
                                                      padding: const EdgeInsets.all(12.0),
                                                      child: Column(
                                                        crossAxisAlignment: CrossAxisAlignment.start,
                                                        children: [
                                                          DriverView(
                                                            driverId: orderModel.driverId.toString(),
                                                          ),
                                                          const Padding(
                                                            padding: EdgeInsets.symmetric(vertical: 4),
                                                            child: Divider(
                                                              thickness: 1,
                                                            ),
                                                          ),
                                                          LocationView(
                                                            sourceLocation: orderModel.sourceLocationName.toString(),
                                                            destinationLocation: orderModel.destinationLocationName.toString(),
                                                          ),
                                                          Padding(
                                                            padding: const EdgeInsets.symmetric(vertical: 14),
                                                            child: Container(
                                                              decoration: BoxDecoration(
                                                                  color: themeChange.getThem() ? AppColors.darkGray : AppColors.gray,
                                                                  borderRadius: const BorderRadius.all(Radius.circular(10))),
                                                              child: Padding(
                                                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 12),
                                                                  child: Center(
                                                                    child: Row(
                                                                      children: [
                                                                        Expanded(
                                                                            child: Text(orderModel.status.toString(), style: TextStyle(fontWeight: FontWeight.w500))),
                                                                        Text(Constant().formatTimestamp(orderModel.createdDate), style: TextStyle()),
                                                                      ],
                                                                    ),
                                                                  )),
                                                            ),
                                                          ),
                                                          Row(
                                                            children: [
                                                              Expanded(
                                                                  child: ButtonThem.buildButton(
                                                                context,
                                                                title: "Review".tr,
                                                                btnHeight: 44,
                                                                onPress: () async {
                                                                  Get.to(const ReviewScreen(), arguments: {
                                                                    "type": "orderModel",
                                                                    "orderModel": orderModel,
                                                                  });
                                                                },
                                                              )),
                                                            ],
                                                          ),
                                                        ],
                                                      ),
                                                    )),
                                              ),
                                            );
                                          }),
                              // Cancelled Orders from API
                              controller.isLoadingCancelled.value
                                  ? Center(child: Constant.loader())
                                  : controller.cancelledOrders.isEmpty
                                      ? Center(child: Text("No cancelled rides found".tr))
                                      : ListView.builder(
                                          itemCount: controller.cancelledOrders.length,
                                          scrollDirection: Axis.vertical,
                                          shrinkWrap: true,
                                          itemBuilder: (context, index) {
                                            OrderModel orderModel = controller.cancelledOrders[index];
                                            return Padding(
                                              padding: const EdgeInsets.all(10),
                                              child: Container(
                                                decoration: BoxDecoration(
                                                  color: themeChange.getThem() ? AppColors.darkContainerBackground : AppColors.containerBackground,
                                                  borderRadius: const BorderRadius.all(Radius.circular(10)),
                                                  border: Border.all(color: themeChange.getThem() ? AppColors.darkContainerBorder : AppColors.containerBorder, width: 0.5),
                                                  boxShadow: themeChange.getThem()
                                                      ? null
                                                      : [
                                                          BoxShadow(
                                                            color: Colors.black.withOpacity(0.10),
                                                            blurRadius: 5,
                                                            offset: const Offset(0, 4), // changes position of shadow
                                                          ),
                                                        ],
                                                ),
                                                child: Padding(
                                                  padding: const EdgeInsets.all(12.0),
                                                  child: Column(
                                                    crossAxisAlignment: CrossAxisAlignment.start,
                                                    children: [
                                                      orderModel.status == Constant.rideComplete || orderModel.status == Constant.rideActive
                                                          ? const SizedBox()
                                                          : Row(
                                                              children: [
                                                                Expanded(
                                                                  child: Text(
                                                                    orderModel.status.toString(),
                                                                    style: TextStyle(fontWeight: FontWeight.w500),
                                                                  ),
                                                                ),
                                                                Text(
                                                                  Constant.amountShow(
                                                                      amount:
                                                                          double.parse(orderModel.offerRate.toString()).toStringAsFixed(Constant.currencyModel!.decimalDigits!)),
                                                                  style: TextStyle(fontWeight: FontWeight.bold),
                                                                ),
                                                              ],
                                                            ),
                                                      const SizedBox(
                                                        height: 10,
                                                      ),
                                                      LocationView(
                                                        sourceLocation: orderModel.sourceLocationName.toString(),
                                                        destinationLocation: orderModel.destinationLocationName.toString(),
                                                      ),
                                                      Padding(
                                                        padding: const EdgeInsets.symmetric(vertical: 14),
                                                        child: Container(
                                                          decoration: BoxDecoration(
                                                              color: themeChange.getThem() ? AppColors.darkGray : AppColors.gray,
                                                              borderRadius: const BorderRadius.all(Radius.circular(10))),
                                                          child: Padding(
                                                              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
                                                              child: Row(
                                                                mainAxisAlignment: MainAxisAlignment.center,
                                                                crossAxisAlignment: CrossAxisAlignment.center,
                                                                children: [
                                                                  Expanded(child: Text(orderModel.status.toString())),
                                                                  Text(Constant().formatTimestamp(orderModel.createdDate), style: TextStyle(fontSize: 12)),
                                                                ],
                                                              )),
                                                        ),
                                                      ),
                                                    ],
                                                  ),
                                                ),
                                              ),
                                            );
                                          }),
                            ],
                          ),
                        )
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
      },
    );
  }
}
