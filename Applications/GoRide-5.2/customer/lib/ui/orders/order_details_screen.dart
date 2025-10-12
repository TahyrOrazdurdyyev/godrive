import 'dart:convert';
import 'dart:developer';
import 'package:cached_network_image/cached_network_image.dart';
// Google Fonts replaced with local fonts
import 'package:cloud_firestore/cloud_firestore.dart';
// Google Fonts replaced with local fonts
import 'package:customer/constant/collection_name.dart';
// Google Fonts replaced with local fonts
import 'package:customer/constant/constant.dart';
// Google Fonts replaced with local fonts
import 'package:customer/constant/send_notification.dart';
// Google Fonts replaced with local fonts
import 'package:customer/controller/order_details_controller.dart';
// Google Fonts replaced with local fonts
import 'package:customer/model/driver_rules_model.dart';
// Google Fonts replaced with local fonts
import 'package:customer/model/driver_user_model.dart';
// Google Fonts replaced with local fonts
import 'package:customer/model/order/driverId_accept_reject.dart';
// Google Fonts replaced with local fonts
import 'package:customer/model/order_model.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/app_colors.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/button_them.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/responsive.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/DarkThemeProvider.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/fire_store_utils.dart';
import 'package:customer/utils/order_api.dart';
import 'package:customer/utils/order_bid_api.dart';
import 'package:customer/utils/driver_api.dart';
// Google Fonts replaced with local fonts
import 'package:customer/widget/location_view.dart';
// Google Fonts replaced with local fonts
import 'package:flutter/material.dart';
// Google Fonts replaced with local fonts
import 'package:flutter_svg/flutter_svg.dart';
// Google Fonts replaced with local fonts
import 'package:get/get.dart';
// Google Fonts replaced with local fonts

import 'package:provider/provider.dart';
// Google Fonts replaced with local fonts

import '../../widget/driver_view.dart';
// Google Fonts replaced with local fonts

/// Helper function to get driver from bid data
Future<DriverUserModel?> _getDriverFromBid(Map<String, dynamic> bid) async {
  try {
    // Bid already contains driver info from JOIN
    DriverUserModel driver = DriverUserModel();
    driver.id = bid['driver_id'].toString();
    driver.fullName = bid['driver_name'];
    driver.profilePic = bid['driver_profile_pic'];
    driver.phoneNumber = bid['driver_phone'];
    driver.email = bid['driver_email'];
    driver.reviewsSum = bid['reviews_sum']?.toString() ?? '0';
    driver.reviewsCount = bid['reviews_count']?.toString() ?? '0';
    
    // Get full driver details from API for vehicle info
    final driverResponse = await DriverApi.getProfile(driver.id!);
    if (driverResponse['success'] == true && driverResponse['driver'] != null) {
      return DriverUserModel.fromJson(driverResponse['driver']);
    }
    
    return driver;
  } catch (e) {
    log('❌ Error getting driver from bid: $e');
    return null;
  }
}

