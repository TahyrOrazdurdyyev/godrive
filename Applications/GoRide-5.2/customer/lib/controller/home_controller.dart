import 'dart:convert';

import 'package:customer/constant/constant.dart';
import 'package:customer/constant/show_toast_dialog.dart';
import 'package:customer/controller/dash_board_controller.dart';
import 'package:customer/model/airport_model.dart';
import 'package:customer/model/banner_model.dart';
import 'package:customer/model/contact_model.dart';
import 'package:customer/model/language_name.dart';
import 'package:customer/model/language_title.dart';
import 'package:customer/model/order/location_lat_lng.dart';
import 'package:customer/model/payment_model.dart';
import 'package:customer/model/service_model.dart';
import 'package:customer/model/user_model.dart';
import 'package:customer/model/zone_model.dart';
import 'package:customer/themes/app_colors.dart';
import 'package:customer/utils/Preferences.dart';
import 'package:customer/utils/notification_service.dart';
import 'package:customer/utils/utils.dart';
import 'package:customer/utils/service_api.dart';
import 'package:http/http.dart' as http;
import 'package:flutter/material.dart';
import 'package:geocoding/geocoding.dart';
import 'package:get/get.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:osm_nominatim/osm_nominatim.dart';

class HomeController extends GetxController {
  DashBoardController dashboardController = Get.put(DashBoardController());

  Rx<TextEditingController> sourceLocationController = TextEditingController().obs;
  Rx<TextEditingController> destinationLocationController = TextEditingController().obs;
  Rx<TextEditingController> offerYourRateController = TextEditingController().obs;
  Rx<ServiceModel> selectedType = ServiceModel().obs;

  Rx<LocationLatLng> sourceLocationLAtLng = LocationLatLng().obs;
  Rx<LocationLatLng> destinationLocationLAtLng = LocationLatLng().obs;

  RxString currentLocation = "".obs;
  RxBool isLoading = true.obs;
  RxList<ServiceModel> serviceList = <ServiceModel>[].obs;
  RxList bannerList = <BannerModel>[].obs;
  RxList<ZoneModel> zoneList = <ZoneModel>[].obs;
  Rx<ZoneModel> selectedZone = ZoneModel().obs;
  Rx<UserModel> userModel = UserModel().obs;
  RxBool isAcSelected = false.obs;
  RxDouble extraDistance = 0.0.obs;
  final PageController pageController = PageController(viewportFraction: 0.96, keepPage: true);

  var colors = [
    AppColors.serviceColor1,
    AppColors.serviceColor2,
    AppColors.serviceColor3,
  ];

  String? startNightTime;
  String? endNightTime;
  DateTime startNightTimeString = DateTime.now();
  DateTime endNightTimeString = DateTime.now();

  @override
  void onInit() {
    // TODO: implement onInit
    getLocation();
    getServiceType();
    getPaymentData();
    getContact();
    super.onInit();
  }

  Future<void> getLocation() async {
    try {
      Constant.currentLocation = await Utils.getCurrentLocation();
      if (Constant.currentLocation == null) return;

          if (Constant.selectedMapType == 'google') {
            List<Placemark> placeMarks = await placemarkFromCoordinates(
              Constant.currentLocation!.latitude,
              Constant.currentLocation!.longitude,
            );
            Constant.country = placeMarks.first.country;
            Constant.city = placeMarks.first.locality;
            currentLocation.value =
                "${placeMarks.first.name}, ${placeMarks.first.subLocality}, ${placeMarks.first.locality}, ${placeMarks.first.administrativeArea}, ${placeMarks.first.postalCode}, ${placeMarks.first.country}";
          } else {
            // Use Yandex Geocoding API for better Turkmenistan coverage
            await _getAddressFromYandexGeocoding(
              Constant.currentLocation!.latitude,
              Constant.currentLocation!.longitude,
            );
          }
    } catch (e) {
      ShowToastDialog.showToast(
        "Location access permission is currently unavailable. You're unable to retrieve any location data. Please grant permission from your device settings.",
        duration: const Duration(seconds: 3),
      );
    }
  }

