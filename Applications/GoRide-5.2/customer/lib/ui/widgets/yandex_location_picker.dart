import 'package:flutter/material.dart';
import 'package:yandex_mapkit/yandex_mapkit.dart';
import 'package:geolocator/geolocator.dart';
import 'package:get/get.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:customer/constant/constant.dart';
import 'package:customer/themes/app_colors.dart';

class YandexLocationPicker extends StatefulWidget {
  final Point? initialLocation;
  final String title;
  final Function(Point, String) onLocationSelected;

  const YandexLocationPicker({
    super.key,
    this.initialLocation,
    required this.title,
    required this.onLocationSelected,
  });

  @override
  State<YandexLocationPicker> createState() => _YandexLocationPickerState();
}

class _YandexLocationPickerState extends State<YandexLocationPicker> {
  late YandexMapController _mapController;
  Point? _selectedLocation;
  Point? _currentLocation;
  String _selectedAddress = '';
  bool _isLoading = false;
  PlacemarkMapObject? _selectedMarker;
  PlacemarkMapObject? _currentLocationMarker;

  // Границы Туркменистана
  static const Point _centerTurkmenistan = Point(latitude: 38.9697, longitude: 59.5563); // Ашхабад

  // Кеш для текущего местоположения
  static Point? _cachedCurrentLocation;

  @override
  void initState() {
    super.initState();
    _initializeLocation();
  }

