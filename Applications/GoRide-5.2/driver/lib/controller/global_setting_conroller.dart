import 'dart:convert';
import 'dart:developer';

import 'package:driver/constant/constant.dart';
import 'package:driver/model/currency_model.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/model/language_model.dart';
import 'package:driver/services/localization_service.dart';
import 'package:driver/utils/Preferences.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/driver_api.dart';
import 'package:driver/utils/notification_service.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:get/get.dart';

class GlobalSettingController extends GetxController {
  RxBool isLoading = true.obs;
  @override
  void onInit() {
    // TODO: implement onInit
    notificationInit();
    getCurrentCurrency();
    super.onInit();
  }

  getCurrentCurrency() async {
    // Set default language
    if (Preferences.getString(Preferences.languageCodeKey).toString().isNotEmpty) {
      LanguageModel languageModel = Constant.getLanguage();
      LocalizationService().changeLocale(languageModel.code.toString());
    } else {
      // Default to Russian (Turkmenistan common language)
      LanguageModel defaultLanguage = LanguageModel(
        id: "ru",
        code: "ru",
        name: "Русский",
        isDefault: true,
      );
      Preferences.setString(Preferences.languageCodeKey, jsonEncode(defaultLanguage));
      LocalizationService().changeLocale(defaultLanguage.code.toString());
    }

    // Hardcoded TMT currency (Turkmenistan Manat)
    Constant.currencyModel = CurrencyModel(
      id: "tmt", 
      code: "TMT", 
      decimalDigits: 2, 
      enable: true, 
      name: "Turkmenistan Manat", 
      symbol: "m", 
      symbolAtRight: false
    );
    print('✅ Currency set to TMT (Turkmenistan Manat)');

    // Google API Key already in Constant.mapAPIKey (no Firestore needed)

    isLoading.value = false;
    update();
  }

  NotificationService notificationService = NotificationService();

  notificationInit() {
    notificationService.initInfo().then((value) async {
      String token = await NotificationService.getToken();
      log(":::::::TOKEN:::::: $token");

      if (FirebaseAuth.instance.currentUser != null) {
        try {
          // Update FCM token via API
          final uid = FireStoreUtils.getCurrentUid();
          final response = await DriverApi.updateFcmToken(uid: uid, fcmToken: token);
          
          if (response['success'] == true) {
            log('✅ FCM token updated via API');
          } else {
            log('⚠️ FCM token update failed');
          }
        } catch (e) {
          print('⚠️ FCM token update failed: $e');
        }
      }
    }).catchError((error) {
      print('⚠️ Notification init failed: $error');
    });
  }
}
