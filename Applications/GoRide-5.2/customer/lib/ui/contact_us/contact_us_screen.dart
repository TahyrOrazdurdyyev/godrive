import 'package:customer/constant/constant.dart';
// Google Fonts replaced with local fonts
import 'package:customer/constant/show_toast_dialog.dart';
// Google Fonts replaced with local fonts
import 'package:customer/controller/contact_us_controller.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/app_colors.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/button_them.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/responsive.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/text_field_them.dart';
// Google Fonts replaced with local fonts
import 'package:flutter/material.dart';
// Google Fonts replaced with local fonts
import 'package:flutter_email_sender/flutter_email_sender.dart';
// Google Fonts replaced with local fonts
import 'package:get/get.dart';
// Google Fonts replaced with local fonts


class ContactUsScreen extends StatelessWidget {
  const ContactUsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    // final themeChange = Provider.of<DarkThemeProvider>(context);
    return GetX<ContactUsController>(
        init: ContactUsController(),
        builder: (controller) {
          return Scaffold(
            backgroundColor: AppColors.primary,
            body: Column(
              children: [
                SizedBox(
                  height: Responsive.width(8, context),
                  width: Responsive.width(100, context),
                ),
                Expanded(
                  child: Container(
                    decoration: BoxDecoration(
                        color: Theme.of(context).colorScheme.background, borderRadius: const BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25))),
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 10),
                      child: controller.isLoading.value
                          ? Constant.loader()
                          : Padding(
                              padding: const EdgeInsets.only(top: 10),
                              child: DefaultTabController(
                                length: 2,
                                child: Padding(
                                  padding: const EdgeInsets.symmetric(horizontal: 10),
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text("Contact us".tr, style: TextStyle(fontSize: 20, fontWeight: FontWeight.w600)),
                                      Text("Let us know your issue & feedback".tr, style: TextStyle()),
                                      const SizedBox(
                                        height: 20,
                                      ),
                                      TabBar(
                                        indicatorColor: AppColors.darkModePrimary,
                                        tabs: [
                                          Tab(
                                              child: Text(
                                            "Call Us".tr,
                                            style: TextStyle(),
                                          )),
                                          Tab(
                                              child: Text(
                                            "Email Us".tr,
                                            style: TextStyle(),
                                          )),
                                        ],
                                      ),
                                      Expanded(
                                        child: TabBarView(
                                          children: [
                                            Padding(
                                              padding: const EdgeInsets.only(top: 20),
                                              child: Column(
                                                children: [
                                                  InkWell(
                                                    onTap: () {
                                                      Constant.makePhoneCall(controller.phone.value);
                                                    },
                                                    child: Row(
                                                      children: [
                                                        const Icon(Icons.call),
                                                        const SizedBox(
                                                          width: 20,
                                                        ),
                                                        Text(controller.phone.value)
                                                      ],
                                                    ),
                                                  ),
                                                  const SizedBox(
                                                    height: 10,
                                                  ),
                                                  const Divider(),
                                                  const SizedBox(
                                                    height: 10,
                                                  ),
                                                  Row(
                                                    children: [
                                                      const Icon(Icons.location_on),
                                                      const SizedBox(
                                                        width: 20,
                                                      ),
                                                      Expanded(child: Text(controller.address.value))
                                                    ],
                                                  )
                                                ],
                                              ),
                                            ),
                                            Padding(
                                              padding: const EdgeInsets.symmetric(vertical: 10),
                                              child: SingleChildScrollView(
                                                child: Column(
                                                  crossAxisAlignment: CrossAxisAlignment.start,
                                                  children: [
                                                    Text("Write us".tr, style: TextStyle(fontSize: 20, fontWeight: FontWeight.w600)),
                                                    Text("Describe your issue".tr, style: TextStyle()),
                                                    const SizedBox(
                                                      height: 20,
                                                    ),
                                                    TextFieldThem.buildTextFiled(context, hintText: 'Email'.tr, controller: controller.emailController.value),
                                                    const SizedBox(
                                                      height: 20,
                                                    ),
                                                    TextFieldThem.buildTextFiled(context,
                                                        hintText: 'Describe your issue and feedback'.tr, controller: controller.feedbackController.value, maxLine: 5),
                                                    const SizedBox(
                                                      height: 20,
                                                    ),
                                                    ButtonThem.buildButton(
                                                      context,
                                                      title: "Submit".tr,
                                                      onPress: () async {
                                                        if (controller.emailController.value.text.isEmpty) {
                                                          ShowToastDialog.showToast("Please enter email".tr);
                                                        } else if (controller.feedbackController.value.text.isEmpty) {
                                                          ShowToastDialog.showToast("Please enter feedback".tr);
                                                        } else {
                                                          final Email email = Email(
                                                            body: controller.feedbackController.value.text,
                                                            subject: controller.subject.value,
                                                            recipients: [controller.email.value],
                                                            cc: [controller.emailController.value.text],
                                                            isHTML: false,
                                                          );
                                                          await FlutterEmailSender.send(email);
                                                          controller.emailController.value.clear();
                                                          controller.feedbackController.value.clear();
                                                        }
                                                      },
                                                    ),
                                                  ],
                                                ),
                                              ),
                                            )
                                          ],
                                        ),
                                      )
                                    ],
                                  ),
                                ),
                              ),
                            ),
                    ),
                  ),
                ),
              ],
            ),
          );
        });
  }
}
