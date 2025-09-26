import 'dart:convert';
import 'dart:math';

import 'package:crypto/crypto.dart';
import 'package:customer/constant/show_toast_dialog.dart';
import 'package:customer/ui/auth_screen/otp_screen.dart';
import 'package:customer/ui/dashboard_screen.dart';
import 'package:customer/services/laravel_service.dart';
import 'package:customer/model/user_model.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:sign_in_with_apple/sign_in_with_apple.dart';

class LoginController extends GetxController {
  Rx<TextEditingController> phoneNumberController = TextEditingController().obs;
  RxString countryCode = "+1".obs;

  Rx<GlobalKey<FormState>> formKey = GlobalKey<FormState>().obs;

  sendCode() async {
    try {
      ShowToastDialog.showLoader("Please wait");
      
      // For demo purposes, create a demo user and login directly
      UserModel? user = await LaravelService.loginUser(
        firebaseUid: 'demo_${DateTime.now().millisecondsSinceEpoch}',
        email: 'demo@goride.com',
        fullName: 'Demo User',
        phoneNumber: phoneNumberController.value.text,
        countryCode: countryCode.value,
        loginType: 'phone',
      );
      
      ShowToastDialog.closeLoader();
      
      if (user != null) {
        ShowToastDialog.showToast("Login successful!");
        Get.offAll(const DashBoardScreen());
      } else {
        ShowToastDialog.showToast("Login failed. Please try again.");
      }
      
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast("Something went wrong. Please try again.");
      debugPrint("Login error: $e");
    }
  }

  Future<UserModel?> signInWithGoogle() async {
    try {
      ShowToastDialog.showLoader("Signing in with Google...");
      
      final GoogleSignInAccount? googleUser = await GoogleSignIn().signIn().catchError((error) {
        debugPrint("catchError--->$error");
        ShowToastDialog.closeLoader();
        ShowToastDialog.showToast("Something went wrong");
        return null;
      });

      if (googleUser == null) {
        ShowToastDialog.closeLoader();
        return null;
      }

      // Login with Laravel API using Google account info
      UserModel? user = await LaravelService.loginUser(
        firebaseUid: googleUser.id,
        email: googleUser.email,
        fullName: googleUser.displayName ?? 'Google User',
        loginType: 'google',
        profilePic: googleUser.photoUrl,
      );
      
      ShowToastDialog.closeLoader();
      
      if (user != null) {
        ShowToastDialog.showToast("Google login successful!");
        Get.offAll(const DashBoardScreen());
        return user;
      } else {
        ShowToastDialog.showToast("Login failed. Please try again.");
        return null;
      }
      
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast("Google sign-in failed");
      debugPrint("Google sign-in error: $e");
      return null;
    }
  }

  Future<Map<String, dynamic>?> signInWithApple() async {
    try {
      // Request credential for the currently signed in Apple account.
      AuthorizationCredentialAppleID appleCredential = await SignInWithApple.getAppleIDCredential(
        scopes: [
          AppleIDAuthorizationScopes.email,
          AppleIDAuthorizationScopes.fullName,
        ],
      );
      print(appleCredential);

      // Create an `OAuthCredential` from the credential returned by Apple.
      final oauthCredential = OAuthProvider("apple.com").credential(
        idToken: appleCredential.identityToken,
          accessToken: appleCredential.authorizationCode
      );

      // Sign in the user with Firebase. If the nonce we generated earlier does
      // not match the nonce in `appleCredential.identityToken`, sign in will fail.
      UserCredential userCredential = await FirebaseAuth.instance.signInWithCredential(oauthCredential);
      return {"appleCredential": appleCredential, "userCredential": userCredential};
    } catch (e) {
      debugPrint(e.toString());
    }
    return null;
  }

  String generateNonce([int length = 32]) {
    const charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVXYZabcdefghijklmnopqrstuvwxyz-._';
    final random = Random.secure();
    return List.generate(length, (_) => charset[random.nextInt(charset.length)]).join();
  }

  /// Returns the sha256 hash of [input] in hex notation.
  String sha256ofString(String input) {
    final bytes = utf8.encode(input);
    final digest = sha256.convert(bytes);
    return digest.toString();
  }
}
