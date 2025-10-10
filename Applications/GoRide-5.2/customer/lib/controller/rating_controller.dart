import 'dart:developer';
import 'package:customer/model/driver_user_model.dart';
import 'package:customer/model/intercity_order_model.dart';
import 'package:customer/model/order_model.dart';
import 'package:customer/utils/fire_store_utils.dart';
import 'package:customer/utils/driver_api.dart';
import 'package:customer/utils/review_api.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../model/review_model.dart';

class RatingController extends GetxController {
  RxBool isLoading = true.obs;
  RxDouble rating = 1.0.obs;
  Rx<TextEditingController> commentController = TextEditingController().obs;

  Rx<ReviewModel> reviewModel = ReviewModel().obs;
  Rx<DriverUserModel> driverModel = DriverUserModel().obs;

  @override
  void onInit() {
    // TODO: implement onInit
    super.onInit();
    getArgument();
  }

  Rx<OrderModel> orderModel = OrderModel().obs;
  Rx<InterCityOrderModel> intercityOrderModel = InterCityOrderModel().obs;
  RxString type = "".obs;

  getArgument() async {
    dynamic argumentData = Get.arguments;
    if (argumentData != null) {
      type.value = argumentData['type'];
      if (type.value == "orderModel") {
        orderModel.value = argumentData['orderModel'];
      } else {
        intercityOrderModel.value = argumentData['interCityOrderModel'];
      }
    }
    
    try {
      // Get driver data via API
      final driverId = type.value == "orderModel" 
          ? orderModel.value.driverId 
          : intercityOrderModel.value.driverId;
      
      if (driverId != null) {
        final driverResponse = await DriverApi.getProfile(int.parse(driverId));
        if (driverResponse['success'] == true && driverResponse['driver'] != null) {
          driverModel.value = DriverUserModel.fromJson(driverResponse['driver']);
        }
      }
      
      // Get existing review via API
      final orderId = type.value == "orderModel" 
          ? orderModel.value.id 
          : intercityOrderModel.value.id;
      
      if (orderId != null) {
        try {
          final reviewResponse = await ReviewApi.getReviewByOrder(orderId);
          if (reviewResponse['success'] == true && reviewResponse['review'] != null) {
            // Parse review data
            final reviewData = reviewResponse['review'];
            reviewModel.value.id = reviewData['id']?.toString();
            reviewModel.value.rating = reviewData['rating']?.toString();
            reviewModel.value.comment = reviewData['comment'];
            
            rating.value = double.parse(reviewModel.value.rating ?? '1.0');
            commentController.value.text = reviewModel.value.comment ?? '';
          }
        } catch (e) {
          // No existing review, that's ok
          log('No existing review found: $e');
        }
      }
    } catch (e) {
      log('❌ Error loading driver/review data: $e');
    }
    
    isLoading.value = false;
    update();
  }
}
