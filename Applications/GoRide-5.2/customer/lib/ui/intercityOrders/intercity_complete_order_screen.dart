import 'package:clipboard/clipboard.dart';
// Google Fonts replaced with local fonts
import 'package:customer/constant/constant.dart';
// Google Fonts replaced with local fonts
import 'package:customer/constant/show_toast_dialog.dart';
// Google Fonts replaced with local fonts
import 'package:customer/controller/intercity_complete_order_controller.dart';
// Google Fonts replaced with local fonts
import 'package:customer/model/driver_user_model.dart';
// Google Fonts replaced with local fonts
import 'package:customer/model/tax_model.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/app_colors.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/responsive.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/DarkThemeProvider.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/fire_store_utils.dart';
// Google Fonts replaced with local fonts
import 'package:customer/widget/driver_view.dart';
// Google Fonts replaced with local fonts
import 'package:customer/widget/location_view.dart';
// Google Fonts replaced with local fonts
import 'package:dotted_border/dotted_border.dart';
// Google Fonts replaced with local fonts
import 'package:flutter/material.dart';
// Google Fonts replaced with local fonts
import 'package:flutter_svg/flutter_svg.dart';
// Google Fonts replaced with local fonts
import 'package:get/get.dart';
// Google Fonts replaced with local fonts

import 'package:provider/provider.dart';
// Google Fonts replaced with local fonts