  // Yandex Geocoding API method
  Future<void> _getAddressFromYandexGeocoding(double lat, double lon) async {
    try {
      print('🗺️ Using Yandex Geocoding API for: $lat, $lon');
      
      final url = 'https://geocode-maps.yandex.ru/1.x/?apikey=${Constant.yandexAPIKey}&geocode=$lon,$lat&format=json&lang=ru_RU&results=1';
      
      final response = await http.get(Uri.parse(url));
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        print('🗺️ Yandex Geocoding response: ${response.body}');
        
        final geoObjects = data['response']['GeoObjectCollection']['featureMember'];
        
        if (geoObjects != null && geoObjects.isNotEmpty) {
          final geoObject = geoObjects[0]['GeoObject'];
          final metaData = geoObject['metaDataProperty']['GeocoderMetaData'];
          
          // Get formatted address
          String formattedAddress = geoObject['name'] ?? '';
          String description = geoObject['description'] ?? '';
          
          if (description.isNotEmpty) {
            formattedAddress = '$formattedAddress, $description';
          }
          
          // Extract country and city from address components
          final components = metaData['Address']['Components'] as List?;
          if (components != null) {
            for (var component in components) {
              final kind = component['kind'];
              final name = component['name'];
              
              if (kind == 'country') {
                Constant.country = name ?? '';
              } else if (kind == 'locality' || kind == 'province') {
                Constant.city = name ?? '';
              }
            }
          }
          
          currentLocation.value = formattedAddress.isNotEmpty ? formattedAddress : 'Местоположение найдено';
          print('🗺️ Yandex Geocoding result: $formattedAddress');
          print('🗺️ Country: ${Constant.country}, City: ${Constant.city}');
        } else {
          currentLocation.value = 'Адрес не найден';
          print('🗺️ Yandex Geocoding: No results found');
        }
      } else {
        print('🗺️ Yandex Geocoding API error: ${response.statusCode}');
        currentLocation.value = 'Ошибка получения адреса';
      }
    } catch (e) {
      print('🗺️ Yandex Geocoding exception: $e');
      currentLocation.value = 'Ошибка геокодинга';
    }
  }

  getServiceType() async {
    try {
      print('🔥 HomeController: Starting to load services...');
      
      // Load services from Laravel API
      final servicesResponse = await ServiceApi.getServices();
      if (servicesResponse['success'] == true && servicesResponse['data'] != null) {
        serviceList.value = (servicesResponse['data'] as List)
            .map((json) => ServiceModel.fromJson(json))
            .toList();
        
        if (serviceList.isNotEmpty) {
          selectedType.value = serviceList.first;
          print('🔥 HomeController: Selected first service: ${selectedType.value.id}');
          print('🔥 HomeController: First service image: ${selectedType.value.image}');
        }
        print('🔥 HomeController: Loaded ${serviceList.length} services from API');
        for (var service in serviceList) {
          print('🔥 Service ${service.id}: image=${service.image}');
        }
      }

      // Load banners from Laravel API
      print('🔥 Loading banners from API...');
      final bannersResponse = await ServiceApi.getBanners();
      if (bannersResponse['success'] == true && bannersResponse['data'] != null) {
        bannerList.clear();
        bannerList.addAll((bannersResponse['data'] as List)
            .map((json) => BannerModel.fromJson(json))
            .toList());
        print('🔥 Banner list: ${bannerList.length} banners loaded from API');
        for (var banner in bannerList) {
          print('🔥 Banner ${banner.id}: image=${banner.image}');
        }
      }

      // Load user profile from preferences
      await loadUserData();

      isLoading.value = false;
      print('🔥 HomeController: Loading complete!');
    } catch (e) {
      print('❌ Error loading services: $e');
      print('❌ Stack trace: ${StackTrace.current}');
      // Fallback to demo data if API fails
      await loadDemoData();
      isLoading.value = false;
    }
  }

  // Fallback demo data method
  Future<void> loadDemoData() async {
    serviceList.value = [
      ServiceModel(
        id: "service_1",
        title: [LanguageTitle(title: "Economy", type: "en")],
        kmCharge: "1.5",
        image: "assets/icons/ic_car_economy.png",
        enable: true,
      ),
      ServiceModel(
        id: "service_2", 
        title: [LanguageTitle(title: "Premium", type: "en")],
        kmCharge: "2.0",
        image: "assets/icons/ic_car_premium.png",
        enable: true,
      ),
      ServiceModel(
        id: "service_3",
        title: [LanguageTitle(title: "Luxury", type: "en")], 
        kmCharge: "3.0",
        image: "assets/icons/ic_car_luxury.png",
        enable: true,
      ),
    ];
    
    if (serviceList.isNotEmpty) {
      selectedType.value = serviceList.first;
    }

    bannerList.clear();
    bannerList.addAll(<BannerModel>[
      BannerModel(
        id: "banner_1",
        image: "assets/images/banner_1.png",
        enable: true,
        position: "1",
      ),
      BannerModel(
        id: "banner_2",
        image: "assets/images/banner_2.png", 
        enable: true,
        position: "2",
      ),
    ]);

    // Load user data from preferences
    await loadUserData();
  }

  RxString duration = "".obs;
  RxString distance = "".obs;
  RxString amount = "".obs;

  Future<void> loadUserData() async {
    try {
      String? userJson = Preferences.getString(Preferences.user);
      if (userJson != null && userJson.isNotEmpty) {
        Map<String, dynamic> userData = json.decode(userJson);
        userModel.value = UserModel.fromJson(userData);
      } else {
        // If no user data in preferences, create empty user model
        userModel.value = UserModel();
      }
    } catch (e) {
      debugPrint("Error loading user data: $e");
      userModel.value = UserModel();
    }
  }
  RxString acCharge = "".obs;
  RxString nonAcCharge = "".obs;
  RxString basicFare = "".obs;
  RxString basicFareCharge = "".obs;
  RxString nightCharge = "".obs;
  RxDouble totalAmount = 0.0.obs;
  RxDouble totalNightFare = 0.0.obs;
  RxBool isAcNonAc = false.obs;
  DateTime currentTime = DateTime.now();
  DateTime currentDate = DateTime.now();

  double convertToMinutes(String duration) {
    double durationValue = 0.0;

    try {
      final RegExp hoursRegex = RegExp(r"(\d+)\s*hour");
      final RegExp minutesRegex = RegExp(r"(\d+)\s*min");

      final Match? hoursMatch = hoursRegex.firstMatch(duration);
      if (hoursMatch != null) {
        int hours = int.parse(hoursMatch.group(1)!.trim());
        durationValue += hours * 60;
      }

      final Match? minutesMatch = minutesRegex.firstMatch(duration);
      if (minutesMatch != null) {
        int minutes = int.parse(minutesMatch.group(1)!.trim());
        durationValue += minutes;
      }
    } catch (e) {
      print("Exception: $e");
      throw FormatException("Invalid duration format: $duration");
    }

    return durationValue;
  }

  calculateDurationAndDistance() async {
    if (Constant.selectedMapType == 'osm') {
      if (sourceLocationLAtLng.value.latitude != null && destinationLocationLAtLng.value.latitude != null) {
        ShowToastDialog.showLoader("Please wait");
        await Constant.getDurationOsmDistance(LatLng(sourceLocationLAtLng.value.latitude!, sourceLocationLAtLng.value.longitude!),
                LatLng(destinationLocationLAtLng.value.latitude!, destinationLocationLAtLng.value.longitude!))
            .then((value) {
          if (value != {} && value.isNotEmpty) {
            int hours = value['routes'].first['duration'] ~/ 3600;
            int minutes = ((value['routes'].first['duration'] % 3600) / 60).round();
            duration.value = '$hours hours $minutes minutes'.trim();
            if (Constant.distanceType == "Km") {
              distance.value = (value['routes'].first['distance'] / 1000).toString();
            } else {
              distance.value = (value['routes'].first['distance'] / 1609.34).toString();
            }
          }
          update();
        });
      }
      ShowToastDialog.closeLoader();
    } else {
      if (sourceLocationLAtLng.value.latitude != null && destinationLocationLAtLng.value.latitude != null) {
        ShowToastDialog.showLoader("Please wait");
        await Constant.getDurationDistance(LatLng(sourceLocationLAtLng.value.latitude!, sourceLocationLAtLng.value.longitude!),
                LatLng(destinationLocationLAtLng.value.latitude!, destinationLocationLAtLng.value.longitude!))
            .then((value) {
          if (value != null) {
            duration.value = value.rows!.first.elements!.first.duration!.text.toString();
            print("duration :: 00 :: ${duration.value}");
            if (Constant.distanceType == "Km") {
              distance.value = (value.rows!.first.elements!.first.distance!.value!.toInt() / 1000).toString();
            } else {
              distance.value = (value.rows!.first.elements!.first.distance!.value!.toInt() / 1609.34).toString();
            }
          }
          update();
        });
        ShowToastDialog.closeLoader();
      }
    }
  }

  calculateAmount() async {
    // 🎯 AUCTION SYSTEM - NO AUTOMATIC PRICE CALCULATION
    // Client enters their own price in "Offer Your Rate" field
    
    // Skip all calculations for auction system
    // Just set amount to "0.00" as placeholder
    amount.value = "0.00";
    
    print('🎯 Auction mode: Skipping price calculation');
    print('🎯 Distance: ${distance.value} km');
    print('🎯 Duration: ${duration.value}');
    
    // Old calculation code removed for auction system
    // Client will enter their offer price manually
    return;
    
    /*
    // OLD CODE - REMOVED FOR AUCTION SYSTEM
    acCharge.value = selectedType.value.acCharge.toString();
    nonAcCharge.value = selectedType.value.nonAcCharge.toString();
    basicFare.value = selectedType.value.basicFare.toString();
    basicFareCharge.value = selectedType.value.basicFareCharge.toString();
    isAcNonAc.value = selectedType.value.isAcNonAc ?? false;
    String formatTime(String? time) {
      if (time == null || !time.contains(":")) {
        return "00:00";
      }
      List<String> parts = time.split(':');
      if (parts.length != 2) return "00:00";
      return "${parts[0].padLeft(2, '0')}:${parts[1].padLeft(2, '0')}";
    }

    startNightTime = formatTime(selectedType.value.startNightTime);
    endNightTime = formatTime(selectedType.value.endNightTime);

    List<String> startParts = startNightTime!.split(':');
    List<String> endParts = endNightTime!.split(':');

    startNightTimeString = DateTime(currentDate.year, currentDate.month, currentDate.day, int.parse(startParts[0]), int.parse(startParts[1]));
    endNightTimeString = DateTime(currentDate.year, currentDate.month, currentDate.day, int.parse(endParts[0]), int.parse(endParts[1]));

    nightCharge.value = selectedType.value.nightCharge.toString();
    if (sourceLocationLAtLng.value.latitude != null && destinationLocationLAtLng.value.latitude != null) {
      double durationValueInMinutes = convertToMinutes(duration.toString());
      if (double.tryParse(distance.value) != null && double.tryParse(basicFare.value) != null && 
          double.parse(distance.value) <= double.parse(basicFare.value)) {
    */
  }

  Rx<PaymentModel> paymentModel = PaymentModel().obs;

  RxString selectedPaymentMethod = "".obs;

  RxList airPortList = <AriPortModel>[].obs;

  getPaymentData() async {
    print('🔥 HomeController: getPaymentData() called');
    // DEMO: Load static payment data
    await Future.delayed(Duration(milliseconds: 300)); // Simulate loading
    
    // Create PaymentModel with ONLY cash payment enabled
    print('🔥 HomeController: Creating PaymentModel with ONLY Cash enabled');
    paymentModel.value = PaymentModel(
      // ONLY CASH ENABLED
      cash: Wallet(
        enable: true,
        name: "Cash"
      ),
      // ALL OTHER PAYMENT METHODS DISABLED
      wallet: Wallet(enable: false, name: "Wallet"),
      strip: Strip(enable: false, name: "Stripe"),
      flutterWave: FlutterWave(enable: false, name: "FlutterWave"),
      payStack: PayStack(enable: false, name: "PayStack"),
      mercadoPago: MercadoPago(enable: false, name: "MercadoPago"),
      razorpay: RazorpayModel(enable: false, name: "Razorpay"),
      paytm: Paytm(enable: false, name: "Paytm"),
      payfast: Payfast(enable: false, name: "Payfast"),
      paypal: Paypal(enable: false, name: "Paypal"),
      xendit: Xendit(enable: false, name: "Xendit"),
      orangePay: OrangePay(enable: false, name: "OrangePay"),
      midtrans: Midtrans(enable: false, name: "Midtrans")
    );
    print('🔥 HomeController: PaymentModel created successfully');

    // Load zones from API
    print('🔥 HomeController: Starting to load zones from API...');
    await loadZones();
    print('🔥 HomeController: Zone loading completed. Total zones: ${zoneList.length}');
  }

  // Load zones from Laravel API
  Future<void> loadZones() async {
    print('🔥 loadZones: Method called');
    try {
      print('🔥 loadZones: Calling ServiceApi.getZones()...');
      final response = await ServiceApi.getZones();
      print('🔥 loadZones: API response received: ${response.toString().substring(0, 100)}');
      
      if (response['success'] == true && response['data'] != null) {
        final List<dynamic> zonesData = response['data'];
        print('🔥 loadZones: Parsing ${zonesData.length} zones...');
        
        zoneList.value = zonesData.map((json) {
          print('🔥 loadZones: Parsing zone: ${json.toString().substring(0, 50)}...');
          return ZoneModel.fromJson(json);
        }).toList();
        
        print('✅ Loaded ${zoneList.length} zones from API');
        for (var zone in zoneList) {
          print('✅ Zone: id=${zone.id}, name=${zone.name}, area=${zone.area?.length ?? 0} points');
        }
      } else {
        print('❌ Failed to load zones: ${response['message']}');
        zoneList.value = [];
      }
    } catch (e, stackTrace) {
      print('❌ Error loading zones: $e');
      print('❌ Stack trace: $stackTrace');
      zoneList.value = [];
    }
  }

  RxList<ContactModel> contactList = <ContactModel>[].obs;
  Rx<ContactModel> selectedTakingRide = ContactModel(fullName: "Myself", contactNumber: "").obs;
  Rx<AriPortModel> selectedAirPort = AriPortModel().obs;

  setContact() {
    print(jsonEncode(contactList));
    Preferences.setString(Preferences.contactList, json.encode(contactList.map<Map<String, dynamic>>((music) => music.toJson()).toList()));
    getContact();
  }

  getContact() {
    // DEMO: Load static contact data or from preferences
    String contactListJson = Preferences.getString(Preferences.contactList);

    if (contactListJson.isNotEmpty) {
      print("---->");
      contactList.clear();
      contactList.value = (json.decode(contactListJson) as List<dynamic>).map<ContactModel>((item) => ContactModel.fromJson(item)).toList();
    } else {
      // Add default "Myself" contact for demo
      contactList.value = [
        ContactModel(fullName: "Myself", contactNumber: ""),
      ];
    }
  }
}
