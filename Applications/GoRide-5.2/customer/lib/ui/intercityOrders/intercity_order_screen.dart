import 'package:customer/constant/constant.dart';
import 'package:customer/constant/show_toast_dialog.dart';
import 'package:customer/controller/intercity_order_screen_controller.dart';
import 'package:customer/model/intercity_order_model.dart';
import 'package:customer/themes/app_colors.dart';
import 'package:customer/themes/button_them.dart';
import 'package:customer/themes/responsive.dart';
import 'package:customer/ui/intercityOrders/intercity_complete_order_screen.dart';
import 'package:customer/ui/intercityOrders/intercity_payment_order_screen.dart';
import 'package:customer/ui/orders/live_tracking_screen.dart';
import 'package:customer/utils/DarkThemeProvider.dart';
import 'package:customer/utils/fire_store_utils.dart';
import 'package:customer/utils/user_api.dart';
import 'package:customer/utils/sos_api.dart';
import 'package:customer/widget/location_view.dart';
import 'package:customer/widget/driver_view.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:provider/provider.dart';
import 'dart:developer';

class InterCityOrderScreen extends StatelessWidget {
  const InterCityOrderScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);
    return GetX<InterCityOrderScreenController>(
        init: InterCityOrderScreenController(),
        builder: (controller) {
          return Scaffold(
            backgroundColor: AppColors.primary,
            body: Column(
              children: [
                Container(
                  height: Responsive.width(8, context),
                  width: Responsive.width(100, context),
                  color: AppColors.primary,
                ),
                Expanded(
                  child: Container(
                    decoration: BoxDecoration(
                        color: Theme.of(context).colorScheme.background,
                        borderRadius: const BorderRadius.only(
                            topLeft: Radius.circular(25),
                            topRight: Radius.circular(25))),
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
                                            ? Center(
                                                child: Text(
                                                    "No active rides found".tr))
                                            : ListView.builder(
                                                itemCount: controller
                                                    .activeOrders.length,
                                                scrollDirection: Axis.vertical,
                                                shrinkWrap: true,
                                                itemBuilder: (context, index) {
                                                  InterCityOrderModel
                                                      orderModel = controller
                                                          .activeOrders[index];

                                                  return InkWell(
                                                    onTap: () {
                                                      Get.to(
                                                          IntercityCompleteOrderScreen(),
                                                          arguments: {
                                                            "orderModel":
                                                                orderModel,
                                                          });
                                                    },
                                                    child: Padding(
                                                      padding:
                                                          const EdgeInsets.all(
                                                              10),
                                                      child: Container(
                                                        decoration:
                                                            BoxDecoration(
                                                          color: themeChange
                                                                  .getThem()
                                                              ? AppColors
                                                                  .darkContainerBackground
                                                              : AppColors
                                                                  .containerBackground,
                                                          borderRadius:
                                                              const BorderRadius
                                                                  .all(Radius
                                                                      .circular(
                                                                          10)),
                                                          border: Border.all(
                                                              color: themeChange
                                                                      .getThem()
                                                                  ? AppColors
                                                                      .darkContainerBorder
                                                                  : AppColors
                                                                      .containerBorder,
                                                              width: 0.5),
                                                          boxShadow: themeChange
                                                                  .getThem()
                                                              ? null
                                                              : [
                                                                  BoxShadow(
                                                                    color: Colors
                                                                        .black
                                                                        .withOpacity(
                                                                            0.10),
                                                                    blurRadius:
                                                                        5,
                                                                    offset: const Offset(
                                                                        0, 4),
                                                                  ),
                                                                ],
                                                        ),
                                                        child: Padding(
                                                          padding:
                                                              const EdgeInsets
                                                                  .all(8.0),
                                                          child: Column(
                                                            children: [
                                                              Row(
                                                                children: [
                                                                  Expanded(
                                                                    child: Text(
                                                                      orderModel
                                                                              .status
                                                                              .toString() ??
                                                                          '',
                                                                      style: TextStyle(
                                                                          color: orderModel.status == Constant.ridePlaced
                                                                              ? Colors.orange
                                                                              : orderModel.status == Constant.rideActive
                                                                                  ? Colors.blue
                                                                                  : orderModel.status == Constant.rideInProgress
                                                                                      ? Colors.green
                                                                                      : Colors.grey,
                                                                          fontWeight: FontWeight.w600),
                                                                    ),
                                                                  ),
                                                                  Text(
                                                                    "\$ ${orderModel.offerRate ?? '0'}",
                                                                    style:
                                                                        TextStyle(
                                                                      fontSize:
                                                                          18,
                                                                      fontWeight:
                                                                          FontWeight
                                                                              .w800,
                                                                    ),
                                                                  ),
                                                                ],
                                                              ),
                                                              const SizedBox(
                                                                  height: 5),
                                                              const Divider(
                                                                  thickness: 1),
                                                              const SizedBox(
                                                                  height: 5),
                                                              Row(
                                                                children: [
                                                                  Expanded(
                                                                    child: LocationView(
                                                                      sourceLocation: orderModel.sourceLocationName?.toString() ??
                                                                          '',
                                                                      destinationLocation: orderModel
                                                                              .destinationLocationName
                                                                              ?.toString() ??
                                                                          '',
                                                                    ),
                                                                  ),
                                                                ],
                                                              ),
                                                              Padding(
                                                                padding: const EdgeInsets
                                                                    .symmetric(
                                                                    vertical:
                                                                        10),
                                                                child:
                                                                    Container(
                                                                  decoration: BoxDecoration(
                                                                      color: themeChange.getThem()
                                                                          ? AppColors
                                                                              .darkGray
                                                                          : AppColors
                                                                              .gray,
                                                                      borderRadius: const BorderRadius.all(
                                                                          Radius.circular(
                                                                              10))),
                                                                  child: Padding(
                                                                      padding: const EdgeInsets.symmetric(
                                                                          horizontal:
                                                                              10,
                                                                          vertical:
                                                                              10),
                                                                      child:
                                                                          Row(
                                                                        mainAxisAlignment:
                                                                            MainAxisAlignment.center,
                                                                        crossAxisAlignment:
                                                                            CrossAxisAlignment.center,
                                                                        children: [
                                                                          Expanded(
                                                                            child:
                                                                                Text(orderModel.status.toString(), style: TextStyle()),
                                                                          ),
                                                                          Text(
                                                                              orderModel.createdAt ?? '',
                                                                              style: TextStyle(fontSize: 12)),
                                                                        ],
                                                                      )),
                                                                ),
                                                              ),
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
                                            ? Center(
                                                child: Text(
                                                    "No completed rides found"
                                                        .tr))
                                            : ListView.builder(
                                                itemCount: controller
                                                    .completedOrders.length,
                                                scrollDirection: Axis.vertical,
                                                shrinkWrap: true,
                                                itemBuilder: (context, index) {
                                                  InterCityOrderModel
                                                      orderModel = controller
                                                              .completedOrders[
                                                          index];
                                                  return Padding(
                                                    padding:
                                                        const EdgeInsets.all(
                                                            10),
                                                    child: Container(
                                                      decoration: BoxDecoration(
                                                        color: themeChange
                                                                .getThem()
                                                            ? AppColors
                                                                .darkContainerBackground
                                                            : AppColors
                                                                .containerBackground,
                                                        borderRadius:
                                                            const BorderRadius
                                                                .all(Radius
                                                                    .circular(
                                                                        10)),
                                                        border: Border.all(
                                                            color: themeChange
                                                                    .getThem()
                                                                ? AppColors
                                                                    .darkContainerBorder
                                                                : AppColors
                                                                    .containerBorder,
                                                            width: 0.5),
                                                        boxShadow: themeChange
                                                                .getThem()
                                                            ? null
                                                            : [
                                                                BoxShadow(
                                                                  color: Colors
                                                                      .black
                                                                      .withOpacity(
                                                                          0.10),
                                                                  blurRadius: 5,
                                                                  offset:
                                                                      const Offset(
                                                                          0, 4),
                                                                ),
                                                              ],
                                                      ),
                                                      child: InkWell(
                                                          onTap: () {
                                                            Get.to(
                                                                IntercityCompleteOrderScreen(),
                                                                arguments: {
                                                                  "orderModel":
                                                                      orderModel,
                                                                });
                                                          },
                                                          child: Padding(
                                                            padding:
                                                                const EdgeInsets
                                                                    .all(12.0),
                                                            child: Column(
                                                              crossAxisAlignment:
                                                                  CrossAxisAlignment
                                                                      .start,
                                                              children: [
                                                                Row(
                                                                  children: [
                                                                    Expanded(
                                                                      child:
                                                                          Text(
                                                                        orderModel.status?.toString() ??
                                                                            '',
                                                                        style: TextStyle(
                                                                            fontWeight:
                                                                                FontWeight.w600),
                                                                      ),
                                                                    ),
                                                                    Text(
                                                                      "\$ ${orderModel.finalRate ?? orderModel.offerRate ?? '0'}",
                                                                      style:
                                                                          TextStyle(
                                                                        fontSize:
                                                                            18,
                                                                        fontWeight:
                                                                            FontWeight.w800,
                                                                      ),
                                                                    ),
                                                                  ],
                                                                ),
                                                                const Padding(
                                                                  padding: EdgeInsets
                                                                      .symmetric(
                                                                          vertical:
                                                                              4),
                                                                  child: Divider(
                                                                      thickness:
                                                                          1),
                                                                ),
                                                                LocationView(
                                                                  sourceLocation:
                                                                      orderModel
                                                                              .sourceLocationName
                                                                              ?.toString() ??
                                                                          '',
                                                                  destinationLocation:
                                                                      orderModel
                                                                              .destinationLocationName
                                                                              ?.toString() ??
                                                                          '',
                                                                ),
                                                                Padding(
                                                                  padding: const EdgeInsets
                                                                      .symmetric(
                                                                      vertical:
                                                                          14),
                                                                  child:
                                                                      Container(
                                                                    decoration: BoxDecoration(
                                                                        color: themeChange.getThem()
                                                                            ? AppColors
                                                                                .darkGray
                                                                            : AppColors
                                                                                .gray,
                                                                        borderRadius: const BorderRadius.all(
                                                                            Radius.circular(10))),
                                                                    child: Padding(
                                                                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 12),
                                                                        child: Center(
                                                                          child:
                                                                              Row(
                                                                            children: [
                                                                              Expanded(child: Text(orderModel.status.toString(), style: TextStyle(fontWeight: FontWeight.w500))),
                                                                              Text(orderModel.createdAt ?? '', style: TextStyle()),
                                                                            ],
                                                                          ),
                                                                        )),
                                                                  ),
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
                                            ? Center(
                                                child: Text(
                                                    "No cancelled rides found"
                                                        .tr))
                                            : ListView.builder(
                                                itemCount: controller
                                                    .cancelledOrders.length,
                                                scrollDirection: Axis.vertical,
                                                shrinkWrap: true,
                                                itemBuilder: (context, index) {
                                                  InterCityOrderModel
                                                      orderModel = controller
                                                              .cancelledOrders[
                                                          index];
                                                  return Padding(
                                                    padding:
                                                        const EdgeInsets.all(
                                                            10),
                                                    child: Container(
                                                      decoration: BoxDecoration(
                                                        color: themeChange
                                                                .getThem()
                                                            ? AppColors
                                                                .darkContainerBackground
                                                            : AppColors
                                                                .containerBackground,
                                                        borderRadius:
                                                            const BorderRadius
                                                                .all(Radius
                                                                    .circular(
                                                                        10)),
                                                        border: Border.all(
                                                            color: themeChange
                                                                    .getThem()
                                                                ? AppColors
                                                                    .darkContainerBorder
                                                                : AppColors
                                                                    .containerBorder,
                                                            width: 0.5),
                                                        boxShadow: themeChange
                                                                .getThem()
                                                            ? null
                                                            : [
                                                                BoxShadow(
                                                                  color: Colors
                                                                      .black
                                                                      .withOpacity(
                                                                          0.10),
                                                                  blurRadius: 5,
                                                                  offset:
                                                                      const Offset(
                                                                          0, 4),
                                                                ),
                                                              ],
                                                      ),
                                                      child: InkWell(
                                                          onTap: () {
                                                            Get.to(
                                                                IntercityCompleteOrderScreen(),
                                                                arguments: {
                                                                  "orderModel":
                                                                      orderModel,
                                                                });
                                                          },
                                                          child: Padding(
                                                            padding:
                                                                const EdgeInsets
                                                                    .all(12.0),
                                                            child: Column(
                                                              crossAxisAlignment:
                                                                  CrossAxisAlignment
                                                                      .start,
                                                              children: [
                                                                Row(
                                                                  children: [
                                                                    Expanded(
                                                                      child:
                                                                          Text(
                                                                        orderModel.status?.toString() ??
                                                                            '',
                                                                        style: TextStyle(
                                                                            fontWeight:
                                                                                FontWeight.w600,
                                                                            color:
                                                                                Colors.red),
                                                                      ),
                                                                    ),
                                                                    Text(
                                                                      "\$ ${orderModel.offerRate ?? '0'}",
                                                                      style:
                                                                          TextStyle(
                                                                        fontSize:
                                                                            18,
                                                                        fontWeight:
                                                                            FontWeight.w800,
                                                                      ),
                                                                    ),
                                                                  ],
                                                                ),
                                                                const Padding(
                                                                  padding: EdgeInsets
                                                                      .symmetric(
                                                                          vertical:
                                                                              4),
                                                                  child: Divider(
                                                                      thickness:
                                                                          1),
                                                                ),
                                                                LocationView(
                                                                  sourceLocation:
                                                                      orderModel
                                                                              .sourceLocationName
                                                                              ?.toString() ??
                                                                          '',
                                                                  destinationLocation:
                                                                      orderModel
                                                                              .destinationLocationName
                                                                              ?.toString() ??
                                                                          '',
                                                                ),
                                                                Padding(
                                                                  padding: const EdgeInsets
                                                                      .symmetric(
                                                                      vertical:
                                                                          14),
                                                                  child:
                                                                      Container(
                                                                    decoration: BoxDecoration(
                                                                        color: themeChange.getThem()
                                                                            ? AppColors
                                                                                .darkGray
                                                                            : AppColors
                                                                                .gray,
                                                                        borderRadius: const BorderRadius.all(
                                                                            Radius.circular(10))),
                                                                    child: Padding(
                                                                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 12),
                                                                        child: Center(
                                                                          child:
                                                                              Row(
                                                                            children: [
                                                                              Expanded(child: Text(orderModel.status.toString(), style: TextStyle(fontWeight: FontWeight.w500))),
                                                                              Text(orderModel.createdAt ?? '', style: TextStyle()),
                                                                            ],
                                                                          ),
                                                                        )),
                                                                  ),
                                                                ),
                                                              ],
                                                            ),
                                                          )),
                                                    ),
                                                  );
                                                }),
                                  ],
                                ),
                              ),
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
        });
  }
}
