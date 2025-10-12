import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:geolocator/geolocator.dart';
import 'package:get/get.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:driver/constant/constant.dart';
import 'package:driver/themes/app_colors.dart';

class TurkmenistanLocationPicker extends StatefulWidget {
  final LatLng? initialLocation;
  final String title;
  final Function(LatLng, String) onLocationSelected;

  const TurkmenistanLocationPicker({
    Key? key,
    this.initialLocation,
    this.title = 'Select Location',
    required this.onLocationSelected,
  }) : super(key: key);

  @override
  State<TurkmenistanLocationPicker> createState() => _TurkmenistanLocationPickerState();
}

class _TurkmenistanLocationPickerState extends State<TurkmenistanLocationPicker> {
  final MapController _mapController = MapController();
  LatLng? _selectedLocation;
  LatLng? _currentLocation;
  String _selectedAddress = '';
  bool _isLoading = false;
  
  // Границы Туркменистана
  static const LatLng _southWest = LatLng(35.1, 52.5);
  static const LatLng _northEast = LatLng(42.8, 66.7);
  static const LatLng _centerTurkmenistan = LatLng(38.9697, 59.5563); // Ашхабад
  
  // Кеш для текущего местоположения
  static LatLng? _cachedCurrentLocation;

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
      print('🗺️ Driver: Using cached location: $_currentLocation');
    } else {
      await _getCurrentLocation();
    }
    
    // Всегда начинаем с текущего местоположения (приоритет над initial position)
    if (_currentLocation != null) {
      _selectedLocation = _currentLocation;
      print('🗺️ Driver: Starting with current location: $_selectedLocation');
    } else if (widget.initialLocation != null) {
      _selectedLocation = widget.initialLocation;
      print('🗺️ Driver: Using initial location: $_selectedLocation');
    } else {
      _selectedLocation = _centerTurkmenistan;
      print('🗺️ Driver: Using default Ashgabat location');
    }
    
    await _getAddressFromLatLng(_selectedLocation!);
    if (mounted) {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _getCurrentLocation() async {
    try {
      bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) {
        print('🗺️ Driver: Location services are disabled');
        return;
      }

      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
        if (permission == LocationPermission.denied) {
          print('🗺️ Driver: Location permissions are denied');
          return;
        }
      }

      if (permission == LocationPermission.deniedForever) {
        print('🗺️ Driver: Location permissions are permanently denied');
        return;
      }

      Position position = await Geolocator.getCurrentPosition();
      LatLng currentPos = LatLng(position.latitude, position.longitude);
      
      // Проверяем, находится ли текущая позиция в границах Туркменистана
      if (_isWithinTurkmenistan(currentPos)) {
        if (mounted) {
          setState(() {
            _currentLocation = currentPos;
            _cachedCurrentLocation = currentPos; // Кешируем для следующих открытий
          });
        }
        print('🗺️ Driver: Current location found and cached: $currentPos');
      } else {
        print('🗺️ Driver: Current location is outside Turkmenistan');
      }
      
    } catch (e) {
      print('🗺️ Driver: Error getting current location: $e');
    }
  }

  bool _isWithinTurkmenistan(LatLng location) {
    return location.latitude >= _southWest.latitude &&
           location.latitude <= _northEast.latitude &&
           location.longitude >= _southWest.longitude &&
           location.longitude <= _northEast.longitude;
  }

  Future<void> _getAddressFromLatLng(LatLng location) async {
    try {
      print('🗺️ Driver: Yandex Geocoding for: ${location.latitude}, ${location.longitude}');
      
      final url = 'https://geocode-maps.yandex.ru/1.x/?apikey=${Constant.yandexAPIKey}&geocode=${location.longitude},${location.latitude}&format=json&lang=ru_RU&results=1';
      
      final response = await http.get(Uri.parse(url));
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        print('🗺️ Driver: Yandex response: ${response.body}');
        
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
          
          print('🗺️ Driver: Yandex final address: $_selectedAddress');
        } else {
          if (mounted) {
            setState(() {
              _selectedAddress = 'Координаты: ${location.latitude.toStringAsFixed(4)}, ${location.longitude.toStringAsFixed(4)}';
            });
          }
          print('🗺️ Driver: Yandex - No results found');
        }
      } else {
        print('🗺️ Driver: Yandex API error: ${response.statusCode}');
        if (mounted) {
          setState(() {
            _selectedAddress = 'Координаты: ${location.latitude.toStringAsFixed(4)}, ${location.longitude.toStringAsFixed(4)}';
          });
        }
      }
    } catch (e) {
      print('🗺️ Driver: Yandex Geocoding exception: $e');
      if (mounted) {
        setState(() {
          _selectedAddress = 'Координаты: ${location.latitude.toStringAsFixed(4)}, ${location.longitude.toStringAsFixed(4)}';
        });
      }
    }
  }

  void _onMapTapped(TapPosition tapPosition, LatLng location) {
    // Проверяем, что выбранная точка в границах Туркменистана
    if (_isWithinTurkmenistan(location)) {
      setState(() {
        _selectedLocation = location;
      });
      _getAddressFromLatLng(location);
    }
  }

  void _goToCurrentLocation() {
    if (_currentLocation != null) {
      _mapController.move(_currentLocation!, 16.0); // Высокий зум для удобства
      setState(() {
        _selectedLocation = _currentLocation;
      });
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
        leading: IconButton(
          icon: Icon(Icons.arrow_back),
          onPressed: () => Get.back(),
        ),
      ),
      floatingActionButton: _currentLocation != null
          ? FloatingActionButton(
              onPressed: _goToCurrentLocation,
              child: Icon(Icons.my_location),
              backgroundColor: AppColors.primary,
            )
          : null,
      body: _isLoading
          ? Center(child: CircularProgressIndicator())
          : Stack(
              children: [
                // OpenStreetMap через наш proxy сервер
                FlutterMap(
                  mapController: _mapController,
                  options: MapOptions(
                    initialCenter: _selectedLocation ?? _currentLocation ?? _centerTurkmenistan,
                    initialZoom: 16.0, // Увеличенный зум для удобства выбора
                    minZoom: 6.0,
                    maxZoom: 18.0,
                    // Ограничиваем карту границами Туркменистана
                    cameraConstraint: CameraConstraint.contain(
                      bounds: LatLngBounds(_southWest, _northEast),
                    ),
                    onTap: _onMapTapped,
                    interactionOptions: InteractionOptions(
                      flags: InteractiveFlag.all,
                    ),
                  ),
                  children: [
                    // Тайлы OpenStreetMap через собственный proxy сервер
                    TileLayer(
                      urlTemplate: 'http://185.10.16.248:8081/{z}/{x}/{y}.png',
                      userAgentPackageName: 'com.goride.driver',
                      maxZoom: 19,
                    ),
                    
                    // Маркеры
                    MarkerLayer(
                      markers: [
                        // Текущее местоположение (синий маркер)
                        if (_currentLocation != null && _selectedLocation != _currentLocation)
                          Marker(
                            point: _currentLocation!,
                            width: 40,
                            height: 40,
                            child: Container(
                              decoration: BoxDecoration(
                                color: Colors.blue,
                                shape: BoxShape.circle,
                                border: Border.all(color: Colors.white, width: 3),
                              ),
                              child: Icon(
                                Icons.person,
                                color: Colors.white,
                                size: 20,
                              ),
                            ),
                          ),
                        
                        // Выбранная локация (красный маркер)
                        if (_selectedLocation != null)
                          Marker(
                            point: _selectedLocation!,
                            width: 40,
                            height: 40,
                            child: Icon(
                              Icons.location_pin,
                              color: Colors.red,
                              size: 40,
                            ),
                          ),
                      ],
                    ),
                  ],
                ),
                
                // Address info panel
                Positioned(
                  bottom: 0,
                  left: 0,
                  right: 0,
                  child: Container(
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.only(
                        topLeft: Radius.circular(20),
                        topRight: Radius.circular(20),
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.1),
                          blurRadius: 10,
                          offset: Offset(0, -2),
                        ),
                      ],
                    ),
                    child: Padding(
                      padding: EdgeInsets.all(20),
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Drag handle
                          Center(
                            child: Container(
                              width: 40,
                              height: 4,
                              decoration: BoxDecoration(
                                color: Colors.grey[300],
                                borderRadius: BorderRadius.circular(2),
                              ),
                            ),
                          ),
                          SizedBox(height: 16),
                          
                          // Selected location info
                          Text(
                            'Selected Location'.tr,
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          SizedBox(height: 8),
                          
                          Text(
                            _selectedAddress.isNotEmpty ? _selectedAddress : 'Tap on map to select location'.tr,
                            style: TextStyle(
                              fontSize: 14,
                              color: Colors.grey[600],
                            ),
                            maxLines: 3,
                            overflow: TextOverflow.ellipsis,
                          ),
                          SizedBox(height: 16),
                          
                          // Confirm button
                          SizedBox(
                            width: double.infinity,
                            child: ElevatedButton(
                              onPressed: _selectedLocation != null ? _confirmSelection : null,
                              style: ElevatedButton.styleFrom(
                                backgroundColor: AppColors.primary,
                                foregroundColor: Colors.white,
                                padding: EdgeInsets.symmetric(vertical: 16),
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(8),
                                ),
                              ),
                              child: Text(
                                'Confirm Location'.tr,
                                style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ],
            ),
    );
  }
}
