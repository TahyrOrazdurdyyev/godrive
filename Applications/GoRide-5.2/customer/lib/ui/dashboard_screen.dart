import 'package:cached_network_image/cached_network_image.dart';
// Google Fonts replaced with local fonts
import 'package:customer/constant/constant.dart';
// Google Fonts replaced with local fonts
import 'package:customer/controller/dash_board_controller.dart';
// Google Fonts replaced with local fonts
import 'package:customer/model/user_model.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/app_colors.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/responsive.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/DarkThemeProvider.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/fire_store_utils.dart';
// Google Fonts replaced with local fonts
import 'package:flutter/material.dart';
// Google Fonts replaced with local fonts
import 'package:flutter_svg/flutter_svg.dart';
// Google Fonts replaced with local fonts
import 'package:get/get.dart';
// Google Fonts replaced with local fonts

import 'package:provider/provider.dart';
// Google Fonts replaced with local fonts

class DashBoardScreen extends StatelessWidget {
  const DashBoardScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return GetX<DashBoardController>(
        init: DashBoardController(),
        builder: (controller) {
          return Scaffold(
            appBar: AppBar(
              backgroundColor: AppColors.primary,
              title: controller.selectedDrawerIndex.value != 0 && controller.selectedDrawerIndex.value != 6
                  ? Text(
                      controller.drawerItems[controller.selectedDrawerIndex.value].title,
                      style: const TextStyle(
                        color: Colors.white,
                      ),
                    )
                  : const Text(""),
              leading: Builder(builder: (context) {
                return InkWell(
                  onTap: () {
                    Scaffold.of(context).openDrawer();
                  },
                  child: Padding(
                    padding: const EdgeInsets.only(left: 10, right: 20, top: 20, bottom: 20),
                    child: SvgPicture.asset('assets/icons/ic_humber.svg'),
                  ),
                );
              }),
              actions: [
                controller.selectedDrawerIndex.value == 0
                    ? // TEMPORARY: Skip Firebase user profile for demo
                      InkWell(
                        onTap: () {
                          controller.selectedDrawerIndex(8);
                        },
                        child: Padding(
                          padding: const EdgeInsets.all(10.0),
                          child: ClipOval(
                            child: Container(
                              height: 40,
                              width: 36,
                              decoration: BoxDecoration(
                                color: Colors.grey[300],
                                shape: BoxShape.circle,
                              ),
                              child: Icon(
                                Icons.person,
                                color: Colors.grey[600],
                                size: 24,
                              ),
                            ),
                          ),
                        ),
                      )
                    : Container(),
              ],
            ),
            drawer: buildAppDrawer(context, controller),
            body: WillPopScope(onWillPop: controller.onWillPop, child: controller.getDrawerItemWidget(controller.selectedDrawerIndex.value)),
          );
        });
  }

  buildAppDrawer(BuildContext context, DashBoardController controller) {
    final themeChange = Provider.of<DarkThemeProvider>(context);
    RxList<DrawerItem> drawerItems = [
      DrawerItem('City'.tr, "assets/icons/ic_city.svg"),
      DrawerItem('OutStation'.tr, "assets/icons/ic_intercity.svg"),
      DrawerItem('Rides'.tr, "assets/icons/ic_order.svg"),
      DrawerItem('OutStation Rides'.tr, "assets/icons/ic_order.svg"),
      DrawerItem('My Wallet'.tr, "assets/icons/ic_wallet.svg"),
      DrawerItem('Settings'.tr, "assets/icons/ic_settings.svg"),
      DrawerItem('Referral a friends'.tr, "assets/icons/ic_referral.svg"),
      DrawerItem('Inbox'.tr, "assets/icons/ic_inbox.svg"),
      DrawerItem('Profile'.tr, "assets/icons/ic_profile.svg"),
      DrawerItem('Contact us'.tr, "assets/icons/ic_contact_us.svg"),
      DrawerItem('FAQs'.tr, "assets/icons/ic_faq.svg"),
      DrawerItem('Log out'.tr, "assets/icons/ic_logout.svg"),
    ].obs;
    var drawerOptions = <Widget>[];
    for (var i = 0; i < drawerItems.length; i++) {
      var d = drawerItems[i];
      drawerOptions.add(InkWell(
        onTap: () {
          controller.onSelectItem(i);
        },
        child: Padding(
          padding: const EdgeInsets.all(8.0),
          child: Container(
            decoration: BoxDecoration(
                color: i == controller.selectedDrawerIndex.value ? Theme.of(context).colorScheme.primary : Colors.transparent,
                borderRadius: const BorderRadius.all(Radius.circular(10))),
            padding: const EdgeInsets.all(12),
            child: Row(
              children: [
                SvgPicture.asset(
                  d.icon,
                  width: 20,
                  color: i == controller.selectedDrawerIndex.value
                      ? themeChange.getThem()
                          ? Colors.black
                          : Colors.white
                      : themeChange.getThem()
                          ? Colors.white
                          : AppColors.drawerIcon,
                ),
                const SizedBox(
                  width: 20,
                ),
                Text(
                  d.title,
                  style: TextStyle(
                      color: i == controller.selectedDrawerIndex.value
                          ? themeChange.getThem()
                              ? Colors.black
                              : Colors.white
                          : themeChange.getThem()
                              ? Colors.white
                              : Colors.black,
                      fontWeight: FontWeight.w500),
                )
              ],
            ),
          ),
        ),
      ));
    }
    return Drawer(
      backgroundColor: Theme.of(context).colorScheme.background,
      child: ListView(
        children: [
          DrawerHeader(
            child: // TEMPORARY: Static user profile for demo
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    ClipRRect(
                      borderRadius: BorderRadius.circular(60),
                      child: Container(
                        height: Responsive.width(20, context),
                        width: Responsive.width(20, context),
                        decoration: BoxDecoration(
                          color: Colors.grey[300],
                          borderRadius: BorderRadius.circular(60),
                        ),
                        child: Icon(
                          Icons.person,
                          color: Colors.grey[600],
                          size: 40,
                        ),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.only(top: 6),
                      child: Text("Demo User", style: TextStyle(fontWeight: FontWeight.w500)),
                    ),
                    Padding(
                      padding: const EdgeInsets.only(top: 2),
                      child: Text(
                        "demo@goride.com",
                        style: TextStyle(),
                      ),
                    )
                  ],
                ),
          ),
          Column(children: drawerOptions),
        ],
      ),
    );
  }
}
