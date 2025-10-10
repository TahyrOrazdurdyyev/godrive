import 'dart:async';
import 'dart:convert';
import 'dart:developer';
import 'package:web_socket_channel/web_socket_channel.dart';
import 'package:web_socket_channel/status.dart' as status;

class WebSocketService {
  static final WebSocketService _instance = WebSocketService._internal();
  factory WebSocketService() => _instance;
  WebSocketService._internal();

  WebSocketChannel? _channel;
  bool _isConnected = false;
  final Map<String, StreamController> _channelControllers = {};
  Timer? _pingTimer;

  static const String _host = '185.10.16.248';
  static const int _port = 80;
  static const String _appKey = 'goride-key';
  static const String _path = '/soketi/app/$_appKey';

  /// Initialize WebSocket connection to Soketi
  Future<void> connect() async {
    if (_isConnected) {
      log('✅ WebSocket already connected');
      return;
    }

    try {
      final uri = Uri(
        scheme: 'ws',
        host: _host,
        port: _port,
        path: _path,
        queryParameters: {
          'protocol': '7',
          'client': 'flutter',
          'version': '1.0.0',
        },
      );

      log('🔌 Connecting to: $uri');
      _channel = WebSocketChannel.connect(uri);

      _channel!.stream.listen(
        _onMessage,
        onError: _onError,
        onDone: _onDone,
        cancelOnError: false,
      );

      _isConnected = true;
      _startPing();
      log('✅ WebSocket connected successfully');
    } catch (e) {
      log('❌ WebSocket connection error: $e');
      rethrow;
    }
  }

  /// Subscribe to a channel
  Future<StreamController> subscribe(String channelName) async {
    if (!_isConnected) {
      await connect();
    }

    if (_channelControllers.containsKey(channelName)) {
      log('⚠️ Already subscribed to: $channelName');
      return _channelControllers[channelName]!;
    }

    final controller = StreamController.broadcast();
    _channelControllers[channelName] = controller;

    final subscribeMessage = jsonEncode({
      'event': 'pusher:subscribe',
      'data': {
        'channel': channelName,
      },
    });

    _channel!.sink.add(subscribeMessage);
    log('📡 Subscribed to channel: $channelName');

    return controller;
  }

  /// Unsubscribe from a channel
  void unsubscribe(String channelName) {
    if (!_channelControllers.containsKey(channelName)) {
      return;
    }

    final unsubscribeMessage = jsonEncode({
      'event': 'pusher:unsubscribe',
      'data': {
        'channel': channelName,
      },
    });

    _channel?.sink.add(unsubscribeMessage);
    _channelControllers[channelName]?.close();
    _channelControllers.remove(channelName);
    log('📡 Unsubscribed from channel: $channelName');
  }

  /// Disconnect from WebSocket
  void disconnect() {
    _pingTimer?.cancel();
    _channel?.sink.close(status.goingAway);
    _channelControllers.forEach((key, controller) {
      controller.close();
    });
    _channelControllers.clear();
    _isConnected = false;
    log('✅ WebSocket disconnected');
  }

  /// Handle incoming messages
  void _onMessage(dynamic message) {
    try {
      final data = jsonDecode(message);
      final event = data['event'] as String?;
      final channelName = data['channel'] as String?;

      log('📨 Received: $event on $channelName');

      if (event == 'pusher:connection_established') {
        log('✅ Connection established');
        return;
      }

      if (event == 'pusher_internal:subscription_succeeded') {
        log('✅ Subscription succeeded: $channelName');
        return;
      }

      if (event == 'pusher:pong') {
        // Ignore pong
        return;
      }

      // Broadcast event to subscribers
      if (channelName != null && _channelControllers.containsKey(channelName)) {
        _channelControllers[channelName]!.add(data);
      }
    } catch (e) {
      log('❌ Error parsing message: $e');
    }
  }

  /// Handle errors
  void _onError(dynamic error) {
    log('❌ WebSocket error: $error');
    _isConnected = false;
  }

  /// Handle connection close
  void _onDone() {
    log('⚠️ WebSocket connection closed');
    _isConnected = false;
    _pingTimer?.cancel();

    // Auto-reconnect after 5 seconds
    Future.delayed(const Duration(seconds: 5), () {
      if (!_isConnected) {
        log('🔄 Attempting to reconnect...');
        connect();
      }
    });
  }

  /// Send ping every 30 seconds to keep connection alive
  void _startPing() {
    _pingTimer = Timer.periodic(const Duration(seconds: 30), (timer) {
      if (_isConnected) {
        final pingMessage = jsonEncode({'event': 'pusher:ping', 'data': {}});
        _channel?.sink.add(pingMessage);
      }
    });
  }

  bool get isConnected => _isConnected;
}