  Future<void> _initializeLocation() async {
    if (mounted) {
      setState(() => _isLoading = true);
    }

    // Приоритет: кешированное местоположение > новое GPS > initial position > центр Ашхабада
    if (_cachedCurrentLocation != null) {
      _currentLocation = _cachedCurrentLocation;
      print('Using cached location: $_currentLocation');
    } else {
      await _getCurrentLocation();
    }

    // Всегда начинаем с текущего местоположения (приоритет над initial position)
    if (_currentLocation != null) {
      _selectedLocation = _currentLocation;
      print('Starting with current location: $_selectedLocation');
    } else if (widget.initialLocation != null) {
      _selectedLocation = widget.initialLocation;
      print('Using initial location: $_selectedLocation');
    } else {
      _selectedLocation = _centerTurkmenistan;
      print('Using default Ashgabat location');
    }

    await _getAddressFromLatLng(_selectedLocation!);
    if (mounted) {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _getCurrentLocation() async {
    try {
      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
        if (permission == LocationPermission.denied) {
          print('Location permissions are denied');
          return;
        }
      }
      if (permission == LocationPermission.deniedForever) {
        print('Location permissions are permanently denied, we cannot request permissions.');
        return;
      }

      Position position = await Geolocator.getCurrentPosition(desiredAccuracy: LocationAccuracy.high);
      Point currentPos = Point(latitude: position.latitude, longitude: position.longitude);

      // Проверяем, находится ли текущая позиция в границах Туркменистана
      if (_isWithinTurkmenistan(currentPos)) {
        if (mounted) {
          setState(() {
            _currentLocation = currentPos;
            _cachedCurrentLocation = currentPos; // Кешируем для следующих открытий
          });
        }
        print('Current location found and cached: $currentPos');
      } else {
        print('Current location outside Turkmenistan bounds: $currentPos');
        Get.snackbar('Location Alert'.tr, 'Your current location is outside Turkmenistan.'.tr, snackPosition: SnackPosition.BOTTOM);
      }
    } catch (e) {
      print('Error getting current location: $e');
    }
  }

  bool _isWithinTurkmenistan(Point location) {
    // Границы Туркменистана: 35.1-42.8 широта, 52.5-66.7 долгота
    return location.latitude >= 35.1 &&
           location.latitude <= 42.8 &&
           location.longitude >= 52.5 &&
           location.longitude <= 66.7;
  }

  Future<void> _getAddressFromLatLng(Point location) async {
    try {
      print('🗺️ Yandex Geocoding for: ${location.latitude}, ${location.longitude}');

      final url = 'https://geocode-maps.yandex.ru/1.x/?apikey=${Constant.yandexAPIKey}&geocode=${location.longitude},${location.latitude}&format=json&lang=ru_RU&results=1';

      final response = await http.get(Uri.parse(url));

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        print('🗺️ Yandex response: ${response.body}');

        final geoObjects = data['response']['GeoObjectCollection']['featureMember'];

        if (geoObjects != null && geoObjects.isNotEmpty) {
          final geoObject = geoObjects[0]['GeoObject'];
          final metaData = geoObject['metaDataProperty']['GeocoderMetaData'];

          // Get formatted address
          String formattedAddress = geoObject['name'] ?? '';
          String description = geoObject['description'] ?? '';

          if (description.isNotEmpty) {
            if (formattedAddress.isNotEmpty) {
              formattedAddress = '$formattedAddress, $description';
            } else {
              formattedAddress = description;
            }
          }

          // Try to get more specific address from components
          final components = metaData['Address']['Components'] as List?;
          List<String> addressParts = [];

          if (components != null) {
            String? house = '';
            String? street = '';
            String? district = '';
            String? city = '';

            for (var component in components) {
              final kind = component['kind'];
              final name = component['name'];

              switch (kind) {
                case 'house':
                  house = name;
                  break;
                case 'street':
                  street = name;
                  break;
                case 'district':
                  district = name;
                  break;
                case 'locality':
                case 'province':
                  city = name;
                  break;
              }
            }

            // Build specific address
            if (street?.isNotEmpty == true) {
              String streetAddress = street!;
              if (house?.isNotEmpty == true) {
                streetAddress += ', $house';
              }
              addressParts.add(streetAddress);
            }

            if (district?.isNotEmpty == true) {
              addressParts.add(district!);
            }

            if (city?.isNotEmpty == true) {
              addressParts.add(city!);
            }
          }

          if (mounted) {
            setState(() {
              if (addressParts.isNotEmpty) {
                _selectedAddress = addressParts.join(', ');
              } else if (formattedAddress.isNotEmpty) {
                _selectedAddress = formattedAddress;
              } else {
                _selectedAddress = 'Адрес: ${location.latitude.toStringAsFixed(4)}, ${location.longitude.toStringAsFixed(4)}';
              }
            });
          }

          print('🗺️ Yandex final address: $_selectedAddress');
        } else {
          if (mounted) {
            setState(() {
              _selectedAddress = 'Координаты: ${location.latitude.toStringAsFixed(4)}, ${location.longitude.toStringAsFixed(4)}';
            });
          }
          print('🗺️ Yandex: No results found');
        }
      } else {
        print('🗺️ Yandex API error: ${response.statusCode}');
        if (mounted) {
          setState(() {
            _selectedAddress = 'Координаты: ${location.latitude.toStringAsFixed(4)}, ${location.longitude.toStringAsFixed(4)}';
          });
        }
      }
    } catch (e) {
      print('🗺️ Yandex Geocoding exception: $e');
      if (mounted) {
        setState(() {
          _selectedAddress = 'Координаты: ${location.latitude.toStringAsFixed(4)}, ${location.longitude.toStringAsFixed(4)}';
        });
      }
    }
  }

  void _updateMarker(Point location) {
    // Проверяем, что выбранная точка в границах Туркменистана
    if (_isWithinTurkmenistan(location)) {
      if (mounted) {
        setState(() {
          _selectedLocation = location;
          _updateMapMarkers();
        });
      }
      _getAddressFromLatLng(location);
    }
  }

