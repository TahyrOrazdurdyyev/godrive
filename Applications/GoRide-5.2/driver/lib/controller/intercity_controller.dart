import 'dart:developer';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:driver/constant/collection_name.dart';
import 'package:driver/constant/constant.dart';
import 'package:driver/constant/send_notification.dart';
import 'package:driver/constant/show_toast_dialog.dart';
import 'package:driver/controller/home_intercity_controller.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/model/intercity_order_model.dart';
import 'package:driver/model/order/driverId_accept_reject.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/driver_api.dart';
import 'package:driver/utils/customer_api.dart';
import 'package:driver/utils/order_bid_api.dart';
import 'package:driver/utils/intercity_order_api.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';

class IntercityController extends GetxController {
  HomeIntercityController homeController = Get.put(HomeIntercityController());

  Rx<TextEditingController> sourceCityController = TextEditingController().obs;
  Rx<TextEditingController> destinationCityController = TextEditingController().obs;
  Rx<TextEditingController> whenController = TextEditingController().obs;
  Rx<TextEditingController> suggestedTimeController = TextEditingController().obs;
  DateTime? suggestedTime = DateTime.now();
  DateTime? dateAndTime = DateTime.now();

  @override
  void onInit() {
    // TODO: implement onInit
    super.onInit();
  }

  RxList<InterCityOrderModel> intercityServiceOrder = <InterCityOrderModel>[].obs;
  RxBool isLoading = false.obs;
  RxString newAmount = "0.0".obs;
  Rx<TextEditingController> enterOfferRateController = TextEditingController().obs;

  Rx<DriverUserModel> driverModel = DriverUserModel().obs;

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
            uid: FireStoreUtils.getCurrentUid(),
            subscriptionTotalOrders: driverModel.value.subscriptionTotalOrders,
          );
        }

        ShowToastDialog.closeLoader();
        ShowToastDialog.showToast("Ride Accepted".tr);
        Get.back();
        homeController.selectedIndex.value = 1;
        
      } catch (e) {
        ShowToastDialog.closeLoader();
        log('❌ Accept order error: $e');
        ShowToastDialog.showToast("Failed to accept order".tr);
      }
    } else {
      ShowToastDialog.showToast("You have to minimum ${Constant.amountShow(amount: Constant.minimumDepositToRideAccept)} wallet amount to Accept Order and place a bid".tr);
    }
  }

  getOrder() async {
    isLoading.value = true;
    intercityServiceOrder.clear();
    
    try {
      // Get driver profile to get driver ID
      final driverResponse = await DriverApi.getProfile(FireStoreUtils.getCurrentUid());
      if (driverResponse['success'] == true && driverResponse['driver'] != null) {
        driverModel.value = DriverUserModel.fromJson(driverResponse['driver']);
        
        // Search for intercity orders
        final searchResponse = await InterCityOrderApi.searchForDriver(
          driverId: int.parse(driverModel.value.id!),
          sourceCity: sourceCityController.value.text,
          destinationCity: destinationCityController.value.text.isNotEmpty 
              ? destinationCityController.value.text 
              : null,
          whenDate: whenController.value.text.isNotEmpty 
              ? DateFormat("dd-MMM-yyyy").format(dateAndTime!) 
              : null,
        );
        
        if (searchResponse['success'] == true && searchResponse['orders'] != null) {
          for (var orderData in searchResponse['orders']) {
            InterCityOrderModel order = InterCityOrderModel.fromJson(orderData);
            intercityServiceOrder.add(order);
          }
        }
      }
    } catch (e) {
      log('❌ Get intercity orders error: $e');
    }
    
    isLoading.value = false;
  }
}
