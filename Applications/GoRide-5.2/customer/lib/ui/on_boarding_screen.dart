import 'package:cached_network_image/cached_network_image.dart';
import 'package:customer/constant/constant.dart';
// import 'package:customer/utils/responsive.dart';
import 'package:customer/controller/on_boarding_controller.dart';
import 'package:customer/themes/app_colors.dart';
import 'package:customer/themes/button_them.dart';
import 'package:customer/ui/auth_screen/login_screen.dart';
import 'package:customer/utils/Preferences.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
// Google Fonts replaced with local fonts

class OnBoardingScreen extends StatelessWidget {
  const OnBoardingScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return GetBuilder<OnBoardingController>(
        init: OnBoardingController(),
        builder: (controller) {
          return Scaffold(
            backgroundColor: Theme.of(context).colorScheme.background,
            body: SafeArea(
              child: Column(
                children: [
                  Expanded(
                    child: PageView.builder(
                      controller: controller.pageController,
                      onPageChanged: controller.selectedPageIndex,
                      itemCount: controller.onBoardingList.length,
                      itemBuilder: (context, index) {
                        return Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Container(
                              height: MediaQuery.of(context).size.height * 0.5,
                              margin: const EdgeInsets.only(bottom: 50),
                              child: Image.asset(
                                controller.onBoardingList[index].image.toString(),
                                fit: BoxFit.contain,
                                errorBuilder: (context, error, stackTrace) => 
                                  Icon(Icons.image, size: 100, color: Colors.grey),
                              ),
                            ),
                            Text(
                              controller.onBoardingList[index].title?.first.title ?? "Title",
                              textAlign: TextAlign.center,
                              style: TextStyle(fontSize: 18, fontWeight: FontWeight.w600, letterSpacing: 1.5),
                            ),
                            Container(
                              margin: const EdgeInsets.only(left: 22, right: 22, top: 15),
                              child: Text(
                                controller.onBoardingList[index].description?.first.description ?? "Description",
                                textAlign: TextAlign.center,
                                style: TextStyle(fontWeight: FontWeight.w400, letterSpacing: 1.5),
                              ),
                            ),
                          ],
                        );
                      },
                    ),
                  ),
                  Container(
                    margin: const EdgeInsets.only(bottom: 70),
                    height: 50,
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: List.generate(
                        controller.onBoardingList.length,
                        (index) => Container(
                          margin: const EdgeInsets.all(4),
                          width: 12,
                          height: 12,
                          decoration: BoxDecoration(
                            color: controller.selectedPageIndex.value == index ? AppColors.primary : Colors.grey.shade300,
                            shape: BoxShape.circle,
                          ),
                        ),
                      ),
                    ),
                  ),
                  Container(
                    margin: const EdgeInsets.only(left: 40, right: 40, bottom: 40),
                    child: ButtonThem.buildButton(
                      context,
                      title: controller.selectedPageIndex.value == controller.onBoardingList.length - 1 ? "Get Started".tr : "Next".tr,
                      onPress: () {
                        if (controller.selectedPageIndex.value == controller.onBoardingList.length - 1) {
                          Preferences.setBoolean(Preferences.isFinishOnBoardingKey, true);
                          Get.offAll(const LoginScreen());
                        } else {
                          controller.pageController.nextPage(duration: const Duration(milliseconds: 300), curve: Curves.ease);
                        }
                      },
                    ),
                  )
                ],
              ),
            ),
          );
        });
  }
}