  void _updateMapMarkers() {
    final markers = <PlacemarkMapObject>[];

    // Selected location marker
    if (_selectedLocation != null) {
      markers.add(
        PlacemarkMapObject(
          mapId: const MapObjectId('selected_location'),
          point: _selectedLocation!,
          opacity: 1,
          icon: PlacemarkIcon.single(
            PlacemarkIconStyle(
              image: BitmapDescriptor.fromAssetImage('assets/images/location_pin.png'),
              scale: 0.15,
            ),
          ),
        ),
      );
    }

    // Current location marker
    if (_currentLocation != null && _selectedLocation != _currentLocation) {
      markers.add(
        PlacemarkMapObject(
          mapId: const MapObjectId('current_location'),
          point: _currentLocation!,
          opacity: 1,
          icon: PlacemarkIcon.single(
            PlacemarkIconStyle(
              image: BitmapDescriptor.fromAssetImage('assets/images/current_location.png'),
              scale: 0.15,
            ),
          ),
        ),
      );
    }

    _mapController.moveCamera(
      CameraUpdate.newCameraPosition(
        CameraPosition(target: _selectedLocation!, zoom: 16.0),
      ),
    );
  }

  void _goToCurrentLocation() {
    if (_currentLocation != null) {
      _mapController.moveCamera(
        CameraUpdate.newCameraPosition(
          CameraPosition(target: _currentLocation!, zoom: 16.0),
        ),
      );
      if (mounted) {
        setState(() {
          _selectedLocation = _currentLocation;
          _updateMapMarkers();
        });
      }
      _getAddressFromLatLng(_currentLocation!);
    }
  }

  void _confirmSelection() {
    if (_selectedLocation != null) {
      widget.onLocationSelected(_selectedLocation!, _selectedAddress);
      Get.back();
    } else {
      Get.snackbar('Error'.tr, 'Please select a location'.tr, snackPosition: SnackPosition.BOTTOM);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.title),
        backgroundColor: AppColors.primary,
      ),
      body: _isLoading
          ? Constant.loader()
          : Stack(
              children: [
                YandexMap(
                  onMapCreated: (controller) {
                    _mapController = controller;
                    _updateMapMarkers();
                  },
                  mapObjects: [
                    if (_selectedLocation != null)
                      PlacemarkMapObject(
                        mapId: const MapObjectId('selected_location'),
                        point: _selectedLocation!,
                        opacity: 1,
                        icon: PlacemarkIcon.single(
                          PlacemarkIconStyle(
                            image: BitmapDescriptor.fromAssetImage('assets/images/location_pin.png'),
                            scale: 0.15,
                          ),
                        ),
                      ),
                    if (_currentLocation != null && _selectedLocation != _currentLocation)
                      PlacemarkMapObject(
                        mapId: const MapObjectId('current_location'),
                        point: _currentLocation!,
                        opacity: 1,
                        icon: PlacemarkIcon.single(
                          PlacemarkIconStyle(
                            image: BitmapDescriptor.fromAssetImage('assets/images/current_location.png'),
                            scale: 0.15,
                          ),
                        ),
                      ),
                  ],
                  onMapTap: (point) {
                    _updateMarker(point);
                  },
                ),
                Positioned(
                  bottom: 100,
                  right: 20,
                  child: FloatingActionButton(
                    onPressed: _goToCurrentLocation,
                    backgroundColor: AppColors.primary,
                    child: const Icon(Icons.my_location, color: Colors.white),
                  ),
                ),
                Positioned(
                  bottom: 0,
                  left: 0,
                  right: 0,
                  child: Container(
                    padding: const EdgeInsets.all(16.0),
                    color: Colors.white,
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(
                          _selectedAddress.isNotEmpty ? _selectedAddress : 'Select a location'.tr,
                          style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                          textAlign: TextAlign.center,
                        ),
                        const SizedBox(height: 10),
                        ElevatedButton(
                          onPressed: _confirmSelection,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppColors.primary,
                            minimumSize: const Size(double.infinity, 50),
                          ),
                          child: Text('Confirm Location'.tr, style: const TextStyle(color: Colors.white)),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
    );
  }
}
