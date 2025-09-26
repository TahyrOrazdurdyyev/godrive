import 'package:customer/themes/app_colors.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/responsive.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/DarkThemeProvider.dart';
// Google Fonts replaced with local fonts
import 'package:flutter/material.dart';
// Google Fonts replaced with local fonts

import 'package:provider/provider.dart';
// Google Fonts replaced with local fonts

class ButtonThem {
  const ButtonThem({Key? key});

  static buildButton(
    BuildContext context, {
    required String title,
    double btnHeight = 48,
    double txtSize = 14,
    double btnWidthRatio = 0.9,
    double btnRadius = 6,
    required Function() onPress,
    bool isVisible = true,
  }) {
    final themeChange = Provider.of<DarkThemeProvider>(context);

    return Visibility(
      visible: isVisible,
      child: SizedBox(
        width: Responsive.width(100, context) * btnWidthRatio,
        child: MaterialButton(
          onPressed: onPress,
          height: btnHeight,
          elevation: 0.5,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(btnRadius),
          ),
          color: themeChange.getThem() ? AppColors.darkModePrimary : AppColors.primary,
          child: Text(
            title.toUpperCase(),
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: txtSize, fontWeight: FontWeight.w600),
          ),
        ),
      ),
    );
  }

  static buildBorderButton(
    BuildContext context, {
    required String title,
    double btnHeight = 48,
    double txtSize = 14,
    double btnWidthRatio = 0.9,
    double borderRadius = 6,
    required Function() onPress,
    bool isVisible = true,
    bool iconVisibility = false,
    String iconAssetImage = '',
    Color? iconColor,
  }) {
    final themeChange = Provider.of<DarkThemeProvider>(context);

    return Visibility(
      visible: isVisible,
      child: SizedBox(
        width: Responsive.width(100, context) * btnWidthRatio,
        height: btnHeight,
        child: ElevatedButton(
          style: ButtonStyle(
            backgroundColor: MaterialStateProperty.all<Color>(themeChange.getThem() ? Colors.transparent : Colors.white),
            foregroundColor: MaterialStateProperty.all<Color>(themeChange.getThem() ? AppColors.darkModePrimary : Colors.white),
            shape: MaterialStateProperty.all<RoundedRectangleBorder>(
              RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(borderRadius),
                side: BorderSide(
                  color: themeChange.getThem() ? AppColors.darkModePrimary : AppColors.primary,
                ),
              ),
            ),
          ),
          onPressed: onPress,
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.center,
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Visibility(
                visible: iconVisibility,
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 10),
                  child: Image.asset(iconAssetImage, fit: BoxFit.cover, width: 32,color: iconColor,),
                ),
              ),
              Text(
                title.toUpperCase(),
                textAlign: TextAlign.center,
                style: TextStyle(color: themeChange.getThem() ? AppColors.darkModePrimary : AppColors.primary, fontSize: txtSize, fontWeight: FontWeight.w600),
              ),
            ],
          ),
        ),
      ),
    );
  }

  static roundButton(
    BuildContext context, {
    required String title,
    required Color btnColor,
    required Color txtColor,
    double btnHeight = 48,
    double txtSize = 14,
    double btnWidthRatio = 0.9,
    required Function() onPress,
    bool isVisible = true,
  }) {
    return Visibility(
      visible: isVisible,
      child: SizedBox(
        width: Responsive.width(100, context) * btnWidthRatio,
        child: MaterialButton(
          onPressed: onPress,
          height: btnHeight,
          elevation: 0.5,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(30),
          ),
          color: btnColor,
          child: Text(
            title.toUpperCase(),
            textAlign: TextAlign.center,
            style: TextStyle(color: txtColor, fontSize: txtSize, fontWeight: FontWeight.w600),
          ),
        ),
      ),
    );
  }
}
