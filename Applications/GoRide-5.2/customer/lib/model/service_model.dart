import 'dart:convert';
import 'package:customer/model/admin_commission.dart';
import 'package:customer/model/language_title.dart';

class ServiceModel {
  String? image;
  bool? enable;
  bool? offerRate;
  bool? intercityType;
  bool? isAcNonAc;
  String? id;
  String? acCharge;
  String? nonAcCharge;
  String? basicFare;
  String? basicFareCharge;
  String? holdingMinute;
  String? holdingMinuteCharge;
  String? endNightTime;
  String? startNightTime;
  String? nightCharge;
  String? perMinuteCharge;
  List<LanguageTitle>? title;
  String? kmCharge;
  AdminCommission? adminCommission;

  ServiceModel(
      {this.image,
      this.enable,
      this.intercityType,
      this.isAcNonAc,
      this.offerRate,
      this.id,
      this.acCharge,
      this.nonAcCharge,
      this.basicFare,
      this.basicFareCharge,
      this.holdingMinute,
      this.holdingMinuteCharge,
      this.endNightTime,
      this.startNightTime,
      this.nightCharge,
      this.perMinuteCharge,
      this.title,
      this.kmCharge,
      this.adminCommission});

  ServiceModel.fromJson(Map<String, dynamic> json) {
    image = json['image']?.toString().replaceAll(':8080', '');
    // Convert int to bool (1 = true, 0 = false)
    enable = json['enable'] == 1 || json['enable'] == true;
    offerRate = (json['offerRate'] ?? json['offer_rate']) == 1 || (json['offerRate'] ?? json['offer_rate']) == true;
    // Convert int/decimal to String for compatibility
    id = json['id']?.toString();
    acCharge = json['acCharge']?.toString() ?? json['ac_charge']?.toString();
    nonAcCharge = json['nonAcCharge']?.toString() ?? json['non_ac_charge']?.toString();
    basicFare = json['basicFare']?.toString() ?? json['basic_fare']?.toString();
    basicFareCharge = json['basicFareCharge']?.toString() ?? json['basic_fare_charge']?.toString();
    holdingMinute = json['holdingMinute']?.toString() ?? json['holding_minute']?.toString();
    holdingMinuteCharge = json['holdingMinuteCharge']?.toString() ?? json['holding_minute_charge']?.toString();
    endNightTime = json['endNightTime'] ?? json['end_night_time'];
    startNightTime = json['startNightTime'] ?? json['start_night_time'];
    nightCharge = json['nightCharge']?.toString() ?? json['night_charge']?.toString();
    perMinuteCharge = json['perMinuteCharge']?.toString() ?? json['per_minute_charge']?.toString();
    kmCharge = json['kmCharge']?.toString() ?? json['km_charge']?.toString();
    // Convert int to bool
    intercityType = (json['intercityType'] ?? json['intercity_type']) == 1 || (json['intercityType'] ?? json['intercity_type']) == true;
    isAcNonAc = (json['isAcNonAc'] ?? json['is_ac_non_ac']) == 1 || (json['isAcNonAc'] ?? json['is_ac_non_ac']) == true;
    adminCommission = json['adminCommission'] != null
        ? AdminCommission.fromJson(json['adminCommission'])
        : (json['admin_commission_data'] != null 
            ? AdminCommission.fromJson(json['admin_commission_data'])
            : AdminCommission(isEnabled: true, amount: "", type: ""));
    
    // Handle title - can be array or JSON string
    if (json['title'] != null) {
      print('🔥 ServiceModel: title raw data = ${json['title']}');
      print('🔥 ServiceModel: title type = ${json['title'].runtimeType}');
      title = <LanguageTitle>[];
      var titleData = json['title'];
      
      // If title is already a List (from API after our fix)
      if (titleData is List) {
        print('🔥 ServiceModel: title is List, length=${titleData.length}');
        titleData.forEach((v) {
          print('🔥 ServiceModel: parsing title item: $v');
          title!.add(LanguageTitle.fromJson(v));
        });
        print('🔥 ServiceModel: parsed ${title!.length} title items');
      } 
      // If title is a String (old data in database)
      else if (titleData is String) {
        print('🔥 ServiceModel: title is String, attempting to decode...');
        try {
          var decoded = jsonDecode(titleData);
          print('🔥 ServiceModel: decoded title = $decoded');
          if (decoded is List) {
            decoded.forEach((v) {
              print('🔥 ServiceModel: parsing title item from string: $v');
              title!.add(LanguageTitle.fromJson(v));
            });
            print('🔥 ServiceModel: parsed ${title!.length} title items from string');
          }
        } catch (e) {
          print('❌ Error parsing title string: $e');
        }
      }
    } else {
      print('❌ ServiceModel: title is NULL!');
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['image'] = image;
    data['enable'] = enable;
    data['offerRate'] = offerRate;
    data['id'] = id;
    data['acCharge'] = acCharge;
    data['nonAcCharge'] = nonAcCharge;
    data['basicFare'] = basicFare;
    data['basicFareCharge'] = basicFareCharge;
    data['holdingMinute'] = holdingMinute;
    data['holdingMinuteCharge'] = holdingMinuteCharge;
    data['endNightTime'] = endNightTime;
    data['startNightTime'] = startNightTime;
    data['nightCharge'] = nightCharge;
    data['perMinuteCharge'] = perMinuteCharge;
    data['title'] = title;
    data['kmCharge'] = kmCharge;
    data['intercityType'] = intercityType;
    data['isAcNonAc'] = isAcNonAc;
    if (title != null) {
      data['title'] = title!.map((v) => v.toJson()).toList();
    }

    if (adminCommission != null) {
      data['adminCommission'] = adminCommission!.toJson();
    }
    return data;
  }
}
