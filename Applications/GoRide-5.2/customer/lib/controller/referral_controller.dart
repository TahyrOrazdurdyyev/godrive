import 'package:customer/model/referral_model.dart';
import 'package:customer/utils/fire_store_utils.dart';
import 'package:get/get.dart';

class ReferralController extends GetxController {
  @override
  void onInit() {
    // TODO: implement onInit
    getReferralCode();
    super.onInit();
  }

  Rx<ReferralModel> referralModel = ReferralModel().obs;
  RxBool isLoading = true.obs;

  getReferralCode() async {
    // DEMO: Load static referral data
    await Future.delayed(Duration(milliseconds: 500)); // Simulate loading
    
    referralModel.value = ReferralModel(
      id: "demo_referral_123",
      referralCode: "GORIDE2024",
      referralBy: "demo_user_123",
    );
    
    isLoading.value = false;
  }
}
