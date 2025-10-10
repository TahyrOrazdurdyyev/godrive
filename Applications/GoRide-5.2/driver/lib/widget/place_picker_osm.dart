import 'package:driver/themes/app_colors.dart';
import 'package:driver/widget/turkmenistan_location_picker.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:latlong2/latlong.dart' as osm;

// Wrapper для обратной совместимости
// Теперь использует TurkmenistanLocationPicker с flutter_map
class LocationPicker extends StatelessWidget {
  const LocationPicker({super.key});

  @override
  Widget build(BuildContext context) {
    return TurkmenistanLocationPicker(
      title: 'Location Picker'.tr,
      onLocationSelected: (osm.LatLng location, String address) {
        // Возвращаем результат в формате, ожидаемом вызывающим кодом
        Get.back(result: {
          'address': address,
          'latitude': location.latitude,
          'longitude': location.longitude,
        });
      },
    );
  }
}