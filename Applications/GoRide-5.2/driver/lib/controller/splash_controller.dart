import 'dart:async';

import 'package:driver/constant/constant.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/ui/auth_screen/login_screen.dart';
import 'package:driver/ui/dashboard_screen.dart';
import 'package:driver/ui/on_boarding_screen.dart';
import 'package:driver/ui/subscription_plan_screen/subscription_list_screen.dart';
import 'package:driver/utils/Preferences.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/driver_api.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:get/get.dart';

class SplashController extends GetxController {
  @override
  void onInit() {
    // TODO: implement onInit
    Timer(const Duration(seconds: 3), () => redirectScreen());
    super.onInit();
  }

  redirectScreen() async {
    if (Preferences.getBoolean(Preferences.isFinishOnBoardingKey) == false) {
      Get.offAll(const OnBoardingScreen());
    } else {
      bool isLogin = await FireStoreUtils.isLogin();
      if (isLogin == true) {
        try {
          // Get profile from API
          final uid = FirebaseAuth.instance.currentUser!.uid;
          final response = await DriverApi.getProfile(uid);
          
          if (response['success'] == true && response['driver'] != null) {
            DriverUserModel userModel = DriverUserModel.fromJson(response['driver']);
            
            // Check subscription expiry
            bool isPlanExpire = false;
            if (userModel.subscriptionPlanId != null) {
              if (userModel.subscriptionExpiryDate == null) {
                // If no expiry date set, check if plan has unlimited duration
                isPlanExpire = false; // Assume active if no expiry
              } else {
                DateTime expiryDate = userModel.subscriptionExpiryDate!.toDate();
                isPlanExpire = expiryDate.isBefore(DateTime.now());
              }
            } else {
              isPlanExpire = true; // No subscription
            }
            
            // Navigate based on subscription status
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
            // API failed, go to dashboard
            Get.offAll(const DashBoardScreen());
          }
        } catch (e) {
          print('❌ Error in splash: $e');
          Get.offAll(const DashBoardScreen());
        }
      } else {
        Get.offAll(const LoginScreen());
      }
    }
  }
}
