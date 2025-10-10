import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:driver/constant/constant.dart';
import 'package:driver/constant/show_toast_dialog.dart';
import 'package:driver/model/driver_rules_model.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/model/service_model.dart';
import 'package:driver/model/vehicle_type_model.dart';
import 'package:driver/model/zone_model.dart';
import 'package:driver/themes/app_colors.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/driver_api.dart';
import 'package:driver/utils/service_api.dart';
import 'package:driver/utils/zone_api.dart';
import 'package:driver/utils/driver_rule_api.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';

class VehicleInformationController extends GetxController {
  Rx<TextEditingController> vehicleNumberController = TextEditingController().obs;
  Rx<TextEditingController> seatsController = TextEditingController().obs;
  Rx<TextEditingController> registrationDateController = TextEditingController().obs;
  Rx<TextEditingController> driverRulesController = TextEditingController().obs;
  Rx<TextEditingController> zoneNameController = TextEditingController().obs;
  Rx<TextEditingController> acPerKmRate = TextEditingController().obs;
  Rx<TextEditingController> nonAcPerKmRate = TextEditingController().obs;
  Rx<TextEditingController> acNonAcWithoutPerKmRate = TextEditingController().obs;
  Rx<DateTime?> selectedDate = DateTime.now().obs;

  RxBool isLoading = true.obs;

  Rx<String> selectedColor = "".obs;
  List<String> carColorList = <String>['Red', 'Black', 'White', 'Blue', 'Green', 'Orange', 'Silver', 'Gray', 'Yellow', 'Brown', 'Gold', 'Beige', 'Purple'].obs;
  List<String> sheetList = <String>['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15'].obs;

  @override
  void onInit() {
    // TODO: implement onInit
    getVehicleTye();
    super.onInit();
  }

  List<VehicleTypeModel> vehicleList = <VehicleTypeModel>[].obs;
  Rx<VehicleTypeModel> selectedVehicle = VehicleTypeModel().obs;
  var colors = [
    AppColors.serviceColor1,
    AppColors.serviceColor2,
    AppColors.serviceColor3,
  ];
  Rx<DriverUserModel> driverModel = DriverUserModel().obs;
  RxList<DriverRulesModel> driverRulesList = <DriverRulesModel>[].obs;
  RxList<DriverRulesModel> selectedDriverRulesList = <DriverRulesModel>[].obs;

  RxList<ServiceModel> serviceList = <ServiceModel>[].obs;
  Rx<ServiceModel> selectedServiceType = ServiceModel().obs;
  RxList<ZoneModel> zoneList = <ZoneModel>[].obs;
  RxList selectedZone = <String>[].obs;
  RxString zoneString = "".obs;

