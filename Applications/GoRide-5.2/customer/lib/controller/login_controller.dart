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
import 'package:firebase_auth/firebase_auth.dart';

class LoginController extends GetxController {
  Rx<TextEditingController> phoneNumberController = TextEditingController().obs;
  RxString countryCode = "+1".obs;

  Rx<GlobalKey<FormState>> formKey = GlobalKey<FormState>().obs;

  sendCode() async {
    try {
      ShowToastDialog.showLoader("Please wait");
      
      String phoneNumber = countryCode.value + phoneNumberController.value.text;
      
      await FirebaseAuth.instance.verifyPhoneNumber(
        phoneNumber: phoneNumber,
        verificationCompleted: (PhoneAuthCredential credential) async {
          // Auto-verification completed
          UserCredential userCredential = await FirebaseAuth.instance.signInWithCredential(credential);
          await _handlePhoneAuthSuccess(userCredential);
        },
        verificationFailed: (FirebaseAuthException e) {
          ShowToastDialog.closeLoader();
          if (e.code == 'too-many-requests') {
            ShowToastDialog.showToast("Too many requests. Please wait a few minutes and try again.");
          } else if (e.code == 'invalid-phone-number') {
            ShowToastDialog.showToast("Invalid phone number format.");
          } else {
            ShowToastDialog.showToast("Verification failed: ${e.message}");
          }
          debugPrint("Phone verification failed: ${e.code} - ${e.message}");
        },
        codeSent: (String verificationId, int? resendToken) {
          ShowToastDialog.closeLoader();
          Get.to(() => OtpScreen(), arguments: {
            'verificationId': verificationId,
            'phoneNumber': phoneNumber,
            'countryCode': countryCode.value,
          });
        },
        codeAutoRetrievalTimeout: (String verificationId) {
          debugPrint("Auto retrieval timeout");
        },
      );
      
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast("Something went wrong. Please try again.");
      debugPrint("Phone auth error: $e");
    }
  }

  Future<void> _handlePhoneAuthSuccess(UserCredential userCredential) async {
    try {
      User? firebaseUser = userCredential.user;
      if (firebaseUser != null) {
        // Login with Laravel API using Firebase user info
        UserModel? user = await LaravelService.loginUser(
          firebaseUid: firebaseUser.uid,
          email: firebaseUser.email ?? '',
          fullName: firebaseUser.displayName ?? 'User',
          phoneNumber: firebaseUser.phoneNumber ?? phoneNumberController.value.text,
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
      }
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast("Login failed. Please try again.");
      debugPrint("Laravel login error: $e");
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

      // Get Firebase credential for Google user
      final GoogleSignInAuthentication googleAuth = await googleUser.authentication;
      final credential = GoogleAuthProvider.credential(
        accessToken: googleAuth.accessToken,
        idToken: googleAuth.idToken,
      );
      
      // Sign in to Firebase
      final UserCredential userCredential = await FirebaseAuth.instance.signInWithCredential(credential);
      
      // Login with Laravel API using Firebase UID
      UserModel? user = await LaravelService.loginUser(
        firebaseUid: userCredential.user!.uid,
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
