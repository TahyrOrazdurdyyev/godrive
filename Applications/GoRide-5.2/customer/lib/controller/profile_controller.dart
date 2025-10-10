import 'dart:convert';
import 'dart:io';
import 'package:customer/constant/show_toast_dialog.dart';
import 'package:customer/model/user_model.dart';
import 'package:customer/services/laravel_service.dart';
import 'package:customer/utils/fire_store_utils.dart';
import 'package:customer/utils/Preferences.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';

class ProfileController extends GetxController {
  RxBool isLoading = true.obs;
  Rx<UserModel> userModel = UserModel().obs;

  Rx<TextEditingController> fullNameController = TextEditingController().obs;
  Rx<TextEditingController> emailController = TextEditingController().obs;
  Rx<TextEditingController> phoneNumberController = TextEditingController().obs;
  RxString countryCode = "+1".obs;

  @override
  void onInit() {
    // TODO: implement onInit
    getData();
    super.onInit();
  }

  getData() async {
    try {
      // Load user data from preferences
      String? userJson = Preferences.getString(Preferences.user);
      if (userJson != null && userJson.isNotEmpty) {
        Map<String, dynamic> userData = json.decode(userJson);
        userModel.value = UserModel.fromJson(userData);
        
        // Update controllers with user data
        phoneNumberController.value.text = userModel.value.phoneNumber ?? '';
        countryCode.value = userModel.value.countryCode ?? '+1';
        emailController.value.text = userModel.value.email ?? '';
        fullNameController.value.text = userModel.value.fullName ?? '';
        profileImage.value = userModel.value.profilePic ?? '';
      } else {
        // If no user data, create empty user model
        userModel.value = UserModel();
      }
    } catch (e) {
      debugPrint("Error loading user data: $e");
      userModel.value = UserModel();
    }
    
    isLoading.value = false;
  }

  final ImagePicker _imagePicker = ImagePicker();
  RxString profileImage = "".obs;

  Future pickFile({required ImageSource source}) async {
    try {
      XFile? image = await _imagePicker.pickImage(source: source);
      if (image == null) return;
      Get.back();
      profileImage.value = image.path;
    } on PlatformException catch (e) {
      ShowToastDialog.showToast("Error picking image: ${e.message}");
    }
  }

  // Upload avatar to Laravel API
  Future<String?> uploadAvatarToLaravel(File imageFile) async {
    try {
      return await LaravelService.uploadCustomerAvatar(imageFile);
    } catch (e) {
      debugPrint("Error uploading avatar: $e");
      return null;
    }
  }

  // Update profile via Laravel API
  Future<bool> updateProfile() async {
    try {
      UserModel updatedUser = userModel.value;
      updatedUser.fullName = fullNameController.value.text;
      updatedUser.email = emailController.value.text;
      updatedUser.phoneNumber = phoneNumberController.value.text;
      updatedUser.countryCode = countryCode.value;
      updatedUser.profilePic = profileImage.value;

      // Update user in Laravel API
      UserModel? result = await LaravelService.updateCustomerProfile(
        fullName: updatedUser.fullName,
        email: updatedUser.email,
        phoneNumber: updatedUser.phoneNumber,
        countryCode: updatedUser.countryCode,
        profilePic: updatedUser.profilePic,
      );

      if (result != null) {
        // Save updated user to preferences
        userModel.value = result;
        await Preferences.setString(Preferences.user, json.encode(result.toJson()));
        return true;
      }
      return false;
    } catch (e) {
      debugPrint("Error updating profile: $e");
      return false;
    }
  }
}