  getVehicleTye() async {
    try {
      // Get services from Laravel API
      final servicesResponse = await ServiceApi.getAllServices();
      if (servicesResponse['success'] == true) {
        serviceList.value = (servicesResponse['services'] as List)
            .map((s) => ServiceModel.fromJson(s))
            .toList();
      }
    } catch (e) {
      print('❌ Error loading services: $e');
    }

    try {
      // Get zones from Laravel API
      final zonesResponse = await ZoneApi.getAllZones();
      if (zonesResponse['success'] == true) {
        zoneList.value = (zonesResponse['zones'] as List)
            .map((z) => ZoneModel.fromJson(z))
            .toList();
      }
    } catch (e) {
      print('❌ Error loading zones: $e');
    }

    try {
      // Get driver profile from API
      final uid = FireStoreUtils.getCurrentUid();
      final response = await DriverApi.getProfile(uid);
      if (response['success'] == true && response['driver'] != null) {
        driverModel.value = DriverUserModel.fromJson(response['driver']);
          if (driverModel.value.vehicleInformation != null) {
            vehicleNumberController.value.text = driverModel.value.vehicleInformation!.vehicleNumber.toString();
            selectedDate.value = driverModel.value.vehicleInformation!.registrationDate!.toDate();
            registrationDateController.value.text = DateFormat("dd-MM-yyyy").format(selectedDate.value!);
            selectedColor.value = driverModel.value.vehicleInformation!.vehicleColor.toString();
            seatsController.value.text = driverModel.value.vehicleInformation!.seats ?? "2";
            if(driverModel.value.vehicleInformation!.acPerKmRate != null){
              acPerKmRate.value.text = driverModel.value.vehicleInformation!.acPerKmRate ?? '';
            }else{
              nonAcPerKmRate.value.text = driverModel.value.vehicleInformation!.nonAcPerKmRate ?? '';
              acNonAcWithoutPerKmRate.value.text = driverModel.value.vehicleInformation!.perKmRate ?? '';
            }
          }

          if (driverModel.value.zoneIds != null) {
            for (var element in driverModel.value.zoneIds!) {
              List<ZoneModel> list = zoneList.where((p0) => p0.id == element).toList();
              if (list.isNotEmpty) {
                selectedZone.add(element);
                zoneString.value = "$zoneString${zoneString.isEmpty ? "" : ","} ${Constant.localizationName(list.first.name)}";
              }
            }
            zoneNameController.value.text = zoneString.value;
          }
          for (var element in serviceList) {
            if (element.id == driverModel.value.serviceId) {
              print("====>");
              selectedServiceType.value = element;
            }
          }
        }
      });
    } catch (e) {
      print('❌ Error loading driver profile: $e');
    }

    try {
      // Get vehicle types from Laravel API
      final vehicleTypesResponse = await VehicleTypeApi.getAllVehicleTypes();
      if (vehicleTypesResponse['success'] == true) {
        vehicleList = (vehicleTypesResponse['vehicle_types'] as List)
            .map((v) => VehicleTypeModel.fromJson(v))
            .toList();
        if (driverModel.value.vehicleInformation != null) {
          for (var element in vehicleList) {
            if (element.id == driverModel.value.vehicleInformation!.vehicleTypeId) {
              selectedVehicle.value = element;
            }
          }
        }
      }
    } catch (e) {
      print('❌ Error loading vehicle types: $e');
    }

    try {
      // Get driver rules from Laravel API
      final driverRulesResponse = await DriverRuleApi.getAllDriverRules();
      if (driverRulesResponse['success'] == true) {
        driverRulesList.value = (driverRulesResponse['driver_rules'] as List)
            .map((r) => DriverRulesModel.fromJson(r))
            .toList();
        if (driverModel.value.vehicleInformation != null) {
          if (driverModel.value.vehicleInformation!.driverRules != null) {
            for (var element in driverModel.value.vehicleInformation!.driverRules!) {
              selectedDriverRulesList.add(element);
            }
          }
        }
      }
    } catch (e) {
      print('❌ Error loading driver rules: $e');
    }
    isLoading.value = false;
    update();
  }

  saveDetails() async {
    if (driverModel.value.serviceId == null) {
      driverModel.value.serviceId = selectedServiceType.value.id;
    }
    driverModel.value.zoneIds = selectedZone;

    driverModel.value.vehicleInformation = VehicleInformation(
        registrationDate: Timestamp.fromDate(selectedDate.value!),
        vehicleColor: selectedColor.value,
        vehicleNumber: vehicleNumberController.value.text,
        vehicleType: selectedVehicle.value.name,
        acPerKmRate: acPerKmRate.value.text,
        nonAcPerKmRate: nonAcPerKmRate.value.text,
        vehicleTypeId: selectedVehicle.value.id,
        seats: seatsController.value.text,
        perKmRate: acNonAcWithoutPerKmRate.value.text,
        driverRules: selectedDriverRulesList);

    try {
      // Try to update via API first
      final uid = FireStoreUtils.getCurrentUid();
      final response = await DriverApi.updateProfile(
        uid: uid,
        fullName: driverModel.value.fullName!,
        email: driverModel.value.email!,
        vehicleNumber: vehicleNumberController.value.text,
        vehicleType: selectedVehicle.value.name != null && selectedVehicle.value.name!.isNotEmpty 
            ? Constant.localizationName(selectedVehicle.value.name) 
            : "Unknown",
      );
      
      if (response['success'] == true) {
        print('✅ Vehicle info updated via API');
      } else {
        print('⚠️ API failed, using Firestore');
      }
    } catch (e) {
      print('❌ Error updating via API: $e');
    }
    
    // Update vehicle info via API
    try {
      await DriverApi.updateProfile(
        driverId: driverModel.value.id!,
        data: {
          'vehicle_information': driverModel.value.vehicleInformation?.toJson(),
        },
      );
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast("Information update successfully".tr);
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast("Failed to update information".tr);
    }
  }
}
