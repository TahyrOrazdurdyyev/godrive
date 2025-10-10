import 'dart:developer';
import 'package:customer/services/api_service.dart';

class InterCityOrderApi {
  /// Create intercity order
  static Future<Map<String, dynamic>> createOrder({
    required int userId,
    required int intercityServiceId,
    required String sourceCity,
    required String sourceLocationName,
    required String destinationCity,
    required String destinationLocationName,
    required double sourceLat,
    required double sourceLng,
    required double destinationLat,
    required double destinationLng,
    required double distance,
    required double offerRate,
    int? zoneId,
    int? couponId,
    int? freightVehicleId,
    String? distanceType,
    double? finalRate,
    String? paymentType,
    String? parcelDimension,
    String? parcelWeight,
    List<String>? parcelImages,
    String? whenDate,
    String? whenTime,
    int? numberOfPassenger,
  }) async {
    try {
      final body = {
        'user_id': userId,
        'intercity_service_id': intercityServiceId,
        'source_city': sourceCity,
        'source_location_name': sourceLocationName,
        'destination_city': destinationCity,
        'destination_location_name': destinationLocationName,
        'source_lat': sourceLat,
        'source_lng': sourceLng,
        'destination_lat': destinationLat,
        'destination_lng': destinationLng,
        'distance': distance,
        'offer_rate': offerRate,
      };

      if (zoneId != null) body['zone_id'] = zoneId;
      if (couponId != null) body['coupon_id'] = couponId;
      if (freightVehicleId != null) body['freight_vehicle_id'] = freightVehicleId;
      if (distanceType != null) body['distance_type'] = distanceType;
      if (finalRate != null) body['final_rate'] = finalRate;
      if (paymentType != null) body['payment_type'] = paymentType;
      if (parcelDimension != null) body['parcel_dimension'] = parcelDimension;
      if (parcelWeight != null) body['parcel_weight'] = parcelWeight;
      if (parcelImages != null) body['parcel_images'] = parcelImages;
      if (whenDate != null) body['when_date'] = whenDate;
      if (whenTime != null) body['when_time'] = whenTime;
      if (numberOfPassenger != null) body['number_of_passenger'] = numberOfPassenger;

      final response = await ApiService.post('/api/intercity-orders', body: body);
      return response;
    } catch (e) {
      log('❌ Create InterCity Order Error: $e');
      rethrow;
    }
  }

  /// Get intercity order by ID
  static Future<Map<String, dynamic>> getOrderById(String orderId) async {
    try {
      final response = await ApiService.get('/api/intercity-orders/$orderId');
      return response;
    } catch (e) {
      log('❌ Get InterCity Order Error: $e');
      rethrow;
    }
  }

  /// Get customer intercity orders
  static Future<Map<String, dynamic>> getCustomerOrders(int userId) async {
    try {
      final response = await ApiService.get('/api/intercity-orders/customer', queryParams: {
        'user_id': userId.toString(),
      });
      return response;
    } catch (e) {
      log('❌ Get Customer InterCity Orders Error: $e');
      rethrow;
    }
  }

  /// Update intercity order
  static Future<Map<String, dynamic>> updateOrder({
    required String orderId,
    required Map<String, dynamic> data,
  }) async {
    try {
      final response = await ApiService.put('/api/intercity-orders/$orderId', body: data);
      return response;
    } catch (e) {
      log('❌ Update InterCity Order Error: $e');
      rethrow;
    }
  }

  /// Cancel intercity order
  static Future<Map<String, dynamic>> cancelOrder({
    required String orderId,
  }) async {
    try {
      final response = await ApiService.post('/api/intercity-orders/$orderId/cancel', body: {});
      return response;
    } catch (e) {
      log('❌ Cancel InterCity Order Error: $e');
      rethrow;
    }
  }

  /// Get intercity services
  static Future<Map<String, dynamic>> getServices() async {
    try {
      final response = await ApiService.get('/api/intercity-services');
      return response;
    } catch (e) {
      log('❌ Get InterCity Services Error: $e');
      rethrow;
    }
  }
}

