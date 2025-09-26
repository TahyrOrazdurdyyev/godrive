import 'package:customer/constant/collection_name.dart';
import 'package:customer/utils/fire_store_utils.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

class ContactUsController extends GetxController {
  RxBool isLoading = true.obs;

  Rx<TextEditingController> emailController = TextEditingController().obs;
  Rx<TextEditingController> feedbackController = TextEditingController().obs;

  @override
  void onInit() {
    // TODO: implement onInit
    getContactUsInformation();
    super.onInit();
  }

  RxString email = "".obs;
  RxString phone = "".obs;
  RxString address = "".obs;
  RxString subject = "".obs;

  getContactUsInformation() async {
    // DEMO: Load static contact information
    await Future.delayed(Duration(milliseconds: 500)); // Simulate loading
    
    email.value = "support@goride.com";
    phone.value = "+1 (555) 123-4567";
    address.value = "123 GoRide Street, Demo City, DC 12345";
    subject.value = "GoRide Customer Support";
    
    isLoading.value = false;
  }
}
