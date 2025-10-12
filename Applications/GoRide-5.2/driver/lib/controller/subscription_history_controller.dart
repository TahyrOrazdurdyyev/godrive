import 'dart:developer';
import 'package:driver/model/subscription_history.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/driver_api.dart';
import 'package:driver/utils/subscription_api.dart';
import 'package:get/get.dart';

class SubscriptionHistoryController extends GetxController {
  RxBool isLoading = true.obs;
  RxList<SubscriptionHistoryModel> subscriptionHistoryList = <SubscriptionHistoryModel>[].obs;

  @override
  void onInit() {
    getAllSubscriptionList();
    super.onInit();
  }

  getAllSubscriptionList() async {
    try {
      final uid = FireStoreUtils.getCurrentUid();
      final response = await SubscriptionApi.getHistory(uid);
      
      if (response['success'] == true && response['history'] != null) {
        subscriptionHistoryList.value = (response['history'] as List)
            .map((json) => SubscriptionHistoryModel.fromJson(json))
            .toList();
      }
    } catch (e) {
      log('❌ Error loading subscription history: $e');
    }
    isLoading.value = false;
  }
}
