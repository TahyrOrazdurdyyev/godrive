import 'dart:developer';
import 'dart:io';

import 'package:country_code_picker/country_code_picker.dart';
import 'package:driver/constant/constant.dart';
import 'package:driver/constant/show_toast_dialog.dart';
import 'package:driver/controller/login_controller.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/themes/app_colors.dart';
import 'package:driver/themes/button_them.dart';
import 'package:driver/themes/responsive.dart';
import 'package:driver/ui/auth_screen/information_screen.dart';
import 'package:driver/ui/dashboard_screen.dart';
import 'package:driver/ui/subscription_plan_screen/subscription_list_screen.dart';
import 'package:driver/ui/terms_and_condition/terms_and_condition_screen.dart';
import 'package:driver/utils/DarkThemeProvider.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/driver_api.dart';
import 'package:driver/utils/notification_service.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:sign_in_with_apple/sign_in_with_apple.dart';

class LoginScreen extends StatelessWidget {
  const LoginScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);
    return GetX<LoginController>(
        init: LoginController(),
        builder: (controller) {
          return Scaffold(
            backgroundColor: Theme.of(context).colorScheme.background,
            body: SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Image.asset("assets/images/login_image.png", width: Responsive.width(100, context)),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Padding(
                          padding: const EdgeInsets.only(top: 10),
                          child: Text("Login".tr, style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 18)),
                        ),
                        Padding(
                          padding: const EdgeInsets.only(top: 5),
                          child: Text("Welcome Back! We are happy to have \n you back".tr, style: GoogleFonts.poppins(fontWeight: FontWeight.w400)),
                        ),
                        const SizedBox(
                          height: 20,
                        ),
                        TextFormField(
                            validator: (value) => value != null && value.isNotEmpty ? null : 'Required',
                            keyboardType: TextInputType.number,
                            textCapitalization: TextCapitalization.sentences,
                            controller: controller.phoneNumberController.value,
                            textAlign: TextAlign.start,
                            style: GoogleFonts.poppins(color: themeChange.getThem() ? Colors.white : Colors.black),
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
                          height: 30,
                        ),
                        ButtonThem.buildButton(
                          context,
                          title: "Next".tr,
                          onPress: () {
                            controller.sendCode();
                          },
                        ),
                        Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 40),
                          child: Row(
                            children: [
                              const Expanded(
                                  child: Divider(
                                height: 1,
                              )),
                              Padding(
                                padding: const EdgeInsets.symmetric(horizontal: 20),
                                child: Text(
                                  "OR".tr,
                                  style: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.w600),
                                ),
                              ),
                              const Expanded(
                                  child: Divider(
                                height: 1,
                              )),
                            ],
                          ),
                        ),
                        ButtonThem.buildBorderButton(
                          context,
                          title: "Login with google".tr,
                          iconVisibility: true,
                          iconAssetImage: 'assets/icons/ic_google.png',
                          onPress: () async {
                            ShowToastDialog.showLoader("Please wait".tr);
                            await controller.signInWithGoogle().then((value) async {
                              ShowToastDialog.closeLoader();
                              if (value != null) {
                                if (value.additionalUserInfo!.isNewUser) {
                                  log("----->new user");
                                  DriverUserModel userModel = DriverUserModel();
                                  userModel.id = value.user!.uid;
                                  userModel.email = value.user!.email;
                                  userModel.fullName = value.user!.displayName;
                                  userModel.profilePic = value.user!.photoURL;
                                  userModel.loginType = Constant.googleLoginType;

                                  ShowToastDialog.closeLoader();
                                  Get.to(const InformationScreen(), arguments: {
                                    "userModel": userModel,
                                  });
                                } else {
                                  log("----->old user");
                                  try {
                                    // Check if driver exists in database
                                    final driverResponse = await DriverApi.getProfile(value.user!.uid);
                                    if (driverResponse['success'] == true && driverResponse['driver'] != null) {
                                      // Update FCM token
                                      String token = await NotificationService.getToken();
                                      await DriverApi.updateFcmToken(
                                        uid: value.user!.uid,
                                        fcmToken: token,
                                      );

                                      // Get fresh driver profile
                                      final freshResponse = await DriverApi.getProfile(FirebaseAuth.instance.currentUser!.uid);
                                      if (freshResponse['success'] == true && freshResponse['driver'] != null) {
                                        DriverUserModel userModel = DriverUserModel.fromJson(freshResponse['driver']);
                                        
                                        // Check subscription plan
                                        bool isPlanExpire = false;
                                        if (userModel.subscriptionPlan?.id != null) {
                                          if (userModel.subscriptionExpiryDate == null) {
                                            if (userModel.subscriptionPlan?.expiryDay == '-1') {
                                              isPlanExpire = false;
                                            } else {
                                              isPlanExpire = true;
                                            }
                                          } else {
                                            DateTime expiryDate = userModel.subscriptionExpiryDate!.toDate();
                                            isPlanExpire = expiryDate.isBefore(DateTime.now());
                                          }
                                        } else {
                                          isPlanExpire = true;
                                        }
                                        
                                        ShowToastDialog.closeLoader();
                                        if (userModel.subscriptionPlanId == null || isPlanExpire == true) {
                                          if (Constant.adminCommission?.isEnabled == false && Constant.isSubscriptionModelApplied == false) {
                                            Get.offAll(const DashBoardScreen());
                                          } else {
                                            Get.offAll(const SubscriptionListScreen(), arguments: {"isShow": true});
                                          }
                                        } else {
                                          Get.offAll(const DashBoardScreen());
                                        }
                                      }
                                    } else {
                                      // New driver - go to registration
                                      ShowToastDialog.closeLoader();
                                      DriverUserModel userModel = DriverUserModel();
                                      userModel.id = value.user!.uid;
                                      userModel.email = value.user!.email;
                                      userModel.fullName = value.user!.displayName;
                                      userModel.profilePic = value.user!.photoURL;
                                      userModel.loginType = Constant.googleLoginType;

                                      Get.to(const InformationScreen(), arguments: {
                                        "userModel": userModel,
                                      });
                                    }
                                  } catch (e) {
                                    ShowToastDialog.closeLoader();
                                    log('❌ Login error: $e');
                                    ShowToastDialog.showToast("Login failed".tr);
                                  }
                                }
                              }
                            });
                          },
                        ),
                        const SizedBox(
                          height: 16,
                        ),
                        Visibility(
                            visible: Platform.isIOS,
                            child: ButtonThem.buildBorderButton(
                              context,
                              title: "Login with apple".tr,
                              iconVisibility: true,
                              iconAssetImage: 'assets/icons/ic_apple.png',
                              iconColor: themeChange.getThem() ? AppColors.darkModePrimary : Colors.black,
                              onPress: () async {
                                ShowToastDialog.showLoader("Please wait".tr);
                                await controller.signInWithApple().then((value) async {
                                  ShowToastDialog.closeLoader();
                                  if (value != null) {
                                    Map<String, dynamic> map = value;
                                    AuthorizationCredentialAppleID appleCredential = map['appleCredential'];
                                    UserCredential userCredential = map['userCredential'];

                                    if (userCredential.additionalUserInfo!.isNewUser) {
                                      log("----->new user");
                                      DriverUserModel userModel = DriverUserModel();
                                      userModel.id = userCredential.user!.uid;
                                      userModel.profilePic = userCredential.user!.photoURL;
                                      userModel.loginType = Constant.appleLoginType;
                                      userModel.email = userCredential.additionalUserInfo!.profile!['email'];
                                      userModel.fullName = "${appleCredential.givenName} ${appleCredential.familyName}";

                                      ShowToastDialog.closeLoader();
                                      Get.to(const InformationScreen(), arguments: {
                                        "userModel": userModel,
                                      });
                                    } else {
                                      log("----->old user");
                                      try {
                                        // Check if driver exists in database
                                        final driverResponse = await DriverApi.getProfile(userCredential.user!.uid);
                                        if (driverResponse['success'] == true && driverResponse['driver'] != null) {
                                          // Get driver profile
                                          DriverUserModel userModel = DriverUserModel.fromJson(driverResponse['driver']);
                                          
                                          // Check subscription plan
                                          bool isPlanExpire = false;
                                          if (userModel.subscriptionPlan?.id != null) {
                                            if (userModel.subscriptionExpiryDate == null) {
                                              if (userModel.subscriptionPlan?.expiryDay == '-1') {
                                                isPlanExpire = false;
                                              } else {
                                                isPlanExpire = true;
                                              }
                                            } else {
                                              DateTime expiryDate = userModel.subscriptionExpiryDate!.toDate();
                                              isPlanExpire = expiryDate.isBefore(DateTime.now());
                                            }
                                          } else {
                                            isPlanExpire = true;
                                          }
                                          
                                          ShowToastDialog.closeLoader();
                                          if (userModel.subscriptionPlanId == null || isPlanExpire == true) {
                                            if (Constant.adminCommission?.isEnabled == false && Constant.isSubscriptionModelApplied == false) {
                                              Get.offAll(const DashBoardScreen());
                                            } else {
                                              Get.offAll(const SubscriptionListScreen(), arguments: {"isShow": true});
                                            }
                                          } else {
                                            Get.offAll(const DashBoardScreen());
                                          }
                                        } else {
                                          // New driver - go to registration
                                          ShowToastDialog.closeLoader();
                                          DriverUserModel userModel = DriverUserModel();
                                          userModel.id = userCredential.user!.uid;
                                          userModel.profilePic = userCredential.user!.photoURL;
                                          userModel.loginType = Constant.appleLoginType;
                                          userModel.email = userCredential.additionalUserInfo!.profile!['email'];
                                          userModel.fullName = "${appleCredential.givenName ?? ''} ${appleCredential.familyName ?? ''}";

                                          Get.to(const InformationScreen(), arguments: {
                                            "userModel": userModel,
                                          });
                                        }
                                      } catch (e) {
                                        ShowToastDialog.closeLoader();
                                        log('❌ Apple login error: $e');
                                        ShowToastDialog.showToast("Login failed".tr);
                                      }
                                    }
                                  }
                                });
                              },
                            )),
                      ],
                    ),
                  )
                ],
              ),
            ),
            bottomNavigationBar: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
                child: Text.rich(
                  textAlign: TextAlign.center,
                  TextSpan(
                    text: 'By tapping "Next" you agree to '.tr,
                    style: GoogleFonts.poppins(),
                    children: <TextSpan>[
                      TextSpan(
                          recognizer: TapGestureRecognizer()
                            ..onTap = () {
                              Get.to(const TermsAndConditionScreen(
                                type: "terms",
                              ));
                            },
                          text: 'Terms and conditions'.tr,
                          style: GoogleFonts.poppins(decoration: TextDecoration.underline)),
                      TextSpan(text: ' and ', style: GoogleFonts.poppins()),
                      TextSpan(
                          recognizer: TapGestureRecognizer()
                            ..onTap = () {
                              Get.to(const TermsAndConditionScreen(
                                type: "privacy",
                              ));
                            },
                          text: 'privacy policy'.tr,
                          style: GoogleFonts.poppins(decoration: TextDecoration.underline)),
                      // can add more TextSpans here...
                    ],
                  ),
                )),
          );
        });
  }
}
