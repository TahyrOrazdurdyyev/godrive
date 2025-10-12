import 'dart:convert';
import 'dart:developer';

import 'package:customer/constant/constant.dart';
import 'package:customer/model/currency_model.dart';
import 'package:customer/model/language_model.dart';
import 'package:customer/model/user_model.dart';
import 'package:customer/services/localization_service.dart';
import 'package:customer/utils/Preferences.dart';
import 'package:customer/utils/fire_store_utils.dart';
import 'package:customer/utils/language_api.dart';
import 'package:customer/utils/user_api.dart';
import 'package:customer/utils/notification_service.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:get/get.dart';

class GlobalSettingController extends GetxController {
  @override
  void onInit() {
    // TEMPORARY: Offline mode - skip network calls
    // notificationInit();
    getCurrentCurrencyOffline();
    super.onInit();
  }

  getCurrentCurrency() async {
    if (Preferences.getString(Preferences.languageCodeKey).toString().isNotEmpty) {
      LanguageModel languageModel = Constant.getLanguage();
      LocalizationService().changeLocale(languageModel.code.toString());
    } else {
      try {
        final response = await LanguageApi.getAllLanguages();
        if (response['success'] == true && response['languages'] != null) {
          List<LanguageModel> languageList = (response['languages'] as List)
              .map((json) => LanguageModel.fromJson(json))
              .toList();

          if (languageList.where((element) => element.isDefault == true).isNotEmpty) {
            LanguageModel languageModel = languageList.firstWhere((element) => element.isDefault == true);
            Preferences.setString(Preferences.languageCodeKey, jsonEncode(languageModel));
            LocalizationService().changeLocale(languageModel.code.toString());
          }
        }
      } catch (e) {
        log('❌ Error loading languages: $e');
      }
    }

    await FireStoreUtils().getCurrency().then((value) {
      if (value != null) {
        Constant.currencyModel = value;
      } else {
        Constant.currencyModel =
            CurrencyModel(id: "", code: "USD", decimalDigits: 2, enable: true, name: "US Dollar", symbol: "\$", symbolAtRight: false);
      }
    });
    await FireStoreUtils().getSettings().then((value) {
      if (value != null) {
        // Settings loaded successfully
        log("Settings loaded: ${value.toString()}");
      }
    });
    update();
  }

  getCurrentCurrencyOffline() async {
    // Offline version without Firebase calls
    if (Preferences.getString(Preferences.languageCodeKey).toString().isNotEmpty) {
      LanguageModel languageModel = Constant.getLanguage();
      LocalizationService().changeLocale(languageModel.code.toString());
    } else {
      // Set default language without Firebase
      LanguageModel defaultLanguage = LanguageModel(
        id: "1", 
        name: "English", 
        code: "en", 
        isDefault: true
      );
      Preferences.setString(Preferences.languageCodeKey, jsonEncode(defaultLanguage));
      LocalizationService().changeLocale(defaultLanguage.code.toString());
    }

    // Set default currency without Firebase
    Constant.currencyModel = CurrencyModel(
      id: "", 
      code: "USD", 
      decimalDigits: 2, 
      enable: true, 
      name: "US Dollar", 
      symbol: "\$", 
      symbolAtRight: false
    );
    
    // Skip Firebase settings call
    log("GlobalSettingController initialized in offline mode");
    update();
  }

  NotificationService notificationService = NotificationService();

  notificationInit() {
    notificationService.initInfo().then((value) async {
      String token = await NotificationService.getToken();
      log(":::::::TOKEN:::::: $token");
      if (FirebaseAuth.instance.currentUser != null) {
        try {
          final uid = FireStoreUtils.getCurrentUid();
          final response = await UserApi.getProfile(uid);
          
          if (response['success'] == true && response['user'] != null) {
            final uid = response['user']['uid'];
            
            // Update FCM token via API
            await UserApi.updateFcmToken(
              uid: uid,
              fcmToken: token,
            );
          }
        } catch (e) {
          log('❌ Error updating FCM token: $e');
        }
      }
    });
  }
}
