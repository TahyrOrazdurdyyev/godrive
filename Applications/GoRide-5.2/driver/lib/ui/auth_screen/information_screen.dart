import 'dart:convert';
import 'package:country_code_picker/country_code_picker.dart';
import 'package:driver/constant/constant.dart';
import 'package:driver/constant/show_toast_dialog.dart';
import 'package:driver/controller/information_controller.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/themes/app_colors.dart';
import 'package:driver/themes/button_them.dart';
import 'package:driver/themes/text_field_them.dart';
import 'package:driver/ui/dashboard_screen.dart';
import 'package:driver/utils/DarkThemeProvider.dart';
import 'package:driver/utils/notification_service.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/driver_api.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';

import '../../themes/responsive.dart';

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
                          padding: const EdgeInsets.only(top: 10),
                          child: Text("Sign up".tr, style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 18)),
                        ),
                        Padding(
                          padding: const EdgeInsets.only(top: 4),
                          child: Text("Create your account to start using GoRide".tr, style: GoogleFonts.poppins(fontWeight: FontWeight.w400)),
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
                          height: 60,
                        ),
                        ButtonThem.buildButton(context, title: "Create account".tr, onPress: () async {
                          print('🔥 CREATE ACCOUNT BUTTON PRESSED');
                          
                          if (controller.fullNameController.value.text.isEmpty) {
                            print('❌ Validation failed: Full name empty');
                            ShowToastDialog.showToast("Please enter full name".tr);
                          } else if (controller.emailController.value.text.isEmpty) {
                            print('❌ Validation failed: Email empty');
                            ShowToastDialog.showToast("Please enter email".tr);
                          } else if (controller.phoneNumberController.value.text.isEmpty) {
                            print('❌ Validation failed: Phone number empty');
                            ShowToastDialog.showToast("Please enter phone number".tr);
                          } else if (Constant.validateEmail(controller.emailController.value.text) == false) {
                            print('❌ Validation failed: Invalid email');
                            ShowToastDialog.showToast("Please enter valid email".tr);
                          } else {
                            print('✅ All validations passed');
                            ShowToastDialog.showLoader("Please wait".tr);
                            
                            try {
                              DriverUserModel userModel = controller.userModel.value;
                              userModel.fullName = controller.fullNameController.value.text;
                              userModel.email = controller.emailController.value.text;
                              userModel.countryCode = controller.countryCode.value;
                              userModel.phoneNumber = controller.phoneNumberController.value.text;
                              userModel.documentVerification = false;
                              userModel.isOnline = false;
                              String token = await NotificationService.getToken();
                              userModel.fcmToken = token;

                              print('🔥 Attempting to register driver via Laravel API...');
                              print('🔥 Driver UID: ${userModel.id}');
                              print('🔥 Driver Name: ${userModel.fullName}');
                              print('🔥 Driver Email: ${userModel.email}');
                              print('🔥 Driver Phone: ${userModel.countryCode}${userModel.phoneNumber}');

                              // Call Laravel API for registration using DriverApi
                              final responseData = await DriverApi.register(
                                uid: userModel.id!,
                                fullName: userModel.fullName!,
                                email: userModel.email!,
                                phone: '${userModel.countryCode}${userModel.phoneNumber}',
                                countryCode: userModel.countryCode!,
                                profilePic: userModel.profilePic,
                                fcmToken: token,
                              );

                              ShowToastDialog.closeLoader();

                              print('✅ Laravel API response: $responseData');

                              if (responseData['success'] == true) {
                                print('✅ Driver registered in MySQL - now creating Firestore profile');
                                  
                                  // Create driver profile in Firestore
                                  userModel.fcmToken = token;
                                  // Profile already created via API during registration
                                  print('✅ Driver profile created via API');
                                  
                                  // Show success toast and redirect to Dashboard
                                  ShowToastDialog.showToast('Account created successfully!'.tr);
                                  
                                  // Redirect to DashBoardScreen (Home Screen)
                                  Get.offAll(const DashBoardScreen());
                                } else {
                                  print('❌ API returned success: false');
                                  ShowToastDialog.showToast(responseData['message'] ?? "Registration failed. Please try again.".tr);
                                }
                            } catch (e, stackTrace) {
                              print('❌ ERROR during registration: $e');
                              print('❌ Stack trace: $stackTrace');
                              ShowToastDialog.closeLoader();
                              ShowToastDialog.showToast("Error: ${e.toString()}".tr);
                            }
                          }
                        }),
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
