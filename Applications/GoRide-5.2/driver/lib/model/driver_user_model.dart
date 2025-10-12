import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:driver/model/driver_rules_model.dart';
import 'package:driver/model/language_name.dart';
import 'package:driver/model/order/location_lat_lng.dart';
import 'package:driver/model/order/positions.dart';
import 'package:driver/model/subscription_plan_model.dart';

class DriverUserModel {
  String? phoneNumber;
  String? loginType;
  String? countryCode;
  String? profilePic;
  bool? documentVerification;
  String? fullName;
  bool? isOnline;
  String? id;
  String? serviceId;
  String? fcmToken;
  String? email;
  VehicleInformation? vehicleInformation;
  String? reviewsCount;
  String? reviewsSum;
  String? walletAmount;
  LocationLatLng? location;
  double? rotation;
  Positions? position;
  Timestamp? createdAt;
  List<dynamic>? zoneIds;
  String? subscriptionTotalOrders;
  String? subscriptionPlanId;
  Timestamp? subscriptionExpiryDate;
  SubscriptionPlanModel? subscriptionPlan;

  DriverUserModel(
      {this.phoneNumber,
      this.loginType,
      this.countryCode,
      this.profilePic,
      this.documentVerification,
      this.fullName,
      this.isOnline,
      this.id,
      this.serviceId,
      this.fcmToken,
      this.email,
      this.location,
      this.vehicleInformation,
      this.reviewsCount,
      this.reviewsSum,
      this.rotation,
      this.position,
      this.walletAmount,
      this.createdAt,
      this.zoneIds,
      this.subscriptionTotalOrders,
      this.subscriptionPlanId,
      this.subscriptionExpiryDate,
      this.subscriptionPlan});

  DriverUserModel.fromJson(Map<String, dynamic> json) {
    // Support both camelCase (Firestore) and snake_case (Laravel API)
    phoneNumber = json['phoneNumber'] ?? json['phone'];
    loginType = json['loginType'] ?? json['login_type'];
    countryCode = json['countryCode'] ?? json['country_code'];
    profilePic = json['profilePic'] ?? json['profile_pic'] ?? '';
    documentVerification = json['documentVerification'] ?? json['document_verification'];
    fullName = json['fullName'] ?? json['full_name'];
    
    // Convert int to bool for isOnline
    final isOnlineValue = json['isOnline'] ?? json['is_online'];
    isOnline = isOnlineValue is bool ? isOnlineValue : (isOnlineValue == 1 || isOnlineValue == true);
    
    // Convert int to String for id
    id = json['id']?.toString();
    
    // Convert int to String for serviceId
    serviceId = (json['serviceId'] ?? json['service_id'])?.toString();
    
    fcmToken = json['fcmToken'] ?? json['fcm_token'];
    email = json['email'];
    
    vehicleInformation = json['vehicleInformation'] != null
        ? VehicleInformation.fromJson(json['vehicleInformation'])
        : (json['vehicle_information'] != null
            ? VehicleInformation.fromJson(json['vehicle_information'])
            : null);
    
    reviewsCount = (json['reviewsCount'] ?? json['reviews_count'])?.toString() ?? '0.0';
    reviewsSum = (json['reviewsSum'] ?? json['reviews_sum'])?.toString() ?? '0.0';
    
    rotation = json['rotation'] is String 
        ? double.tryParse(json['rotation'])
        : (json['rotation'] as num?)?.toDouble();
    
    walletAmount = (json['walletAmount'] ?? json['wallet_amount'])?.toString() ?? "0.0";
    
    // Location from API
    if (json['latitude'] != null && json['longitude'] != null) {
      location = LocationLatLng(
        latitude: json['latitude'] is String 
            ? double.tryParse(json['latitude'])
            : (json['latitude'] as num?)?.toDouble(),
        longitude: json['longitude'] is String 
            ? double.tryParse(json['longitude'])
            : (json['longitude'] as num?)?.toDouble(),
      );
    } else if (json['location'] != null) {
      location = LocationLatLng.fromJson(json['location']);
    }
    
    position = json['position'] != null ? Positions.fromJson(json['position']) : null;
    
    // Handle created_at
    final createdAtValue = json['createdAt'] ?? json['created_at'];
    if (createdAtValue != null) {
      if (createdAtValue is Timestamp) {
        createdAt = createdAtValue;
      } else if (createdAtValue is String) {
        try {
          final dateTime = DateTime.parse(createdAtValue);
          createdAt = Timestamp.fromDate(dateTime);
        } catch (e) {
          createdAt = null;
        }
      }
    }
    
    // Handle zone_ids as array
    if (json['zoneIds'] != null) {
      if (json['zoneIds'] is List) {
        zoneIds = json['zoneIds'];
      } else if (json['zoneIds'] is String) {
        final zoneIdsStr = json['zoneIds'] as String;
        if (zoneIdsStr.isNotEmpty) {
          zoneIds = zoneIdsStr.split(',').map((e) => e.trim()).toList();
        }
      }
    } else if (json['zone_ids'] != null) {
      // Convert zone_ids string to array if needed
      if (json['zone_ids'] is List) {
        zoneIds = json['zone_ids'];
      } else if (json['zone_ids'] is String) {
        final zoneIdsStr = json['zone_ids'] as String;
        if (zoneIdsStr.isNotEmpty) {
          zoneIds = zoneIdsStr.split(',').map((e) => e.trim()).toList();
        }
      }
    }
    
    subscriptionTotalOrders = (json['subscriptionTotalOrders'] ?? json['subscription_total_orders'])?.toString();
    subscriptionPlanId = (json['subscriptionPlanId'] ?? json['subscription_plan_id'])?.toString();
    
    // Handle subscription_expiry_date
    final expiryDate = json['subscriptionExpiryDate'] ?? json['subscription_expiry_date'];
    if (expiryDate != null) {
      if (expiryDate is Timestamp) {
        subscriptionExpiryDate = expiryDate;
      } else if (expiryDate is String) {
        try {
          final dateTime = DateTime.parse(expiryDate);
          subscriptionExpiryDate = Timestamp.fromDate(dateTime);
        } catch (e) {
          subscriptionExpiryDate = null;
        }
      }
    }
    
    subscriptionPlan = json['subscription_plan'] != null
        ? SubscriptionPlanModel.fromJson(json['subscription_plan'])
        : null;
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['phoneNumber'] = phoneNumber;
    data['loginType'] = loginType;
    data['countryCode'] = countryCode;
    data['profilePic'] = profilePic;
    data['documentVerification'] = documentVerification;
    data['fullName'] = fullName;
    data['isOnline'] = isOnline;
    data['id'] = id;
    data['serviceId'] = serviceId;
    data['fcmToken'] = fcmToken;
    data['email'] = email;
    data['rotation'] = rotation;
    data['createdAt'] = createdAt;
    if (vehicleInformation != null) {
      data['vehicleInformation'] = vehicleInformation!.toJson();
    }
    if (location != null) {
      data['location'] = location!.toJson();
    }
    data['reviewsCount'] = reviewsCount;
    data['reviewsSum'] = reviewsSum;
    data['walletAmount'] = walletAmount;
    data['zoneIds'] = zoneIds;
    if (position != null) {
      data['position'] = position!.toJson();
    }
    data['subscriptionTotalOrders'] = subscriptionTotalOrders;
    data['subscriptionPlanId'] = subscriptionPlanId;
    data['subscriptionExpiryDate'] = subscriptionExpiryDate;
    data['subscription_plan'] = subscriptionPlan?.toJson();
    return data;
  }
}

