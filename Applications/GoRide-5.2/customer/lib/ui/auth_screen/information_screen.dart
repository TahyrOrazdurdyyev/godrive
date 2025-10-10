import 'dart:convert';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:firebase_auth/firebase_auth.dart';
// Google Fonts replaced with local fonts
import 'package:country_code_picker/country_code_picker.dart';
// Google Fonts replaced with local fonts
import 'package:customer/constant/constant.dart';
// Google Fonts replaced with local fonts
import 'package:customer/constant/show_toast_dialog.dart';
// Google Fonts replaced with local fonts
import 'package:customer/controller/information_controller.dart';
// Google Fonts replaced with local fonts
import 'package:customer/model/referral_model.dart';
// Google Fonts replaced with local fonts
import 'package:customer/model/user_model.dart';
import 'package:customer/services/laravel_service.dart';
import 'package:customer/utils/Preferences.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/app_colors.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/button_them.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/responsive.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/text_field_them.dart';
// Google Fonts replaced with local fonts
import 'package:customer/ui/dashboard_screen.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/DarkThemeProvider.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/fire_store_utils.dart';
// Google Fonts replaced with local fonts
import 'package:flutter/material.dart';
// Google Fonts replaced with local fonts
import 'package:get/get.dart';
// Google Fonts replaced with local fonts

import 'package:provider/provider.dart';
// Google Fonts replaced with local fonts

