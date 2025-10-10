import 'dart:developer';
import 'package:driver/model/intercity_order_model.dart';
import 'package:driver/model/order_model.dart';
import 'package:driver/model/user_model.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/driver_api.dart';
import 'package:driver/utils/customer_api.dart';
import 'package:driver/utils/review_api.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../model/review_model.dart';

class RatingController extends GetxController {
  RxBool isLoading = true.obs;
  RxDouble rating = 0.0.obs;
  Rx<TextEditingController> commentController = TextEditingController().obs;

  Rx<ReviewModel> reviewModel = ReviewModel().obs;
  Rx<UserModel> userModel = UserModel().obs;

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
      // Get customer
      final customerId = type.value == "orderModel" ? orderModel.value.userId.toString() : intercityOrderModel.value.userId.toString();
      final customerResponse = await CustomerApi.getCustomerProfile(customerId);
      if (customerResponse['success'] == true && customerResponse['customer'] != null) {
        userModel.value = UserModel.fromJson(customerResponse['customer']);
      }

      // Get review
      final orderId = type.value == "orderModel" ? orderModel.value.id.toString() : intercityOrderModel.value.id.toString();
      final orderType = type.value == "orderModel" ? "city" : "intercity";
      final reviewResponse = await ReviewApi.getReviewByOrder(orderId, orderType: orderType);
      if (reviewResponse['success'] == true && reviewResponse['review'] != null) {
        reviewModel.value = ReviewModel.fromJson(reviewResponse['review']);
        rating.value = double.parse(reviewModel.value.rating.toString());
        commentController.value.text = reviewModel.value.comment.toString();
      }
    } catch (e) {
      log('❌ Load rating data error: $e');
    }
    
    isLoading.value = false;
    update();
  }
}
