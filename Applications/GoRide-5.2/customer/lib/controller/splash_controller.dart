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
      // TEMPORARY: Skip Firebase login check for demo
      // bool isLogin = await FireStoreUtils.isLogin();
      bool isLogin = false; // Always go to login for demo
      if (isLogin == true) {
        Get.offAll(const DashBoardScreen());
      } else {
        Get.offAll(const LoginScreen());
      }
    }
  }
}