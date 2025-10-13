import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:customer/model/language_name.dart';

class LatLon {
  double? lat;
  double? lon;

  LatLon({this.lat, this.lon});

  LatLon.fromJson(Map<String, dynamic> json) {
    lat = json['lat']?.toDouble();
    lon = json['lon']?.toDouble();
  }

  Map<String, dynamic> toJson() {
    return {'lat': lat, 'lon': lon};
  }
}

class ZoneModel {
  List<LatLon>? area;
  bool? publish;
  double? latitude;
  String? name;  // Changed from List<LanguageName> to String
  String? id;
  double? longitude;

  ZoneModel({this.area, this.publish, this.latitude, this.name, this.id, this.longitude});

  ZoneModel.fromJson(Map<String, dynamic> json) {
    // Parse area as list of LatLon objects
    if (json['area'] != null && json['area'] is List) {
      area = <LatLon>[];
      json['area'].forEach((v) {
        if (v is Map<String, dynamic>) {
          area!.add(LatLon.fromJson(v));
        }
      });
    }

    // Parse name as simple string
    name = json['name']?.toString();

    // Parse publish/enable (Laravel uses 'enable')
    publish = json['publish'] ?? (json['enable'] == 1);
    
    latitude = json['latitude']?.toDouble();
    
    // Parse id as string
    if (json['id'] != null) {
      id = json['id'].toString();
    }
    
    longitude = json['longitude']?.toDouble();
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    if (area != null) {
      data['area'] = area!.map((v) => v.toJson()).toList();
    }
    data['name'] = name;
    data['publish'] = publish;
    data['latitude'] = latitude;
    data['id'] = id;
    data['longitude'] = longitude;
    return data;
  }
}
