
import 'package:customer/model/admin_commission.dart';
import 'package:customer/model/language_name.dart';

class IntercityServiceModel {
  String? image;
  bool? enable;
  String? kmCharge;
  String? title; // Laravel API uses 'title' instead of 'name'
  String? pricePerSeat;
  String? priceFullVehicle;
  List<LanguageName>? name; // Keep for backward compatibility
  bool? offerRate;
  String? id;
  AdminCommission? adminCommission;

  IntercityServiceModel({
    this.image, 
    this.enable, 
    this.kmCharge, 
    this.title,
    this.pricePerSeat,
    this.priceFullVehicle,
    this.name, 
    this.offerRate, 
    this.id,
    this.adminCommission
  });

  IntercityServiceModel.fromJson(Map<String, dynamic> json) {
    image = json['image'];
    
    // Handle both int and bool for 'enable'
    if (json['enable'] is int) {
      enable = json['enable'] == 1;
    } else {
      enable = json['enable'];
    }
    
    // Handle both Laravel (km_charge) and Firebase (kmCharge) formats
    kmCharge = json['km_charge'] ?? json['kmCharge'];
    
    // Handle Laravel 'title' field
    title = json['title'];
    
    // Handle Laravel price fields
    pricePerSeat = json['price_per_seat'];
    priceFullVehicle = json['price_full_vehicle'];
    
    // Handle Firebase 'name' field (for backward compatibility)
    if (json['name'] != null) {
      if (json['name'] is List) {
        name = <LanguageName>[];
        json['name'].forEach((v) {
          name!.add(LanguageName.fromJson(v));
        });
      } else if (json['name'] is String) {
        // If name is a simple string, create a single LanguageName
        name = [LanguageName(name: json['name'])];
      }
    }
    
    adminCommission = json['adminCommission'] != null 
        ? AdminCommission.fromJson(json['adminCommission']) 
        : (json['admin_commission_data'] != null 
            ? AdminCommission.fromJson(json['admin_commission_data'])
            : AdminCommission(isEnabled: true, amount: "", type: ""));

    offerRate = json['offerRate'] ?? json['offer_rate'];
    
    // Handle both String and int for 'id'
    if (json['id'] is int) {
      id = json['id'].toString();
    } else {
      id = json['id'];
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['image'] = image;
    data['enable'] = enable;
    data['kmCharge'] = kmCharge;
    data['km_charge'] = kmCharge;
    data['title'] = title;
    data['price_per_seat'] = pricePerSeat;
    data['price_full_vehicle'] = priceFullVehicle;
    if (name != null) {
      data['name'] = name!.map((v) => v.toJson()).toList();
    }
    data['offerRate'] = offerRate;
    data['id'] = id;
    if (adminCommission != null) {
      data['adminCommission'] = adminCommission!.toJson();
    }
    return data;
  }
}