class OrderDetailsScreen extends StatelessWidget {
  const OrderDetailsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);

    return GetBuilder<OrderDetailsController>(
        init: OrderDetailsController(),
        builder: (controller) {
          return Scaffold(
            backgroundColor: AppColors.primary,
            appBar: AppBar(
              backgroundColor: AppColors.primary,
              title: Text("Ride Details".tr),
              leading: InkWell(
                  onTap: () {
                    Get.back();
                  },
                  child: const Icon(
                    Icons.arrow_back,
                  )),
            ),
            body: Column(
              children: [
                SizedBox(
                  height: Responsive.width(6, context),
                  width: Responsive.width(100, context),
                ),
                Expanded(
                  child: Container(
                    decoration: BoxDecoration(
                        color: themeChange.getThem() ? AppColors.darkGray : AppColors.gray,
                        borderRadius: const BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25))),
                    child: Padding(
                      padding: const EdgeInsets.only(top: 10),
                      child: controller.isLoading.value
                          ? Center(child: Constant.loader())
                          : Builder(
                              builder: (context) {
                                // Use local variable for convenience
                                final orderModel = controller.orderModel.value;
                                return SingleChildScrollView(
                                  child: Column(
                                    children: [
                                Padding(
                                  padding: const EdgeInsets.all(15.0),
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Row(
                                        children: [
                                          Expanded(
                                            child: Text(
                                              orderModel.status.toString(),
                                              style: TextStyle(fontWeight: FontWeight.w500),
                                            ),
                                          ),
                                          Text(
                                            orderModel.status == Constant.ridePlaced
                                                ? Constant.amountShow(amount: orderModel.offerRate.toString())
                                                : Constant.amountShow(amount: orderModel.finalRate == null ? "0.0" : orderModel.finalRate.toString()),
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
                                      const SizedBox(
                                        height: 10,
                                      ),
                                      Container(
                                        decoration: BoxDecoration(
                                            color: themeChange.getThem() ? AppColors.darkContainerBorder : Colors.white, borderRadius: const BorderRadius.all(Radius.circular(10))),
                                        child: Padding(
                                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 14),
                                            child: Row(
                                              mainAxisAlignment: MainAxisAlignment.center,
                                              crossAxisAlignment: CrossAxisAlignment.center,
                                              children: [
                                                Expanded(
                                                  child: Row(
                                                    children: [
                                                      Text("OTP".tr, style: TextStyle()),
                                                      Text(" : ${orderModel.otp}", style: TextStyle(fontWeight: FontWeight.w600)),
                                                    ],
                                                  ),
                                                ),
                                                Text(Constant().formatTimestamp(orderModel.createdDate), style: TextStyle()),
                                              ],
                                            )),
                                      ),
                                      const SizedBox(
                                        height: 10,
                                      ),
                                      ButtonThem.buildButton(
                                        context,
                                        title: "Cancel".tr,
                                        btnHeight: 44,
                                        onPress: () async {
                                          try {
                                            await OrderApi.cancelOrder(
                                              orderId: orderModel.id ?? '',
                                              cancellationReason: 'Customer cancelled'
                                            );
                                            Get.back();
                                          } catch (e) {
                                            log('❌ Error cancelling order: $e');
                                          }
                                        },
                                      )
                                    ],
                                  ),
                                ),
                                FutureBuilder<Map<String, dynamic>>(
                                  future: OrderBidApi.getAcceptedBids(orderModel.id.toString()),
                                  builder: (context, bidSnapshot) {
                                    if (bidSnapshot.connectionState == ConnectionState.waiting) {
                                      return Constant.loader();
                                    }
                                    
                                    if (bidSnapshot.hasError || bidSnapshot.data == null) {
                                      return Center(child: Text("No driver Found".tr));
                                    }
                                    
                                    final response = bidSnapshot.data as Map<String, dynamic>;
                                    if (response['success'] != true || response['bids'] == null || (response['bids'] as List).isEmpty) {
                                      return Center(child: Text("No driver Found".tr));
                                    }
                                    
                                    final bids = response['bids'] as List;
                                    
                                    return Container(
                                      color: Theme.of(context).colorScheme.background,
                                      padding: const EdgeInsets.only(top: 10),
                                      child: ListView.builder(
                                        shrinkWrap: true,
                                        itemCount: bids.length,
                                        physics: const NeverScrollableScrollPhysics(),
                                        itemBuilder: (context, index) {
                                          final bid = bids[index];
                                          return FutureBuilder<DriverUserModel?>(
                                              future: _getDriverFromBid(bid),
                                              builder: (context, snapshot) {
                                                  switch (snapshot.connectionState) {
                                                    case ConnectionState.waiting:
                                                      return Constant.loader();
                                                    case ConnectionState.done:
                                                      if (snapshot.hasError) {
                                                        return Text(snapshot.error.toString());
                                                      } else {
                                                        DriverUserModel driverModel = snapshot.data!;
                                                        // Create DriverIdAcceptReject from bid data
                                                        DriverIdAcceptReject driverIdAcceptReject = DriverIdAcceptReject(
                                                          driverId: bid['driver_id'].toString(),
                                                          offerAmount: bid['offer_amount']?.toString() ?? '0',
                                                        );
                                                                    return Padding(
                                                                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                                                                      child: Container(
                                                                        decoration: BoxDecoration(
                                                                          color: themeChange.getThem() ? AppColors.darkContainerBackground : AppColors.containerBackground,
                                                                          borderRadius: const BorderRadius.all(Radius.circular(10)),
                                                                          border: Border.all(
                                                                              color: themeChange.getThem() ? AppColors.darkContainerBorder : AppColors.containerBorder, width: 0.5),
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
                                                                        child: Column(
                                                                          children: [
                                                                            Padding(
                                                                              padding: const EdgeInsets.all(8.0),
                                                                              child: DriverView(
                                                                                  driverId: driverModel.id.toString()),
                                                                            ),
                                                                            const SizedBox(
                                                                              height: 10,
                                                                            ),
                                                                            const Divider(),
                                                                            Padding(
                                                                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                                                                child: Row(
                                                                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                                                                  crossAxisAlignment: CrossAxisAlignment.center,
                                                                                  children: [
                                                                                    Row(
                                                                                      children: [
                                                                                        SvgPicture.asset(
                                                                                          'assets/icons/ic_car.svg',
                                                                                          width: 18,
                                                                                          color: themeChange.getThem() ? Colors.white : Colors.black,
                                                                                        ),
                                                                                        const SizedBox(
                                                                                          width: 10,
                                                                                        ),
                                                                                        Text(
                                                                                          Constant.localizationName(driverModel.vehicleInformation!.vehicleType),
                                                                                          style: TextStyle(fontWeight: FontWeight.w600),
                                                                                        )
                                                                                      ],
                                                                                    ),
                                                                                    Row(
                                                                                      children: [
                                                                                        SvgPicture.asset(
                                                                                          'assets/icons/ic_color.svg',
                                                                                          width: 18,
                                                                                          color: themeChange.getThem() ? Colors.white : Colors.black,
                                                                                        ),
                                                                                        const SizedBox(
                                                                                          width: 10,
                                                                                        ),
                                                                                        Text(
                                                                                          driverModel.vehicleInformation!.vehicleColor.toString(),
                                                                                          style: TextStyle(fontWeight: FontWeight.w600),
                                                                                        )
                                                                                      ],
                                                                                    ),
                                                                                    Row(
                                                                                      children: [
                                                                                        Image.asset(
                                                                                          'assets/icons/ic_number.png',
                                                                                          width: 18,
                                                                                          color: themeChange.getThem() ? Colors.white : Colors.black,
                                                                                        ),
                                                                                        const SizedBox(
                                                                                          width: 10,
                                                                                        ),
                                                                                        Text(
                                                                                          driverModel.vehicleInformation!.vehicleNumber.toString(),
                                                                                          style: TextStyle(fontWeight: FontWeight.w600),
                                                                                        )
                                                                                      ],
                                                                                    ),
                                                                                  ],
                                                                                )),
                                                                            const Divider(),
                                                                            const SizedBox(
                                                                              height: 10,
                                                                            ),
                                                                            driverModel.vehicleInformation!.driverRules == null
                                                                                ? const SizedBox()
                                                                                : ListView.builder(
                                                                                    shrinkWrap: true,
                                                                                    itemCount: driverModel.vehicleInformation!.driverRules!.length,
                                                                                    itemBuilder: (context, index) {
                                                                                      DriverRulesModel driverRules = driverModel.vehicleInformation!.driverRules![index];
                                                                                      return Padding(
                                                                                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                                                                                        child: Row(
                                                                                          children: [
                                                                                            CachedNetworkImage(
                                                                                              imageUrl: driverRules.image.toString(),
                                                                                              fit: BoxFit.fill,
                                                                                              height: Responsive.width(4, context),
                                                                                              width: Responsive.width(4, context),
                                                                                              placeholder: (context, url) => Constant.loader(),
                                                                                              errorWidget: (context, url, error) => Image.network(Constant.userPlaceHolder),
                                                                                            ),
                                                                                            const SizedBox(
                                                                                              width: 10,
                                                                                            ),
                                                                                            Text(Constant.localizationName(driverRules.name))
                                                                                          ],
                                                                                        ),
                                                                                      );
                                                                                    },
                                                                                  ),
                                                                            const SizedBox(
                                                                              height: 10,
                                                                            ),
                                                                            Padding(
                                                                              padding: const EdgeInsets.all(8.0),
                                                                              child: Row(
                                                                                children: [
                                                                                  Expanded(
                                                                                    child: ButtonThem.buildBorderButton(
                                                                                      context,
                                                                                      title: "Reject".tr,
                                                                                      btnHeight: 45,
                                                                                      iconVisibility: false,
                                                                                      onPress: () async {
                                                                                        List<dynamic> rejectDriverId = [];
                                                                                        if (controller.orderModel.value.rejectedDriverId != null) {
                                                                                          rejectDriverId = controller.orderModel.value.rejectedDriverId!;
                                                                                        } else {
                                                                                          rejectDriverId = [];
                                                                                        }
                                                                                        rejectDriverId.add(driverModel.id);

                                                                                        List<dynamic> acceptDriverId = [];
                                                                                        if (controller.orderModel.value.acceptedDriverId != null) {
                                                                                          acceptDriverId = controller.orderModel.value.acceptedDriverId!;
                                                                                        } else {
                                                                                          acceptDriverId = [];
                                                                                        }

                                                                                        print("===>");
                                                                                        print(acceptDriverId);
                                                                                        acceptDriverId.remove(driverModel.id);

                                                                                        controller.orderModel.value.rejectedDriverId = rejectDriverId;
                                                                                        controller.orderModel.value.acceptedDriverId = acceptDriverId;

                                                                                        await SendNotification.sendOneNotification(
                                                                                            token: driverModel.fcmToken.toString(),
                                                                                            title: 'Ride Canceled'.tr,
                                                                                            body: 'The passenger has canceled the ride. No action is required from your end.'.tr,
                                                                                            payload: {});
                                                                                        
                                                                                        // Update order via API
                                                                                        await OrderApi.updateOrder(
                                                                                          orderId: controller.orderModel.value.id!,
                                                                                          data: {
                                                                                            'rejected_driver_ids': jsonEncode(rejectDriverId),
                                                                                            'accepted_driver_ids': jsonEncode(acceptDriverId),
                                                                                          },
                                                                                        );
                                                                                      },
                                                                                    ),
                                                                                  ),
                                                                                  const SizedBox(
                                                                                    width: 10,
                                                                                  ),
                                                                                  Expanded(
                                                                                    child: ButtonThem.buildButton(
                                                                                      context,
                                                                                      title: "Accept".tr,
                                                                                      btnHeight: 45,
                                                                                      onPress: () async {
                                                                                        orderModel.acceptedDriverId = [];
                                                                                        orderModel.driverId = driverIdAcceptReject.driverId.toString();
                                                                                        orderModel.status = Constant.rideActive;
                                                                                        orderModel.finalRate = driverIdAcceptReject.offerAmount;
                                                                                        orderModel.vehicleInformation = driverModel.vehicleInformation;

                                                                                        if (orderModel.isAcSelected == true) {
                                                                                          orderModel.acNonAcCharges = driverModel.vehicleInformation?.acPerKmRate;
                                                                                        } else {
                                                                                          orderModel.acNonAcCharges = driverModel.vehicleInformation?.nonAcPerKmRate;
                                                                                        }
                                                                                        
                                                                                        // Update order via API
                                                                                        await OrderApi.updateOrder(
                                                                                          orderId: orderModel.id!,
                                                                                          data: {
                                                                                            'driver_id': orderModel.driverId,
                                                                                            'status': orderModel.status,
                                                                                            'final_rate': orderModel.finalRate,
                                                                                            'accepted_driver_ids': jsonEncode([]),
                                                                                            'ac_non_ac_charges': orderModel.acNonAcCharges,
                                                                                            'vehicle_information_data': jsonEncode(driverModel.vehicleInformation?.toJson() ?? {}),
                                                                                          },
                                                                                        );

                                                                                        await SendNotification.sendOneNotification(
                                                                                            token: driverModel.fcmToken.toString(),
                                                                                            title: 'Ride Confirmed'.tr,
                                                                                            body:
                                                                                                'Your ride request has been accepted by the passenger. Please proceed to the pickup location.'
                                                                                                    .tr,
                                                                                            payload: {});
                                                                                        Get.back();
                                                                                      },
                                                                                    ),
                                                                                  )
                                                                                ],
                                                                              ),
                                                                            )
                                                                          ],
                                                                        ),
                                                                      ),
                                                                    );
                                                      }
                                                    default:
                                                      return Text('Error'.tr);
                                                  }
                                                });
                                        },
                                      ),
                                    );
                                  },
                                )
                              ],
                            ),
                          );
                              },
                            ),
                    ),
                  ),
                ),
              ],
            ),
          );
        });
  }
}
