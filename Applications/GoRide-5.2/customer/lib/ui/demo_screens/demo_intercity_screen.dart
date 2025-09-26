import 'package:customer/themes/app_colors.dart';
import 'package:customer/themes/responsive.dart';
import 'package:customer/utils/DarkThemeProvider.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:provider/provider.dart';

class DemoIntercityScreen extends StatelessWidget {
  const DemoIntercityScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);
    return Scaffold(
      backgroundColor: AppColors.primary,
      body: Column(
        children: [
          Container(
            height: Responsive.width(10, context),
            width: Responsive.width(100, context),
            color: AppColors.primary,
          ),
          Expanded(
            child: Container(
              decoration: BoxDecoration(
                color: Theme.of(context).colorScheme.background, 
                borderRadius: const BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25))
              ),
              child: Padding(
                padding: const EdgeInsets.all(20),
                child: Column(
                  children: [
                    Text(
                      "OutStation Rides".tr,
                      style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                    ),
                    SizedBox(height: 30),
                    Expanded(
                      child: ListView(
                        children: [
                          _buildIntercityCard(
                            "OutStation Trip #001",
                            "City Center → Beach Resort",
                            "\$125.00",
                            "Dec 20, 2024",
                            Colors.blue,
                            themeChange
                          ),
                          SizedBox(height: 15),
                          _buildIntercityCard(
                            "OutStation Trip #002", 
                            "Airport → Mountain Lodge",
                            "\$95.50",
                            "Dec 18, 2024",
                            Colors.green,
                            themeChange
                          ),
                          SizedBox(height: 15),
                          _buildIntercityCard(
                            "OutStation Trip #003",
                            "Downtown → Countryside Villa", 
                            "\$150.75",
                            "Dec 16, 2024",
                            Colors.green,
                            themeChange
                          ),
                        ],
                      ),
                    )
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildIntercityCard(String title, String route, String price, String date, Color statusColor, DarkThemeProvider themeChange) {
    return Container(
      padding: EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: themeChange.getThem() ? AppColors.darkContainerBackground : AppColors.containerBackground,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: themeChange.getThem() ? AppColors.darkContainerBorder : AppColors.containerBorder, width: 0.5),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(title, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
              Container(
                padding: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: statusColor.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(statusColor == Colors.blue ? "Scheduled" : "Completed", 
                     style: TextStyle(color: statusColor, fontSize: 12)),
              ),
            ],
          ),
          SizedBox(height: 8),
          Row(
            children: [
              Icon(Icons.alt_route, size: 16, color: Colors.grey[600]),
              SizedBox(width: 8),
              Expanded(child: Text(route, style: TextStyle(color: Colors.grey[600]))),
            ],
          ),
          SizedBox(height: 8),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(date, style: TextStyle(color: Colors.grey[600], fontSize: 12)),
              Text(price, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: AppColors.primary)),
            ],
          ),
        ],
      ),
    );
  }
}
