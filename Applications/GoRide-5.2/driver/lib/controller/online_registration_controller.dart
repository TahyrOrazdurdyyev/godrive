import 'package:driver/model/document_model.dart';
import 'package:driver/model/driver_document_model.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/driver_api.dart';
import 'package:driver/utils/driver_document_api.dart';
import 'package:get/get.dart';

class OnlineRegistrationController extends GetxController {
  RxBool isLoading = true.obs;

  @override
  void onInit() {
    // TODO: implement onInit
    getDocument();
    super.onInit();
  }

  RxList documentList = <DocumentModel>[].obs;
  RxList driverDocumentList = <Documents>[].obs;

  getDocument() async {
    // Get required document list from Firestore (configuration)
    await FireStoreUtils.getDocumentList().then((value) {
      documentList.value = value;
      isLoading.value = false;
    });

    // Get uploaded documents from API
    try {
      final uid = FireStoreUtils.getCurrentUid();
      final driverResponse = await DriverApi.getProfile(uid);
      
      if (driverResponse['success'] == true && driverResponse['driver'] != null) {
        final driverId = driverResponse['driver']['id'];
        final docsResponse = await DriverDocumentApi.getDocuments(driverId);
        
        if (docsResponse['success'] == true && docsResponse['documents'] != null) {
          // Convert API docs to Documents model for UI compatibility
          driverDocumentList.clear();
          for (var doc in docsResponse['documents']) {
            driverDocumentList.add(Documents(
              documentId: doc['document_type'],
              frontImage: doc['document_url'],
              backImage: doc['document_url'],
              documentNumber: doc['document_name'],
              verified: doc['status'] == 'approved',
            ));
          }
        }
      }
    } catch (e) {
      print('❌ Error loading driver documents: $e');
    }
    
    update();
  }
}
