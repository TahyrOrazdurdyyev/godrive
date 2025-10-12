// Google Fonts replaced with local fonts
import 'package:customer/constant/constant.dart';
// Google Fonts replaced with local fonts
import 'package:customer/constant/send_notification.dart';
// Google Fonts replaced with local fonts
import 'package:customer/controller/intercity_accept_order_controller.dart';
// Google Fonts replaced with local fonts
import 'package:customer/model/driver_user_model.dart';
// Google Fonts replaced with local fonts
import 'package:customer/model/intercity_order_model.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/app_colors.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/button_them.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/responsive.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/DarkThemeProvider.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/driver_api.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/intercity_order_api.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/order_bid_api.dart';
// Google Fonts replaced with local fonts
import 'package:customer/widget/driver_view.dart';
// Google Fonts replaced with local fonts
import 'package:customer/widget/location_view.dart';
// Google Fonts replaced with local fonts
import 'package:flutter/material.dart';
// Google Fonts replaced with local fonts
import 'package:flutter_svg/flutter_svg.dart';
// Google Fonts replaced with local fonts
import 'package:get/get.dart';
// Google Fonts replaced with local fonts
import 'dart:developer';
import 'package:provider/provider.dart';
// Google Fonts replaced with local fonts

class InterCityAcceptOrderScreen extends StatelessWidget {
  const InterCityAcceptOrderScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);

    return GetBuilder<InterCityAcceptOrderController>(
        init: InterCityAcceptOrderController(),
        builder: (controller) {
          return Scaffold(
            backgroundColor: AppColors.primary,
            appBar: AppBar(
              backgroundColor: AppColors.primary,
              title:  Text("OutStation ride details".tr),
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
                  height: Responsive.width(8, context),
                  width: Responsive.width(100, context),
                ),
                Expanded(
                  child: Container(
                    decoration:
                        BoxDecoration(color: themeChange.getThem() ? AppColors.darkGray : AppColors.gray, borderRadius: BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25))),
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 10),
                      child: Padding(
                        padding: const EdgeInsets.only(top: 10),
                        child: Obx(() {
                          if (controller.isLoading.value) {
                            return Constant.loader();
                          }

                          InterCityOrderModel orderModel = controller.orderModel.value;
                          return Column(
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
                                                : Constant.amountShow(amount: orderModel.finalRate == null?"0.0":orderModel.finalRate.toString()),
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
                                        decoration:
                                            BoxDecoration(color: themeChange.getThem() ? AppColors.darkContainerBorder : Colors.white, borderRadius: const BorderRadius.all(Radius.circular(10))),
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
                                            await InterCityOrderApi.cancelIntercityOrder(orderModel.id!);
                                            Get.back();
                                          } catch (e) {
                                            log('❌ Cancel error: $e');
                                          }
                                        },
                                      )
                                    ],
                                  ),
                                ),
                                Expanded(
                                  child: controller.acceptedBids.isEmpty
                                      ?  Center(
                                          child: Text("No driver Found".tr),
                                        )
                                      : ListView.builder(
                                          shrinkWrap: true,
                                          itemCount: controller.acceptedBids.length,
                                          itemBuilder: (context, index) {
                                            final bid = controller.acceptedBids[index];
                                            // Parse driver from bid
                                            DriverUserModel driverModel = _getDriverFromBid(bid);
                                            
                                            return Padding(
                                                                      padding: const EdgeInsets.symmetric(horizontal: 10,vertical: 5),
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
                                                                        child: Column(
                                                                          children: [
                                                                            Padding(
                                                                              padding: const EdgeInsets.all(8.0),
                                                                              child: DriverView(driverId: driverModel.id.toString()),
                                                                            ),
                                                                            const SizedBox(
                                                                              height: 10,
                                                                            ),
                                                                            Container(
                                                                              decoration: BoxDecoration(color: themeChange.getThem() ? AppColors.darkGray : AppColors.gray),
                                                                              child: Padding(
                                                                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
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
                                                                                        try {
                                                                                          // Update bid status to rejected
                                                                                          await OrderBidApi.createOrUpdateBid(
                                                                                            orderId: orderModel.id!,
                                                                                            driverId: int.parse(driverModel.id!),
                                                                                            status: 'rejected',
                                                                                            orderType: 'intercity',
                                                                                          );
                                                                                          
                                                                                          await SendNotification.sendOneNotification(
                                                                                              token: driverModel.fcmToken.toString(),
                                                                                              title: 'Ride Canceled'.tr,
                                                                                              body: 'The passenger has canceled the ride. No action is required from your end.'.tr,
                                                                                              payload: {});
                                                                                          
                                                                                          // Refresh bids
                                                                                          controller.loadAcceptedBids();
                                                                                        } catch (e) {
                                                                                          log('❌ Reject error: $e');
                                                                                        }
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
                                                                                        try {
                                                                                          // Update intercity order via API
                                                                                          await InterCityOrderApi.updateIntercityOrder(
                                                                                            orderId: orderModel.id!,
                                                                                            data: {
                                                                                              'driver_id': driverModel.id,
                                                                                              'status': Constant.rideActive,
                                                                                              'final_rate': bid['offer_amount'],
                                                                                            },
                                                                                          );
                                                                                          
                                                                                          await SendNotification.sendOneNotification(
                                                                                              token: driverModel.fcmToken.toString(),
                                                                                              title: 'Ride Confirmed',
                                                                                              body: 'Your ride request has been accepted by the passenger. Please proceed to the pickup location.'.tr,
                                                                                              payload: {});
                                                                                          
                                                                                          Get.back();
                                                                                        } catch (e) {
                                                                                          log('❌ Accept error: $e');
                                                                                        }
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
                                          },
                                        ),
                                )
                              ],
                            );
                          }),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          );
        });
  }
  
  /// Helper to parse driver from bid
  DriverUserModel _getDriverFromBid(Map<String, dynamic> bid) {
    DriverUserModel driver = DriverUserModel();
    driver.id = bid['driver_id'].toString();
    driver.fullName = bid['driver_name'] ?? '';
    driver.profilePic = bid['driver_profile_pic'] ?? '';
    driver.phoneNumber = bid['driver_phone'] ?? '';
    driver.email = bid['driver_email'] ?? '';
    driver.reviewsSum = bid['reviews_sum']?.toString() ?? '0';
    driver.reviewsCount = bid['reviews_count']?.toString() ?? '0';
    // Vehicle information should be fetched separately if needed
    return driver;
  }
}
