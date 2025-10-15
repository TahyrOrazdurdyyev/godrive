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

  /// Get customer intercity orders (alias for compatibility)
  static Future<Map<String, dynamic>> getCustomerIntercityOrders(String uid) async {
    try {
      // First get user by uid to get user_id
      final userResponse = await ApiService.get('/api/user/profile', queryParams: {'uid': uid});
      if (userResponse['success'] == true && userResponse['user'] != null) {
        final userId = userResponse['user']['id'];
        return await getCustomerOrders(userId);
      }
      return {'success': false, 'message': 'User not found'};
    } catch (e) {
      log('❌ Get Customer InterCity Orders Error: $e');
      rethrow;
    }
  }

  /// Create intercity order (full version for compatibility)
  static Future<Map<String, dynamic>> createIntercityOrder({
    required String userId,
    required String serviceId,
    required double sourceLat,
    required double sourceLng,
    required String sourceLocationName,
    required double destinationLat,
    required double destinationLng,
    required String destinationLocationName,
    required String distance,
    required String duration,
    required double offerRate,
    required String paymentType,
    String? otp,
    required String whenTime,
    required String whenDates,
    required String comments,
    int? zoneId,
    String? parcelImage,
    String? parcelWeight,
    String? parcelDimension,
    String? sourceCity,
    String? destinationCity,
    int? numberOfPassenger,
  }) async {
    try {
      // First get user by uid to get user_id
      final userResponse = await ApiService.get('/api/user/profile', queryParams: {'uid': userId});
      if (userResponse['success'] != true || userResponse['user'] == null) {
        return {'success': false, 'message': 'User not found'};
      }
      final userIdInt = userResponse['user']['id'];

      final body = {
        'user_id': userIdInt,
        'intercity_service_id': int.parse(serviceId),
        'source_city': sourceCity ?? '',
        'source_location_name': sourceLocationName,
        'destination_city': destinationCity ?? '',
        'destination_location_name': destinationLocationName,
        'source_lat': sourceLat,
        'source_lng': sourceLng,
        'destination_lat': destinationLat,
        'destination_lng': destinationLng,
        'distance': double.parse(distance),
        'offer_rate': offerRate,
        'payment_type': paymentType,
        'when_date': whenDates,
        'when_time': whenTime,
      };

      if (zoneId != null) body['zone_id'] = zoneId;
      if (parcelWeight != null) body['parcel_weight'] = parcelWeight;
      if (parcelDimension != null) body['parcel_dimension'] = parcelDimension;
      if (parcelImage != null) body['parcel_images'] = [parcelImage];
      if (numberOfPassenger != null) body['number_of_passenger'] = numberOfPassenger;

      final response = await ApiService.post('/api/intercity-orders', body: body);
      return response;
    } catch (e) {
      log('❌ Create InterCity Order Error: $e');
      rethrow;
    }
  }

  /// Update intercity order (alias for compatibility)
  static Future<Map<String, dynamic>> updateIntercityOrder({
    required String orderId,
    required Map<String, dynamic> data,
  }) async {
    return await updateOrder(orderId: orderId, data: data);
  }

  /// Cancel intercity order (alias for compatibility)
  static Future<Map<String, dynamic>> cancelIntercityOrder(String orderId) async {
    return await cancelOrder(orderId: orderId);
  }

  /// Get by ID (alias for compatibility)
  static Future<Map<String, dynamic>> getById(String orderId) async {
    return await getOrderById(orderId);
  }

  /// Get intercity order by ID (alias for compatibility)
  static Future<Map<String, dynamic>> getIntercityOrderById(String orderId) async {
    return await getOrderById(orderId);
  }
}

