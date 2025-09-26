import 'package:cached_network_image/cached_network_image.dart';
// Google Fonts replaced with local fonts
import 'package:cloud_firestore/cloud_firestore.dart';
// Google Fonts replaced with local fonts
import 'package:customer/constant/constant.dart';
// Google Fonts replaced with local fonts
import 'package:customer/model/driver_user_model.dart';
// Google Fonts replaced with local fonts
import 'package:customer/model/inbox_model.dart';
// Google Fonts replaced with local fonts
import 'package:customer/model/user_model.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/app_colors.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/responsive.dart';
// Google Fonts replaced with local fonts
import 'package:customer/ui/chat_screen/chat_screen.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/DarkThemeProvider.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/fire_store_utils.dart';
// Google Fonts replaced with local fonts
import 'package:customer/widget/firebase_pagination/src/firestore_pagination.dart';
// Google Fonts replaced with local fonts
import 'package:customer/widget/firebase_pagination/src/models/view_type.dart';
// Google Fonts replaced with local fonts
import 'package:flutter/material.dart';
// Google Fonts replaced with local fonts
import 'package:get/get.dart';
// Google Fonts replaced with local fonts

import 'package:provider/provider.dart';
// Google Fonts replaced with local fonts

class InboxScreen extends StatelessWidget {
  const InboxScreen({super.key});

  // DEMO: Static inbox data
  List<InboxModel> getDemoInboxData() {
    return [
      InboxModel(
        customerId: "demo_customer_1",
        customerName: "John Smith",
        customerProfileImage: "",
        orderId: "ORD001234",
        createdAt: Timestamp.fromDate(DateTime.now().subtract(Duration(hours: 2))),
        lastMessage: "Thanks for the ride!",
        driverId: "demo_driver_1",
        driverName: "Driver John",
        driverProfileImage: "",
      ),
      InboxModel(
        customerId: "demo_customer_2",
        customerName: "Emma Wilson",
        customerProfileImage: "",
        orderId: "ORD001235",
        createdAt: Timestamp.fromDate(DateTime.now().subtract(Duration(hours: 5))),
        lastMessage: "Driver was very professional",
        driverId: "demo_driver_2",
        driverName: "Driver Mike",
        driverProfileImage: "",
      ),
      InboxModel(
        customerId: "demo_customer_3", 
        customerName: "Mike Johnson",
        customerProfileImage: "",
        orderId: "ORD001236",
        createdAt: Timestamp.fromDate(DateTime.now().subtract(Duration(days: 1))),
        lastMessage: "Great service as always",
        driverId: "demo_driver_3",
        driverName: "Driver Sarah",
        driverProfileImage: "",
      ),
    ];
  }

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);
    final demoInboxData = getDemoInboxData();

    return Scaffold(
      backgroundColor: AppColors.primary,
      body: Column(
        children: [
          SizedBox(
            height: Responsive.width(6, context),
            width: Responsive.width(100, context),
          ),
          Expanded(
            child: Container(
              decoration: BoxDecoration(color: Theme.of(context).colorScheme.background, borderRadius: const BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25))),
              child: Padding(
                padding: const EdgeInsets.only(top: 10),
                child: demoInboxData.isEmpty 
                  ? Center(child: Text("No Conversion found".tr))
                  : ListView.builder(
                      physics: const BouncingScrollPhysics(),
                      itemCount: demoInboxData.length,
                      itemBuilder: (context, index) {
                        InboxModel inboxModel = demoInboxData[index];
                        return InkWell(
                          onTap: () {
                            // DEMO: Show demo message instead of opening chat
                            Get.snackbar(
                              "Demo Mode",
                              "Chat feature available in full version",
                              snackPosition: SnackPosition.BOTTOM,
                            );
                          },
                      child: Padding(
                        padding: const EdgeInsets.all(4.0),
                        child: Container(
                          decoration: BoxDecoration(
                            color: themeChange.getThem() ? AppColors.darkContainerBackground : AppColors.containerBackground,
                            borderRadius: const BorderRadius.all(Radius.circular(10)),
                            border: Border.all(color: themeChange.getThem() ? AppColors.darkContainerBorder : AppColors.containerBorder, width: 0.5),
                            boxShadow: themeChange.getThem()
                                ? null
                                : [
                                    BoxShadow(
                                      color: Colors.grey.withOpacity(0.5),
                                      blurRadius: 8,
                                      offset: const Offset(0, 2), // changes position of shadow
                                    ),
                                  ],
                          ),
                          child: Padding(
                            padding: const EdgeInsets.symmetric(vertical: 5),
                            child: ListTile(
                              leading: ClipOval(
                                child: Container(
                                  width: 40,
                                  height: 40,
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
                              title: Row(
                                children: [
                                  Expanded(
                                      child: Text(
                                    inboxModel.customerName.toString(),
                                    style: TextStyle(fontWeight: FontWeight.w600),
                                  )),
                                  Text(Constant.dateFormatTimestamp(inboxModel.createdAt), style: TextStyle(fontSize: 12, fontWeight: FontWeight.w400)),
                                ],
                              ),
                              subtitle: Text("Ride Id : #${inboxModel.orderId}".tr),
                            ),
                          ),
                        ),
                      ),
                        );
                      },
                    ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
