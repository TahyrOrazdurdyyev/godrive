import 'dart:async';

import 'package:customer/utils/fire_store_utils.dart';
import 'package:customer/ui/auth_screen/login_screen.dart';
import 'package:customer/ui/dashboard_screen.dart';
import 'package:customer/ui/on_boarding_screen.dart';
import 'package:customer/utils/Preferences.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:get/get.dart';

class SplashController extends GetxController {
  @override
  void onInit() {
    Timer(const Duration(seconds: 3), () {
      redirectScreen();
    });
    super.onInit();
  }

  redirectScreen() async {
    // Check if onboarding is completed
    if (Preferences.getBoolean(Preferences.isFinishOnBoardingKey) == false) {
      Get.offAll(const OnBoardingScreen());
    } else {
      // Check if user is logged in with Firebase
      User? currentUser = FirebaseAuth.instance.currentUser;
      if (currentUser != null) {
        // User is logged in with Firebase
        Get.offAll(const DashBoardScreen());
      } else {
        // User is not logged in
        Get.offAll(const LoginScreen());
      }
    }
  }
}