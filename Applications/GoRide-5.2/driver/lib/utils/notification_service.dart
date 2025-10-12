import 'dart:convert';
import 'dart:developer';

import 'package:driver/model/driver_user_model.dart';
import 'package:driver/model/intercity_order_model.dart';
import 'package:driver/model/order_model.dart';
import 'package:driver/model/user_model.dart';
import 'package:driver/ui/chat_screen/chat_screen.dart';
import 'package:driver/ui/home_screens/order_map_screen.dart';
import 'package:driver/ui/order_intercity_screen/complete_intecity_order_screen.dart';
import 'package:driver/ui/order_screen/complete_order_screen.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/order_api.dart';
import 'package:driver/utils/intercity_order_api.dart';
import 'package:driver/utils/customer_api.dart';
import 'package:driver/utils/driver_api.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:get/get.dart';

Future<void> firebaseMessageBackgroundHandle(RemoteMessage message) async {
  log("BackGround Message :: ${message.messageId}");
  if (message.notification != null) {
    log(message.data.toString());
    NotificationService().display(message);
  }
}

class NotificationService {
  FlutterLocalNotificationsPlugin flutterLocalNotificationsPlugin = FlutterLocalNotificationsPlugin();

  initInfo() async {
    await FirebaseMessaging.instance.setForegroundNotificationPresentationOptions(
      alert: true,
      badge: true,
      sound: true,
    );
    var request = await FirebaseMessaging.instance.requestPermission(
      alert: true,
      announcement: false,
      badge: true,
      carPlay: false,
      criticalAlert: false,
      provisional: false,
      sound: true,
    );

    if (request.authorizationStatus == AuthorizationStatus.authorized || request.authorizationStatus == AuthorizationStatus.provisional) {
      const AndroidInitializationSettings initializationSettingsAndroid = AndroidInitializationSettings('@mipmap/ic_launcher');
      var iosInitializationSettings = const DarwinInitializationSettings();
      final InitializationSettings initializationSettings = InitializationSettings(android: initializationSettingsAndroid, iOS: iosInitializationSettings);
      await flutterLocalNotificationsPlugin.initialize(initializationSettings, onDidReceiveNotificationResponse: (payload) {});
      setupInteractedMessage();
    }
  }

  Future<void> setupInteractedMessage() async {
    RemoteMessage? initialMessage = await FirebaseMessaging.instance.getInitialMessage();
    if (initialMessage != null) {
      FirebaseMessaging.onBackgroundMessage((message) => firebaseMessageBackgroundHandle(message));
    }

    // if (initialMessage != null) {
    //   log('Message also contained a notification: ${initialMessage.notification!.body}');
    // }

    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      log("::::::::::::onMessage:::::::::::::::::");
      // if (message.notification != null) {
      //   log(message.data.toString());
      //   display(message);
      // }
    });
    FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) async {
      log("::::::::::::onMessageOpenedApp:::::::::::::::::");
      if (message.notification != null) {
        log(message.data.toString());
        // display(message);
        if (message.data['type'] == "city_order") {
          Get.to(const OrderMapScreen(), arguments: {"orderModel": message.data['orderId']});
        } else if (message.data['type'] == "city_order_payment_complete") {
          // Get order from API
          OrderModel? orderModel;
          try {
            final response = await OrderApi.getOrderById(int.parse(message.data['orderId']));
            if (response['success'] == true && response['order'] != null) {
              orderModel = OrderModel.fromJson(response['order']);
            }
          } catch (e) {
            log('❌ Error getting order: $e');
          }
          Get.to(const CompleteOrderScreen(), arguments: {
            "orderModel": orderModel,
          });
        } else if (message.data['type'] == "intercity_order_payment_complete") {
          // Get intercity order from API
          InterCityOrderModel? orderModel;
          try {
            final response = await InterCityOrderApi.getById(message.data['orderId'].toString());
            if (response['success'] == true && response['order'] != null) {
              orderModel = InterCityOrderModel.fromJson(response['order']);
            }
          } catch (e) {
            log('❌ Error getting intercity order: $e');
          }
          Get.to(const CompleteIntercityOrderScreen(), arguments: {
            "orderModel": orderModel,
          });
        } else if (message.data['type'] == "chat") {
          // Get customer and driver from API
          UserModel? customer;
          DriverUserModel? driver;
          try {
            final customerResponse = await CustomerApi.getCustomerProfile(message.data['customerId']);
            if (customerResponse['success'] == true && customerResponse['customer'] != null) {
              customer = UserModel.fromJson(customerResponse['customer']);
            }
            
            final driverResponse = await DriverApi.getProfile(message.data['driverId']);
            if (driverResponse['success'] == true && driverResponse['driver'] != null) {
              driver = DriverUserModel.fromJson(driverResponse['driver']);
            }
          } catch (e) {
            log('❌ Error getting user data: $e');
          }

          Get.to(ChatScreens(
            driverId: driver!.id,
            customerId: customer!.id,
            customerName: customer.fullName,
            customerProfileImage: customer.profilePic,
            driverName: driver.fullName,
            driverProfileImage: driver.profilePic,
            orderId: message.data['orderId'],
            token: customer.fcmToken,
          ));
        }
      }
    });

    await FirebaseMessaging.instance.subscribeToTopic("goRide_driver");

  }

  static getToken() async {
    String? token = await FirebaseMessaging.instance.getToken();
    return token!;
  }

  void display(RemoteMessage message) async {
    log('Got a message whilst in the foreground!');
    log('Message data: ${message.notification!.body.toString()}');
    try {
      // final id = DateTime.now().millisecondsSinceEpoch ~/ 1000;

      AndroidNotificationChannel channel = const AndroidNotificationChannel(
        '0',
        'goRide-driver',
        description: 'Show GoRide Notification',
        importance: Importance.max,
      );
      AndroidNotificationDetails notificationDetails =
          AndroidNotificationDetails(channel.id, channel.name, channelDescription: 'your channel Description', importance: Importance.high, priority: Priority.high, ticker: 'ticker');
      const DarwinNotificationDetails darwinNotificationDetails = DarwinNotificationDetails(presentAlert: true, presentBadge: true, presentSound: true);
      NotificationDetails notificationDetailsBoth = NotificationDetails(android: notificationDetails, iOS: darwinNotificationDetails);
      await FlutterLocalNotificationsPlugin().show(
        0,
        message.notification!.title,
        message.notification!.body,
        notificationDetailsBoth,
        payload: jsonEncode(message.data),
      );
    } on Exception catch (e) {
      log(e.toString());
    }
  }
}