class InformationScreen extends StatelessWidget {
  const InformationScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);

    return GetX<InformationController>(
        init: InformationController(),
        builder: (controller) {
          return Scaffold(
            body: SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Image.asset("assets/images/login_image.png", width: Responsive.width(100, context)),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 10),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Padding(
                          padding: EdgeInsets.only(top: 10),
                          child: Text("Sign up".tr, style: TextStyle(fontWeight: FontWeight.w600, fontSize: 18)),
                        ),
                        Padding(
                          padding: const EdgeInsets.only(top: 4),
                          child: Text("Create your account to start using GoRide".tr, style: TextStyle(fontWeight: FontWeight.w400)),
                        ),
                        const SizedBox(
                          height: 20,
                        ),
                        TextFieldThem.buildTextFiled(context, hintText: 'Full name'.tr, controller: controller.fullNameController.value),
                        const SizedBox(
                          height: 10,
                        ),
                        TextFormField(
                            validator: (value) => value != null && value.isNotEmpty ? null : 'Required',
                            keyboardType: TextInputType.number,
                            textCapitalization: TextCapitalization.sentences,
                            controller: controller.phoneNumberController.value,
                            textAlign: TextAlign.start,
                            enabled: controller.loginType.value == Constant.phoneLoginType ? false : true,
                            decoration: InputDecoration(
                                isDense: true,
                                filled: true,
                                fillColor: themeChange.getThem() ? AppColors.darkTextField : AppColors.textField,
                                contentPadding: const EdgeInsets.symmetric(vertical: 12),
                                prefixIcon: CountryCodePicker(
                                  onChanged: (value) {
                                    controller.countryCode.value = value.dialCode.toString();
                                  },
                                  dialogBackgroundColor: themeChange.getThem() ? AppColors.darkBackground : AppColors.background,
                                  initialSelection: controller.countryCode.value,
                                  comparator: (a, b) => b.name!.compareTo(a.name.toString()),
                                  flagDecoration: const BoxDecoration(
                                    borderRadius: BorderRadius.all(Radius.circular(2)),
                                  ),
                                ),
                                disabledBorder: OutlineInputBorder(
                                  borderRadius: const BorderRadius.all(Radius.circular(4)),
                                  borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                ),
                                focusedBorder: OutlineInputBorder(
                                  borderRadius: const BorderRadius.all(Radius.circular(4)),
                                  borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                ),
                                enabledBorder: OutlineInputBorder(
                                  borderRadius: const BorderRadius.all(Radius.circular(4)),
                                  borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                ),
                                errorBorder: OutlineInputBorder(
                                  borderRadius: const BorderRadius.all(Radius.circular(4)),
                                  borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                ),
                                border: OutlineInputBorder(
                                  borderRadius: const BorderRadius.all(Radius.circular(4)),
                                  borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                ),
                                hintText: "Phone number".tr)),
                        const SizedBox(
                          height: 10,
                        ),
                        TextFieldThem.buildTextFiled(context,
                            hintText: 'Email'.tr, controller: controller.emailController.value, enable: controller.loginType.value == Constant.googleLoginType ? false : true),
                        const SizedBox(
                          height: 10,
                        ),
                        TextFieldThem.buildTextFiled(
                          context,
                          hintText: 'Referral Code (Optional)'.tr,
                          controller: controller.referralCodeController.value,
                        ),
                        const SizedBox(
                          height: 60,
                        ),
                        ButtonThem.buildButton(
                          context,
                          title: "Create account".tr,
                          onPress: () async {
                            if (controller.fullNameController.value.text.isEmpty) {
                              ShowToastDialog.showToast("Please enter full name".tr);
                            } else if (controller.emailController.value.text.isEmpty) {
                              ShowToastDialog.showToast("Please enter email".tr);
                            } else if (controller.phoneNumberController.value.text.isEmpty) {
                              ShowToastDialog.showToast("Please enter phone".tr);
                            } else if (Constant.validateEmail(controller.emailController.value.text) == false) {
                              ShowToastDialog.showToast("Please enter valid email".tr);
                            } else {
                              try {
                                ShowToastDialog.showLoader("Please wait".tr);
                                
                                // Get Firebase UID from current user
                                String? firebaseUid = FirebaseAuth.instance.currentUser?.uid;
                                if (firebaseUid == null || firebaseUid.isEmpty) {
                                  ShowToastDialog.closeLoader();
                                  ShowToastDialog.showToast("Authentication error. Please login again.");
                                  return;
                                }
                                
                                print("🔥 Firebase UID: $firebaseUid");
                                print("🔥 Email: ${controller.emailController.value.text}");
                                print("🔥 Full Name: ${controller.fullNameController.value.text}");
                                print("🔥 Phone: ${controller.phoneNumberController.value.text}");
                                print("🔥 Country Code: ${controller.countryCode.value}");

                                // Register user with Laravel API
                                UserModel? registeredUser = await LaravelService.loginUser(
                                  firebaseUid: firebaseUid,
                                  email: controller.emailController.value.text,
                                  fullName: controller.fullNameController.value.text,
                                  phoneNumber: controller.phoneNumberController.value.text,
                                  countryCode: controller.countryCode.value,
                                  loginType: 'phone', // Fixed loginType
                                );

                                ShowToastDialog.closeLoader();
                                
                                if (registeredUser != null) {
                                  print("🎉 Registration successful!");
                                  // Save user data to preferences
                                  await Preferences.setString(Preferences.user, json.encode(registeredUser.toJson()));
                                  print("🎉 User data saved to preferences");
                                  ShowToastDialog.showToast("Account created successfully!");
                                  Get.offAll(const DashBoardScreen());
                                } else {
                                  print("❌ Registration failed - registeredUser is null");
                                  ShowToastDialog.showToast("Registration failed. Please try again.");
                                }
                              } catch (e) {
                                ShowToastDialog.closeLoader();
                                ShowToastDialog.showToast("Something went wrong. Please try again.");
                                print("Registration error: $e");
                                print("Firebase user: ${FirebaseAuth.instance.currentUser?.uid}");
                                print("Email: ${controller.emailController.value.text}");
                                print("Phone: ${controller.phoneNumberController.value.text}");
                              }
                            }
                          },
                        ),
                      ],
                    ),
                  )
                ],
              ),
            ),
          );
        });
  }
}
