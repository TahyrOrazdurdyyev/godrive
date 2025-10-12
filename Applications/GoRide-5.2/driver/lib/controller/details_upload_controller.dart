import 'dart:io';

import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:driver/constant/constant.dart';
import 'package:driver/constant/show_toast_dialog.dart';
import 'package:driver/model/document_model.dart';
import 'package:driver/model/driver_document_model.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/driver_api.dart';
import 'package:driver/utils/driver_document_api.dart';
import 'package:flutter/cupertino.dart';
import 'dart:convert';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';

class DetailsUploadController extends GetxController {
  Rx<DocumentModel> documentModel = DocumentModel().obs;

  Rx<TextEditingController> documentNumberController = TextEditingController().obs;
  Rx<TextEditingController> expireAtController = TextEditingController().obs;
  Rx<DateTime?> selectedDate = DateTime.now().obs;

  RxString frontImage = "".obs;
  RxString backImage = "".obs;

  RxBool isLoading = true.obs;

  @override
  void onInit() {
    // TODO: implement onInit
    getArgument();
    super.onInit();
  }

  getArgument() async {
    dynamic argumentData = Get.arguments;
    if (argumentData != null) {
      documentModel.value = argumentData['documentModel'];
    }
    getDocument();
    update();
  }

  Rx<Documents> documents = Documents().obs;

  getDocument() async {
    try {
      // Get driver ID from API
      final uid = FireStoreUtils.getCurrentUid();
      final driverResponse = await DriverApi.getProfile(uid);
      
      if (driverResponse['success'] == true && driverResponse['driver'] != null) {
        final driverId = driverResponse['driver']['id'];
        
        // Get documents from API
        final docsResponse = await DriverDocumentApi.getDocuments(driverId);
        
        if (docsResponse['success'] == true && docsResponse['documents'] != null) {
          // Find document matching current documentModel
          final docs = docsResponse['documents'] as List;
          final matchingDoc = docs.where((doc) => doc['document_type'] == documentModel.value.id).toList();
          
          if (matchingDoc.isNotEmpty) {
            final doc = matchingDoc.first;
            documentNumberController.value.text = doc['document_name'] ?? '';
            frontImage.value = doc['document_url'] ?? '';
            // For simplicity, using same image for back (can be extended later)
            backImage.value = doc['document_url'] ?? '';
          }
        }
      }
    } catch (e) {
      print('❌ Error loading documents: $e');
    }
    isLoading.value = false;
  }

  final ImagePicker _imagePicker = ImagePicker();

  Future pickFile({required ImageSource source, required String type}) async {
    try {
      XFile? image = await _imagePicker.pickImage(source: source);
      if (image == null) return;
      Get.back();

      if (type == "front") {
        frontImage.value = image.path;
      } else {
        backImage.value = image.path;
      }
    } on PlatformException catch (e) {
      ShowToastDialog.showToast("Failed to Pick : \n $e");
    }
  }


  uploadDocument() async {
    String frontImageFileName = File(frontImage.value).path.split('/').last;
    String backImageFileName = File(backImage.value).path.split('/').last;

    if(frontImage.value.isNotEmpty && Constant().hasValidUrl(frontImage.value) == false){
      frontImage.value = await Constant.uploadUserImageToFireStorage(File(frontImage.value), "driverDocument/${FireStoreUtils.getCurrentUid()}", frontImageFileName);
    }

    if(backImage.value.isNotEmpty && Constant().hasValidUrl(backImage.value) == false){
      backImage.value = await Constant.uploadUserImageToFireStorage(File(backImage.value), "driverDocument/${FireStoreUtils.getCurrentUid()}", backImageFileName);
    }
    documents.value.frontImage = frontImage.value;
    documents.value.documentId = documentModel.value.id;
    documents.value.documentNumber = documentNumberController.value.text;
    documents.value.backImage = backImage.value;
    try {
      // Get driver ID
      final uid = FireStoreUtils.getCurrentUid();
      final driverResponse = await DriverApi.getProfile(uid);
      
      if (driverResponse['success'] == true && driverResponse['driver'] != null) {
        final driverId = driverResponse['driver']['id'];
        
        // Convert front image to base64
        if (frontImage.value.isNotEmpty && !frontImage.value.startsWith('http')) {
          final bytes = await File(frontImage.value).readAsBytes();
          final base64Image = 'data:image/jpeg;base64,${base64Encode(bytes)}';
          
          // Upload front image
          await DriverDocumentApi.uploadDocument(
            driverId: driverId,
            documentType: documentModel.value.id!,
            documentBase64: base64Image,
            documentName: '${documentModel.value.title}_front_${documentNumberController.value.text}',
          );
        }
        
        // Convert and upload back image if different from front
        if (backImage.value.isNotEmpty && backImage.value != frontImage.value && !backImage.value.startsWith('http')) {
          final bytes = await File(backImage.value).readAsBytes();
          final base64Image = 'data:image/jpeg;base64,${base64Encode(bytes)}';
          
          await DriverDocumentApi.uploadDocument(
            driverId: driverId,
            documentType: '${documentModel.value.id}_back',
            documentBase64: base64Image,
            documentName: '${documentModel.value.title}_back_${documentNumberController.value.text}',
          );
        }
        
        ShowToastDialog.closeLoader();
        ShowToastDialog.showToast("Document upload successfully");
        Get.back();
      } else {
        ShowToastDialog.closeLoader();
        ShowToastDialog.showToast("Failed to upload document");
      }
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast("Error: ${e.toString()}");
      print('❌ Error uploading document: $e');
    }
  }
}

