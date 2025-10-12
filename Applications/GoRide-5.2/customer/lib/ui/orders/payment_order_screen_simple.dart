import 'package:customer/constant/constant.dart';
import 'package:customer/controller/payment_order_controller.dart';
import 'package:customer/themes/app_colors.dart';
import 'package:customer/themes/button_them.dart';
import 'package:customer/themes/responsive.dart';
import 'package:customer/utils/dark_theme_provider.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:provider/provider.dart';

class PaymentOrderScreenSimple extends StatelessWidget {
  const PaymentOrderScreenSimple({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);
    
    return GetX<PaymentOrderController>(
      init: PaymentOrderController(),
      builder: (controller) {
        return Scaffold(
          backgroundColor: themeChange.getThem() ? AppColors.darkBackground : AppColors.lightBackground,
          appBar: AppBar(
            backgroundColor: themeChange.getThem() ? AppColors.darkBackground : AppColors.lightBackground,
            elevation: 0,
            leading: InkWell(
              onTap: () {
                Get.back();
              },
              child: Icon(
                Icons.arrow_back,
                color: themeChange.getThem() ? AppColors.darkText : AppColors.lightText,
              ),
            ),
            title: Text(
              "Payment".tr,
              style: TextStyle(
                color: themeChange.getThem() ? AppColors.darkText : AppColors.lightText,
                fontSize: 18,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
          body: controller.isLoading.value
              ? const Center(child: CircularProgressIndicator())
              : Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Order Summary Card
                      Container(
                        width: Responsive.width(100, context),
                        decoration: BoxDecoration(
                          color: themeChange.getThem() ? AppColors.darkGrey : AppColors.lightGrey,
                          borderRadius: BorderRadius.circular(12),
                        ),
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              "Order Summary".tr,
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.w600,
                                color: themeChange.getThem() ? AppColors.darkText : AppColors.lightText,
                              ),
                            ),
                            const SizedBox(height: 16),
                            
                            // Trip details
                            Row(
                              children: [
                                Icon(Icons.location_on, color: AppColors.primary, size: 20),
                                const SizedBox(width: 8),
                                Expanded(
                                  child: Text(
                                    controller.orderModel.value.departName ?? "Pickup Location",
                                    style: TextStyle(
                                      color: themeChange.getThem() ? AppColors.darkText : AppColors.lightText,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 8),
                            Row(
                              children: [
                                Icon(Icons.flag, color: Colors.red, size: 20),
                                const SizedBox(width: 8),
                                Expanded(
                                  child: Text(
                                    controller.orderModel.value.destinationName ?? "Destination",
                                    style: TextStyle(
                                      color: themeChange.getThem() ? AppColors.darkText : AppColors.lightText,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            
                            const Divider(height: 24),
                            
                            // Price breakdown
                            _buildPriceRow("Subtotal".tr, controller.orderModel.value.subTotal ?? "0", themeChange),
                            if (controller.orderModel.value.taxAmount != null && controller.orderModel.value.taxAmount != "0")
                              _buildPriceRow("Tax".tr, controller.orderModel.value.taxAmount!, themeChange),
                            if (controller.orderModel.value.tipAmount != null && controller.orderModel.value.tipAmount != "0")
                              _buildPriceRow("Tip".tr, controller.orderModel.value.tipAmount!, themeChange),
                            if (controller.orderModel.value.discountAmount != null && controller.orderModel.value.discountAmount != "0")
                              _buildPriceRow("Discount".tr, "-${controller.orderModel.value.discountAmount!}", themeChange, isDiscount: true),
                            
                            const Divider(),
                            
                            // Total
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text(
                                  "Total".tr,
                                  style: TextStyle(
                                    fontSize: 18,
                                    fontWeight: FontWeight.w600,
                                    color: themeChange.getThem() ? AppColors.darkText : AppColors.lightText,
                                  ),
                                ),
                                Text(
                                  "${Constant.currencyModel?.symbol ?? "\$"}${controller.calculateAmount().toStringAsFixed(Constant.currencyModel?.decimalDigits ?? 2)}",
                                  style: TextStyle(
                                    fontSize: 18,
                                    fontWeight: FontWeight.w600,
                                    color: AppColors.primary,
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                      
                      const SizedBox(height: 24),
                      
                      // Payment Method Card
                      Container(
                        width: Responsive.width(100, context),
                        decoration: BoxDecoration(
                          color: themeChange.getThem() ? AppColors.darkGrey : AppColors.lightGrey,
                          borderRadius: BorderRadius.circular(12),
                        ),
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              "Payment Method".tr,
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.w600,
                                color: themeChange.getThem() ? AppColors.darkText : AppColors.lightText,
                              ),
                            ),
                            const SizedBox(height: 16),
                            
                            // Cash payment option
                            Container(
                              decoration: BoxDecoration(
                                border: Border.all(color: AppColors.primary, width: 2),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: ListTile(
                                leading: Icon(Icons.money, color: AppColors.primary),
                                title: Text(
                                  "Cash Payment".tr,
                                  style: TextStyle(
                                    fontWeight: FontWeight.w600,
                                    color: themeChange.getThem() ? AppColors.darkText : AppColors.lightText,
                                  ),
                                ),
                                subtitle: Text(
                                  "Pay cash to driver".tr,
                                  style: TextStyle(
                                    color: themeChange.getThem() ? AppColors.darkText.withOpacity(0.7) : AppColors.lightText.withOpacity(0.7),
                                  ),
                                ),
                                trailing: Icon(Icons.check_circle, color: AppColors.primary),
                              ),
                            ),
                          ],
                        ),
                      ),
                      
                      const Spacer(),
                      
                      // Complete Order Button
                      ButtonThem.buildButton(
                        context,
                        title: "Complete Order".tr,
                        onPress: () {
                          controller.completeOrder();
                        },
                      ),
                    ],
                  ),
                ),
        );
      },
    );
  }

  Widget _buildPriceRow(String label, String amount, DarkThemeProvider themeChange, {bool isDiscount = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: TextStyle(
              color: themeChange.getThem() ? AppColors.darkText : AppColors.lightText,
            ),
          ),
          Text(
            "${Constant.currencyModel?.symbol ?? "\$"}$amount",
            style: TextStyle(
              color: isDiscount ? Colors.green : (themeChange.getThem() ? AppColors.darkText : AppColors.lightText),
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }
}