class IntercityCompleteOrderScreen extends StatelessWidget {
  const IntercityCompleteOrderScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);

    return GetX<IntercityCompleteOrderController>(
        init: IntercityCompleteOrderController(),
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
                    child: controller.isLoading.value
                        ? const Center(child: CircularProgressIndicator())
                        : Container(
                            decoration:
                                BoxDecoration(color: Theme.of(context).colorScheme.background, borderRadius: const BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25))),
                            child: Padding(
                              padding: const EdgeInsets.symmetric(horizontal: 10),
                              child: Padding(
                                padding: const EdgeInsets.only(top: 20),
                                child: Padding(
                                  padding: const EdgeInsets.symmetric(horizontal: 10),
                                  child: SingleChildScrollView(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Container(
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
                                            padding: const EdgeInsets.all(8.0),
                                            child: Column(
                                              crossAxisAlignment: CrossAxisAlignment.start,
                                              children: [
                                                Row(
                                                  children: [
                                                    Expanded(
                                                      child: Text(
                                                        "Order ID".tr,
                                                        style: TextStyle(
                                                          fontWeight: FontWeight.w600,
                                                        ),
                                                      ),
                                                    ),
                                                    InkWell(
                                                      onTap: () {
                                                        FlutterClipboard.copy(controller.orderModel.value.id.toString()).then((value) {
                                                          ShowToastDialog.showToast("OrderId copied".tr);
                                                        });
                                                      },
                                                      child: DottedBorder(
                                                        borderType: BorderType.RRect,
                                                        radius: const Radius.circular(4),
                                                        dashPattern: const [6, 6, 6, 6],
                                                        color: AppColors.textFieldBorder,
                                                        child: Padding(
                                                          padding: const EdgeInsets.symmetric(horizontal: 10),
                                                          child: Text(
                                                            "Copy".tr,
                                                            style: TextStyle(fontWeight: FontWeight.w700),
                                                          ),
                                                        ),
                                                      ),
                                                    ),
                                                  ],
                                                ),
                                                const SizedBox(
                                                  height: 5,
                                                ),
                                                Text(
                                                  "#${controller.orderModel.value.id!.toUpperCase()}",
                                                  style: TextStyle(
                                                    fontWeight: FontWeight.w400,
                                                  ),
                                                ),
                                              ],
                                            ),
                                          ),
                                        ),
                                        const SizedBox(
                                          height: 20,
                                        ),
                                        DriverView(driverId: controller.orderModel.value.driverId.toString()),
                                        const Padding(
                                          padding: EdgeInsets.symmetric(vertical: 5),
                                          child: Divider(thickness: 1),
                                        ),
                                        Text(
                                          "Vehicle Details".tr,
                                          style: TextStyle(fontWeight: FontWeight.w600),
                                        ),
                                        const SizedBox(
                                          height: 10,
                                        ),
                                        FutureBuilder<DriverUserModel?>(
                                            future: FireStoreUtils.getDriver(controller.orderModel.value.driverId.toString()),
                                            builder: (context, snapshot) {
                                              switch (snapshot.connectionState) {
                                                case ConnectionState.waiting:
                                                  return Constant.loader();
                                                case ConnectionState.done:
                                                  if (snapshot.hasError) {
                                                    return Text(snapshot.error.toString());
                                                  } else {
                                                    DriverUserModel driverModel = snapshot.data!;
                                                    return Container(
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
                                                        padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 10),
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
                                                        ),
                                                      ),
                                                    );
                                                  }
                                                default:
                                                  return  Text('Error'.tr);
                                              }
                                            }),
                                        const SizedBox(
                                          height: 20,
                                        ),
                                        Text(
                                          "Pickup and drop-off locations".tr,
                                          style: TextStyle(fontWeight: FontWeight.w600),
                                        ),
                                        const SizedBox(
                                          height: 10,
                                        ),
                                        Container(
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
                                            padding: const EdgeInsets.all(8.0),
                                            child: LocationView(
                                              sourceLocation: controller.orderModel.value.sourceLocationName.toString(),
                                              destinationLocation: controller.orderModel.value.destinationLocationName.toString(),
                                            ),
                                          ),
                                        ),
                                        Padding(
                                          padding: const EdgeInsets.symmetric(vertical: 20),
                                          child: Container(
                                            decoration: BoxDecoration(color: themeChange.getThem() ? AppColors.darkGray : AppColors.gray, borderRadius: const BorderRadius.all(Radius.circular(10))),
                                            child: Padding(
                                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 12),
                                                child: Center(
                                                  child: Row(
                                                    children: [
                                                      Expanded(child: Text(controller.orderModel.value.status.toString(), style: TextStyle(fontWeight: FontWeight.w500))),
                                                      Text(Constant().formatTimestamp(controller.orderModel.value.createdDate), style: TextStyle()),
                                                    ],
                                                  ),
                                                )),
                                          ),
                                        ),
                                        Container(
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
                                            padding: const EdgeInsets.all(8.0),
                                            child: Column(
                                              crossAxisAlignment: CrossAxisAlignment.start,
                                              children: [
                                                Row(
                                                  children: [
                                                    Expanded(
                                                      child: Text(
                                                        "Booking summary".tr,
                                                        style: TextStyle(fontWeight: FontWeight.w600),
                                                      ),
                                                    ),
                                                    Container(
                                                      decoration: BoxDecoration(color: themeChange.getThem() ? AppColors.darkGray : AppColors.gray, borderRadius: BorderRadius.all(Radius.circular(5))),
                                                      child: Padding(
                                                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 2),
                                                        child: Text(
                                                          controller.orderModel.value.paymentType.toString(),
                                                          style: TextStyle(fontWeight: FontWeight.w600),
                                                        ),
                                                      ),
                                                    ),
                                                  ],
                                                ),
                                                const Divider(
                                                  thickness: 1,
                                                ),
                                                Row(
                                                  children: [
                                                    Expanded(
                                                      child: Text(
                                                        "Ride Amount".tr,
                                                        style: TextStyle(color: AppColors.subTitleColor),
                                                      ),
                                                    ),
                                                    Text(
                                                      Constant.amountShow(amount: controller.orderModel.value.finalRate.toString()),
                                                      style: TextStyle(fontWeight: FontWeight.w600),
                                                    ),
                                                  ],
                                                ),
                                                const Divider(
                                                  thickness: 1,
                                                ),
                                                ListView.builder(
                                                  itemCount: controller.orderModel.value.taxList!.length,
                                                  shrinkWrap: true,
                                                  padding: EdgeInsets.zero,
                                                  itemBuilder: (context, index) {
                                                    TaxModel taxModel = controller.orderModel.value.taxList![index];
                                                    return Column(
                                                      children: [
                                                        Row(
                                                          children: [
                                                            Expanded(
                                                              child: Text(
                                                                "${taxModel.title.toString()} (${taxModel.type == "fix" ? Constant.amountShow(amount: taxModel.tax) : "${taxModel.tax}%"})",
                                                                style: TextStyle(color: AppColors.subTitleColor),
                                                              ),
                                                            ),
                                                            Text(
                                                              Constant.amountShow(
                                                                  amount: Constant()
                                                                      .calculateTax(
                                                                          amount:
                                                                              (double.parse(controller.orderModel.value.finalRate.toString()) - double.parse(controller.couponAmount.value.toString()))
                                                                                  .toString(),
                                                                          taxModel: taxModel)
                                                                      .toString()),
                                                              style: TextStyle(fontWeight: FontWeight.w600),
                                                            ),
                                                          ],
                                                        ),
                                                        const Divider(
                                                          thickness: 1,
                                                        ),
                                                      ],
                                                    );
                                                  },
                                                ),
                                                Row(
                                                  children: [
                                                    Expanded(
                                                      child: Text(
                                                        "Discount".tr,
                                                        style: TextStyle(color: AppColors.subTitleColor),
                                                      ),
                                                    ),
                                                    Row(
                                                      children: [
                                                        Text(
                                                          "(-${controller.couponAmount.value == "0.0" ? Constant.amountShow(amount: "0.0") : Constant.amountShow(amount: controller.couponAmount.value)})",
                                                          style: TextStyle(fontWeight: FontWeight.w600, color: Colors.red),
                                                        ),
                                                      ],
                                                    ),
                                                  ],
                                                ),
                                                const Divider(
                                                  thickness: 1,
                                                ),
                                                Row(
                                                  children: [
                                                    Expanded(
                                                      child: Text(
                                                        "Payable amount",
                                                        style: TextStyle(fontWeight: FontWeight.w600),
                                                      ),
                                                    ),
                                                    Text(
                                                      Constant.amountShow(amount: controller.calculateAmount().toString()),
                                                      style: TextStyle(fontWeight: FontWeight.w600),
                                                    ),
                                                  ],
                                                ),
                                              ],
                                            ),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                              ),
                            ),
                          ),
                  ),
                ],
              ));
        });
  }
}
