import 'package:driver/constant/show_toast_dialog.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/driver_api.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';

class ProfileController extends GetxController {
  RxBool isLoading = true.obs;
  Rx<DriverUserModel> driverModel = DriverUserModel().obs;

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
      // Get profile from Laravel API
      final uid = FireStoreUtils.getCurrentUid();
      final response = await DriverApi.getProfile(uid);
      
      if (response['success'] == true) {
        final driver = response['driver'];
        
        // Convert API response to DriverUserModel
        driverModel.value = DriverUserModel(
          id: uid,
          fullName: driver['full_name'],
          email: driver['email'],
          phoneNumber: driver['phone']?.replaceAll(driver['country_code'] ?? '', ''),
          countryCode: driver['country_code'],
          profilePic: driver['profile_pic'],
        );

        phoneNumberController.value.text = driverModel.value.phoneNumber.toString();
        countryCode.value = driverModel.value.countryCode.toString();
        emailController.value.text = driverModel.value.email.toString();
        fullNameController.value.text = driverModel.value.fullName.toString();
        profileImage.value = driverModel.value.profilePic ?? '';
        isLoading.value = false;
      } else {
        // API failed
        print('❌ Failed to load driver profile from API');
        isLoading.value = false;
      }
    } catch (e) {
      print('❌ Error loading profile: $e');
      isLoading.value = false;
    }
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
      ShowToastDialog.showToast("Failed to Pick : \n $e");
    }
  }

}
