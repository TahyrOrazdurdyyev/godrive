import 'package:customer/constant/constant.dart';
import 'package:customer/controller/wallet_controller.dart';
import 'package:customer/themes/app_colors.dart';
import 'package:customer/themes/responsive.dart';
import 'package:customer/utils/dark_theme_provider.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:provider/provider.dart';

class WalletScreenSimple extends StatelessWidget {
  const WalletScreenSimple({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);
    
    return GetX<WalletController>(
      init: WalletController(),
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
              "My Wallet".tr,
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
                    children: [
                      // Wallet Balance Card
                      Container(
                        width: Responsive.width(100, context),
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            colors: [AppColors.primary, AppColors.primary.withOpacity(0.8)],
                            begin: Alignment.topLeft,
                            end: Alignment.bottomRight,
                          ),
                          borderRadius: BorderRadius.circular(16),
                        ),
                        padding: const EdgeInsets.all(24),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              "Wallet Balance".tr,
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 16,
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                            const SizedBox(height: 8),
                            Text(
                              "${Constant.currencyModel?.symbol ?? "\$"}${controller.userModel.value.walletAmount ?? "0.00"}",
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 32,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 16),
                            Row(
                              children: [
                                Icon(Icons.info_outline, color: Colors.white.withOpacity(0.8), size: 16),
                                const SizedBox(width: 8),
                                Expanded(
                                  child: Text(
                                    "Cash payments only. Wallet top-up not available.".tr,
                                    style: TextStyle(
                                      color: Colors.white.withOpacity(0.8),
                                      fontSize: 12,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                      
                      const SizedBox(height: 24),
                      
                      // Transaction History Header
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(
                            "Transaction History".tr,
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.w600,
                              color: themeChange.getThem() ? AppColors.darkText : AppColors.lightText,
                            ),
                          ),
                          Icon(
                            Icons.history,
                            color: themeChange.getThem() ? AppColors.darkText : AppColors.lightText,
                          ),
                        ],
                      ),
                      
                      const SizedBox(height: 16),
                      
                      // Transaction List
                      Expanded(
                        child: controller.transactionList.isEmpty
                            ? Center(
                                child: Column(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Icon(
                                      Icons.receipt_long_outlined,
                                      size: 64,
                                      color: themeChange.getThem() 
                                          ? AppColors.darkText.withOpacity(0.3)
                                          : AppColors.lightText.withOpacity(0.3),
                                    ),
                                    const SizedBox(height: 16),
                                    Text(
                                      "No transactions yet".tr,
                                      style: TextStyle(
                                        fontSize: 16,
                                        color: themeChange.getThem() 
                                            ? AppColors.darkText.withOpacity(0.6)
                                            : AppColors.lightText.withOpacity(0.6),
                                      ),
                                    ),
                                    const SizedBox(height: 8),
                                    Text(
                                      "Your transaction history will appear here".tr,
                                      style: TextStyle(
                                        fontSize: 14,
                                        color: themeChange.getThem() 
                                            ? AppColors.darkText.withOpacity(0.4)
                                            : AppColors.lightText.withOpacity(0.4),
                                      ),
                                      textAlign: TextAlign.center,
                                    ),
                                  ],
                                ),
                              )
                            : ListView.builder(
                                itemCount: controller.transactionList.length,
                                itemBuilder: (context, index) {
                                  final transaction = controller.transactionList[index];
                                  return Container(
                                    margin: const EdgeInsets.only(bottom: 12),
                                    decoration: BoxDecoration(
                                      color: themeChange.getThem() ? AppColors.darkGrey : AppColors.lightGrey,
                                      borderRadius: BorderRadius.circular(12),
                                    ),
                                    child: ListTile(
                                      leading: Container(
                                        padding: const EdgeInsets.all(8),
                                        decoration: BoxDecoration(
                                          color: transaction.isCredit == true ? Colors.green.withOpacity(0.1) : Colors.red.withOpacity(0.1),
                                          borderRadius: BorderRadius.circular(8),
                                        ),
                                        child: Icon(
                                          transaction.isCredit == true ? Icons.add : Icons.remove,
                                          color: transaction.isCredit == true ? Colors.green : Colors.red,
                                        ),
                                      ),
                                      title: Text(
                                        transaction.note ?? "Transaction",
                                        style: TextStyle(
                                          fontWeight: FontWeight.w600,
                                          color: themeChange.getThem() ? AppColors.darkText : AppColors.lightText,
                                        ),
                                      ),
                                      subtitle: Text(
                                        transaction.createdDate ?? "",
                                        style: TextStyle(
                                          color: themeChange.getThem() 
                                              ? AppColors.darkText.withOpacity(0.6)
                                              : AppColors.lightText.withOpacity(0.6),
                                        ),
                                      ),
                                      trailing: Text(
                                        "${transaction.isCredit == true ? "+" : "-"}${Constant.currencyModel?.symbol ?? "\$"}${transaction.amount ?? "0"}",
                                        style: TextStyle(
                                          fontWeight: FontWeight.w600,
                                          color: transaction.isCredit == true ? Colors.green : Colors.red,
                                        ),
                                      ),
                                    ),
                                  );
                                },
                              ),
                      ),
                    ],
                  ),
                ),
        );
      },
    );
  }
}
