import 'package:customer/constant/constant.dart';
import 'package:customer/model/language_model.dart';
import 'package:customer/utils/Preferences.dart';
import 'package:customer/utils/fire_store_utils.dart';
import 'package:get/get.dart';

class SettingController extends GetxController {
  @override
  void onInit() {
    // TODO: implement onInit
    getLanguage();
    super.onInit();
  }

  RxBool isLoading = true.obs;
  RxList<LanguageModel> languageList = <LanguageModel>[].obs;
  RxList<String> modeList = <String>['Light mode', 'Dark mode'].obs;
  Rx<LanguageModel> selectedLanguage = LanguageModel().obs;
  Rx<String> selectedMode = "".obs;

  getLanguage() async {
    // DEMO: Load static language list
    await Future.delayed(Duration(milliseconds: 500)); // Simulate loading
    
    languageList.value = [
      LanguageModel(id: "1", name: "English", code: "en", isDefault: true),
      LanguageModel(id: "2", name: "Español", code: "es", isDefault: false),
      LanguageModel(id: "3", name: "Français", code: "fr", isDefault: false),
      LanguageModel(id: "4", name: "Deutsch", code: "de", isDefault: false),
    ];
    
    // Set default language if none selected
    if (Preferences.getString(Preferences.languageCodeKey).toString().isNotEmpty) {
      LanguageModel pref = Constant.getLanguage();
      for (var element in languageList) {
        if (element.id == pref.id) {
          selectedLanguage.value = element;
        }
      }
    } else {
      selectedLanguage.value = languageList.first; // Default to English
    }
    
    // Set theme mode
    if (Preferences.getString(Preferences.themKey).toString().isNotEmpty) {
      selectedMode.value = Preferences.getString(Preferences.themKey).toString();
    } else {
      selectedMode.value = "Light mode"; // Default to light mode
    }
    
    isLoading.value = false;
    update();
  }
}
