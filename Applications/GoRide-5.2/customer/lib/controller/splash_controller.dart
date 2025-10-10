import 'dart:async';

// import 'package:customer/services/FirebaseFirestoreService.dart';
import 'package:customer/utils/fire_store_utils.dart';
import 'package:customer/ui/auth_screen/login_screen.dart';
import 'package:customer/ui/dashboard_screen.dart';
import 'package:customer/ui/on_boarding_screen.dart';
import 'package:customer/utils/Preferences.dart';
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
    if (Preferences.getBoolean(Preferences.isFinishOnBoardingKey) == false) {
      Get.offAll(const OnBoardingScreen());
    } else {
      // Check if user has valid token
      String? token = Preferences.getToken();
      if (token != null && token.isNotEmpty) {
        Get.offAll(const DashBoardScreen());
      } else {
        Get.offAll(const LoginScreen());
      }
    }
  }
}