class VehicleInformation {
  List<LanguageName>? vehicleType;
  String? vehicleTypeId;
  Timestamp? registrationDate;
  String? vehicleColor;
  String? vehicleNumber;
  String? acPerKmRate;
  String? nonAcPerKmRate;
  String? perKmRate;
  String? seats;
  List<DriverRulesModel>? driverRules;

  VehicleInformation(
      {this.vehicleType,
      this.vehicleTypeId,
      this.registrationDate,
      this.vehicleColor,
      this.vehicleNumber,
      this.acPerKmRate,
      this.nonAcPerKmRate,
      this.perKmRate,
      this.seats,
      this.driverRules});

  VehicleInformation.fromJson(Map<String, dynamic> json) {
    if (json['vehicleType'] != null) {
      vehicleType = <LanguageName>[];
      json['vehicleType'].forEach((v) {
        vehicleType!.add(LanguageName.fromJson(v));
      });
    }
    vehicleTypeId = json['vehicleTypeId'];
    registrationDate = json['registrationDate'];
    vehicleColor = json['vehicleColor'];
    vehicleNumber = json['vehicleNumber'];
    acPerKmRate = json['acPerKmRate'];
    nonAcPerKmRate = json['nonAcPerKmRate'];
    perKmRate = json['perKmRate'];
    seats = json['seats'];
    if (json['driverRules'] != null) {
      driverRules = <DriverRulesModel>[];
      json['driverRules'].forEach((v) {
        driverRules!.add(DriverRulesModel.fromJson(v));
      });
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    if (vehicleType != null) {
      data['vehicleType'] = vehicleType!.map((v) => v.toJson()).toList();
    }
    data['vehicleTypeId'] = vehicleTypeId;
    data['registrationDate'] = registrationDate;
    data['vehicleColor'] = vehicleColor;
    data['vehicleNumber'] = vehicleNumber;
    data['acPerKmRate'] = acPerKmRate;
    data['nonAcPerKmRate'] = nonAcPerKmRate;
    data['perKmRate'] = perKmRate;
    data['seats'] = seats;
    if (driverRules != null) {
      data['driverRules'] = driverRules!.map((v) => v.toJson()).toList();
    }
    return data;
  }
}
