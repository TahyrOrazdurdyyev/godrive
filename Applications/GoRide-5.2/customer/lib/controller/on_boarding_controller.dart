import 'package:customer/model/on_boarding_model.dart';
import 'package:customer/model/language_title.dart';
import 'package:customer/model/language_description.dart';
import 'package:customer/utils/fire_store_utils.dart';
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
        title: [LanguageTitle(title: "Book Your Ride", type: "en")],
        description: [LanguageDescription(description: "Easy and quick ride booking with just a few taps", type: "en")],
        image: "assets/images/onboarding_1.png"
      ),
      OnBoardingModel(
        id: "2", 
        title: [LanguageTitle(title: "Track Your Driver", type: "en")],
        description: [LanguageDescription(description: "Real-time tracking of your driver and estimated arrival time", type: "en")],
        image: "assets/images/onboarding_2.png"
      ),
      OnBoardingModel(
        id: "3",
        title: [LanguageTitle(title: "Safe & Secure", type: "en")],
        description: [LanguageDescription(description: "Your safety is our priority with verified drivers", type: "en")],
        image: "assets/images/onboarding_3.png"
      ),
    ];
    isLoading.value = false;
    update();
  }
}
