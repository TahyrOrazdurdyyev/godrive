import 'dart:convert';
import 'dart:developer';

import 'package:customer/model/driver_user_model.dart';
import 'package:customer/model/intercity_order_model.dart';
import 'package:customer/model/order_model.dart';
import 'package:customer/model/user_model.dart';
import 'package:customer/ui/chat_screen/chat_screen.dart';
import 'package:customer/ui/intercityOrders/intercity_payment_order_screen.dart';
import 'package:customer/ui/orders/payment_order_screen.dart';
import 'package:customer/utils/fire_store_utils.dart';
import 'package:customer/utils/user_api.dart';
import 'package:customer/utils/driver_api.dart';
import 'package:customer/utils/order_api.dart';
import 'package:customer/utils/intercity_order_api.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:get/get.dart';

Future<void> firebaseMessageBackgroundHandle(RemoteMessage message) async {
  log("BackGround Message :: ${message.messageId}");
  if (message.notification != null) {
    log(message.notification.toString());
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

    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      log("::::::::::::onMessage:::::::::::::::::");
      // if (message.notification != null) {
      //   log(message.notification.toString());
      //   display(message);
      // }
    });
    FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) async {
      log("::::::::::::onMessageOpenedApp:::::::::::::::::");
      if (message.notification != null) {
        log(message.notification.toString());
        if (message.data['type'] == "chat") {
          UserModel? customer;
          DriverUserModel? driver;
          
          try {
            final customerResponse = await UserApi.getProfile(message.data['customerId']);
            if (customerResponse['success'] == true && customerResponse['user'] != null) {
              customer = UserModel.fromJson(customerResponse['user']);
            }
            
            final driverResponse = await DriverApi.getProfile(message.data['driverId']);
            if (driverResponse['success'] == true && driverResponse['driver'] != null) {
              driver = DriverUserModel.fromJson(driverResponse['driver']);
            }
          } catch (e) {
            log('❌ Error loading user/driver for chat: $e');
          }

          if (customer != null && driver != null) {
            Get.to(ChatScreens(
              driverId: driver.id,
              customerId: customer.id,
              customerName: customer.fullName,
              customerProfileImage: customer.profilePic,
              driverName: driver.fullName,
              driverProfileImage: driver.profilePic,
              orderId: message.data['orderId'],
              token: driver.fcmToken,
            ));
          }
        } else if (message.data['type'] == "city_order_complete") {
          OrderModel? orderModel;
          
          try {
            final response = await OrderApi.getOrderById(message.data['orderId']);
            if (response['success'] == true && response['order'] != null) {
              orderModel = OrderModel.fromJson(response['order']);
            }
          } catch (e) {
            log('❌ Error loading order: $e');
          }
          
          if (orderModel != null) {
            Get.to(const PaymentOrderScreen(), arguments: {
              "orderModel": orderModel,
            });
          }
        } else if (message.data['type'] == "intercity_order_complete") {
          InterCityOrderModel? orderModel;
          
          try {
            final response = await InterCityOrderApi.getOrderById(message.data['orderId']);
            if (response['success'] == true && response['order'] != null) {
              orderModel = InterCityOrderModel.fromJson(response['order']);
            }
          } catch (e) {
            log('❌ Error loading intercity order: $e');
          }
          
          if (orderModel != null) {
            Get.to(const InterCityPaymentOrderScreen(), arguments: {
              "orderModel": orderModel,
            });
          }
        }
      }
    });
    await FirebaseMessaging.instance.subscribeToTopic("goRide_customer");

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
        'goRide-customer',
        description: 'Show QuickLAI Notification',
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
