import 'package:driver/model/on_boarding_model.dart';
import 'package:driver/model/language_title.dart';
import 'package:driver/model/language_description.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

class OnBoardingController extends GetxController {
  var selectedPageIndex = 0.obs;

  bool get isLastPage => selectedPageIndex.value == onBoardingList.length - 1;
  var pageController = PageController();

  @override
  void onInit() {
    // TODO: implement onInit
    getOnBoardingData();
    super.onInit();
  }

  RxBool isLoading = true.obs;
  RxList<OnBoardingModel> onBoardingList = <OnBoardingModel>[].obs;

  getOnBoardingData() async {
    // TEMPORARY: Use local data instead of Firebase
    onBoardingList.value = [
      OnBoardingModel(
        id: "1",
        title: [LanguageTitle(title: "Start Driving", type: "en")],
        description: [LanguageDescription(description: "Accept ride requests and start earning", type: "en")],
        image: "assets/images/onboarding_1.png"
      ),
      OnBoardingModel(
        id: "2", 
        title: [LanguageTitle(title: "Navigate Easily", type: "en")],
        description: [LanguageDescription(description: "Get turn-by-turn directions to pick up and drop off", type: "en")],
        image: "assets/images/onboarding_2.png"
      ),
      OnBoardingModel(
        id: "3",
        title: [LanguageTitle(title: "Earn More", type: "en")],
        description: [LanguageDescription(description: "Track your earnings and get paid weekly", type: "en")],
        image: "assets/images/onboarding_3.png"
      ),
    ];
    isLoading.value = false;
    update();
  }
}
