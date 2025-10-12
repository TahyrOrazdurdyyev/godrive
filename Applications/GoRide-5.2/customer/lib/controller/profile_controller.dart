import 'dart:convert';
import 'dart:io';
import 'package:customer/constant/show_toast_dialog.dart';
import 'package:customer/model/user_model.dart';
import 'package:customer/utils/user_api.dart';
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

  // Upload avatar - convert to base64 for API
  Future<String?> uploadAvatarToLaravel(File imageFile) async {
    try {
      final bytes = await imageFile.readAsBytes();
      final base64Image = base64Encode(bytes);
      return 'data:image/jpeg;base64,$base64Image';
    } catch (e) {
      debugPrint("Error uploading avatar: $e");
      return null;
    }
  }

  // Update profile via API
  Future<bool> updateProfile() async {
    try {
      final uid = FireStoreUtils.getCurrentUid();
      
      final response = await UserApi.updateProfile(
        uid: uid,
        fullName: fullNameController.value.text,
        email: emailController.value.text,
        phoneNumber: phoneNumberController.value.text,
        countryCode: countryCode.value,
        profilePic: profileImage.value,
      );

      if (response['success'] == true && response['user'] != null) {
        // Save updated user to preferences
        userModel.value = UserModel.fromJson(response['user']);
        await Preferences.setString(Preferences.user, json.encode(response['user']));
        return true;
      }
      return false;
    } catch (e) {
      debugPrint("Error updating profile: $e");
      return false;
    }
  }
}
