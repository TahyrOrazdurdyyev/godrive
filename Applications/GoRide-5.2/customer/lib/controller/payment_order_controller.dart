import 'dart:convert';
import 'package:customer/constant/show_toast_dialog.dart';
import 'package:customer/model/order_model.dart';
import 'package:customer/model/user_model.dart';
import 'package:customer/utils/Preferences.dart';
import 'package:customer/ui/dashboard_screen.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

class PaymentOrderController extends GetxController {
  RxBool isLoading = true.obs;
  Rx<OrderModel> orderModel = OrderModel().obs;
  Rx<UserModel> userModel = UserModel().obs;
  
  // Only cash payment method
  RxString selectedPaymentMethod = "Cash".obs;

  @override
  void onInit() {
    getArgument();
    getUser();
    super.onInit();
  }

  getArgument() async {
    dynamic argumentData = Get.arguments;
    if (argumentData != null) {
      orderModel.value = argumentData['orderModel'];
    }
    isLoading.value = false;
    update();
  }

  getUser() async {
    try {
      // Load user data from preferences
      String? userJson = Preferences.getString(Preferences.user);
      if (userJson != null && userJson.isNotEmpty) {
        Map<String, dynamic> userData = json.decode(userJson);
        userModel.value = UserModel.fromJson(userData);
      } else {
        userModel.value = UserModel();
      }
    } catch (e) {
      debugPrint("Error loading user data: $e");
      userModel.value = UserModel();
    }
  }

  double calculateAmount() {
    double total = 0.0;
    
    // Base fare
    if (orderModel.value.subTotal != null) {
      total += double.parse(orderModel.value.subTotal.toString());
    }
    
    // Tax
    if (orderModel.value.taxAmount != null) {
      total += double.parse(orderModel.value.taxAmount.toString());
    }
    
    // Tip
    if (orderModel.value.tipAmount != null) {
      total += double.parse(orderModel.value.tipAmount.toString());
    }
    
    // Discount
    if (orderModel.value.discountAmount != null) {
      total -= double.parse(orderModel.value.discountAmount.toString());
    }
    
    return total;
  }

  completeOrder() async {
    ShowToastDialog.showLoader("Processing payment...");
    
    try {
      // Since it's cash payment, just mark order as completed
      await Future.delayed(Duration(seconds: 2)); // Simulate processing
      
      // Update order status to completed
      orderModel.value.status = "completed";
      orderModel.value.paymentType = "Cash";
      orderModel.value.paymentStatus = true;
      
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast("Order completed successfully! Pay cash to driver.");
      
      // Navigate to dashboard
      Get.offAll(const DashBoardScreen());
      
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast("Error completing order: $e");
    }
  }
}
