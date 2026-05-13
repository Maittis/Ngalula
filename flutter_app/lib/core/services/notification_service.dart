import 'dart:async';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:timezone/timezone.dart' as tz;
import 'package:timezone/data/latest.dart' as tz;
import '../config/app_config.dart';
import '../repositories/notification_repository.dart';
import '../models/appointment_reminder.dart';
import '../models/notification_settings.dart';
import '../models/push_notification.dart';

class NotificationService {
  static final NotificationService _instance = NotificationService._internal();
  factory NotificationService() => _instance;
  NotificationService._internal();

  final FlutterLocalNotificationsPlugin _localNotifications = FlutterLocalNotificationsPlugin();
  final NotificationRepository _notificationRepository = NotificationRepository();
  
  NotificationSettings _settings = NotificationSettings.defaultSettings();
  StreamController<PushNotification>? _pushNotificationStream;
  
  // Getters
  NotificationSettings get settings => _settings;
  Stream<PushNotification> get pushNotifications => _pushNotificationStream?.stream ?? const Stream.empty();

  // Initialize notification service
  static Future<void> initialize() async {
    final service = NotificationService();
    await service._initializeNotifications();
    await service._initializeFirebaseMessaging();
    await service._loadSettings();
  }

  Future<void> _initializeNotifications() async {
    // Initialize timezone
    tz.initializeTimeZones();
    
    // Initialize local notifications
    const AndroidInitializationSettings initializationSettingsAndroid =
        AndroidInitializationSettings('@mipmap/ic_launcher');
    
    const DarwinInitializationSettings initializationSettingsIOS =
        DarwinInitializationSettings(
          requestAlertPermission: true,
          requestBadgePermission: true,
          requestSoundPermission: true,
        );
    
    const InitializationSettings initializationSettings = InitializationSettings(
      android: initializationSettingsAndroid,
      iOS: initializationSettingsIOS,
    );
    
    await _localNotifications.initialize(
      initializationSettings,
      onDidReceiveNotificationResponse: _onNotificationTapped,
      onDidReceiveBackgroundNotificationResponse: _onNotificationTapped,
    );
    
    // Request permissions
    final bool? result = await _localNotifications
        .resolvePlatformSpecificImplementation<
            IOSFlutterLocalNotificationsPlugin>()
        ?.requestPermissions(
          alert: true,
          badge: true,
          sound: true,
        );
    
    if (result != null && result) {
      print('iOS notification permissions granted');
    }
    
    final AndroidFlutterLocalNotificationsPlugin? androidImplementation =
        _localNotifications.resolvePlatformSpecificImplementation<
            AndroidFlutterLocalNotificationsPlugin>();
    
    final bool? grantedNotificationPermission =
        await androidImplementation?.requestNotificationsPermission();
    
    if (grantedNotificationPermission != null && grantedNotificationPermission) {
      print('Android notification permissions granted');
    }
  }

  Future<void> _initializeFirebaseMessaging() async {
    // Request permission
    final settings = await FirebaseMessaging.instance.requestPermission(
      alert: true,
      announcement: false,
      badge: true,
      carPlay: false,
      criticalAlert: false,
      provisional: false,
      sound: true,
    );

    if (settings.authorizationStatus == AuthorizationStatus.authorized) {
      print('User granted permission');
    } else if (settings.authorizationStatus == AuthorizationStatus.provisional) {
      print('User granted provisional permission');
    } else {
      print('User declined or has not accepted permission');
    }

    // Get FCM token
    final fcmToken = await FirebaseMessaging.instance.getToken();
    print('FCM Token: $fcmToken');
    
    // Register FCM token with backend
    await _notificationRepository.registerDeviceToken(fcmToken!);

    // Listen to FCM messages
    FirebaseMessaging.onMessage.listen(_handleForegroundMessage);
    
    FirebaseMessaging.onMessageOpenedApp.listen(_handleMessageOpenedApp);
    
    FirebaseMessaging.onBackgroundMessage(_firebaseMessagingBackgroundHandler);
  }

  Future<void> _loadSettings() async {
    _settings = await _notificationRepository.getNotificationSettings();
  }

  // Handle foreground FCM messages
  Future<void> _handleForegroundMessage(RemoteMessage message) async {
    final notification = PushNotification.fromRemoteMessage(message);
    
    // Show local notification
    await _showLocalNotification(notification);
    
    // Add to stream
    _pushNotificationStream?.add(notification);
  }

  // Handle message opened app
  Future<void> _handleMessageOpenedApp(RemoteMessage message) async {
    final notification = PushNotification.fromRemoteMessage(message);
    await _handleNotificationAction(notification);
  }

  // Background message handler
  static Future<void> _firebaseMessagingBackgroundHandler(RemoteMessage message) async {
    print('Handling a background message: ${message.messageId}');
  }

  // Handle notification tap
  static void _onNotificationTapped(NotificationResponse notificationResponse) {
    final service = NotificationService();
    service._handleNotificationTap(notificationResponse);
  }

  Future<void> _handleNotificationTap(NotificationResponse response) async {
    // Handle notification tap based on payload
    final payload = response.payload;
    if (payload != null) {
      // Parse payload and navigate accordingly
      await _handleNotificationAction(PushNotification.fromJson(payload));
    }
  }

  Future<void> _handleNotificationAction(PushNotification notification) async {
    switch (notification.type) {
      case 'appointment_reminder':
        // Navigate to appointment details
        await _navigateToAppointmentDetails(notification.data['booking_id']);
        break;
      case 'payment_confirmation':
        // Navigate to payment history
        await _navigateToPaymentHistory();
        break;
      case 'reward_earned':
        // Navigate to rewards screen
        await _navigateToRewardsScreen();
        break;
      case 'welcome_to_spa':
        // Navigate to check-in screen
        await _navigateToCheckInScreen(notification.data['booking_id']);
        break;
      case 'promotion':
        // Navigate to promotion details
        await _navigateToPromotionDetails(notification.data['promotion_id']);
        break;
      default:
        // Navigate to home
        await _navigateToHome();
    }
  }

  // Schedule appointment reminders
  Future<void> scheduleAppointmentReminders(AppointmentReminder reminder) async {
    if (!_settings.appointmentReminders) return;
    
    // Schedule 24-hour reminder
    if (reminder.reminder24h) {
      await _scheduleReminder(
        id: reminder.bookingId.hashCode + 1000,
        title: 'Appointment Tomorrow',
        body: 'Your appointment at ${reminder.spaName} is tomorrow at ${reminder.appointmentTime}',
        scheduledTime: reminder.appointmentDateTime.subtract(const Duration(hours: 24)),
        payload: {
          'type': 'appointment_reminder',
          'booking_id': reminder.bookingId,
        },
      );
    }
    
    // Schedule 2-hour reminder
    if (reminder.reminder2h) {
      await _scheduleReminder(
        id: reminder.bookingId.hashCode + 2000,
        title: 'Appointment in 2 Hours',
        body: 'Your appointment at ${reminder.spaName} starts in 2 hours',
        scheduledTime: reminder.appointmentDateTime.subtract(const Duration(hours: 2)),
        payload: {
          'type': 'appointment_reminder',
          'booking_id': reminder.bookingId,
        },
      );
    }
    
    // Schedule 30-minute reminder
    if (reminder.reminder30m) {
      await _scheduleReminder(
        id: reminder.bookingId.hashCode + 3000,
        title: 'Appointment in 30 Minutes',
        body: 'Your appointment at ${reminder.spaName} starts in 30 minutes',
        scheduledTime: reminder.appointmentDateTime.subtract(const Duration(minutes: 30)),
        payload: {
          'type': 'appointment_reminder',
          'booking_id': reminder.bookingId,
        },
      );
    }
    
    // Schedule check-in reminder
    if (reminder.checkInReminder) {
      await _scheduleReminder(
        id: reminder.bookingId.hashCode + 4000,
        title: 'Time to Check In',
        body: 'Your appointment at ${reminder.spaName} starts soon. Please check in at the reception.',
        scheduledTime: reminder.appointmentDateTime.subtract(const Duration(minutes: 10)),
        payload: {
          'type': 'check_in_reminder',
          'booking_id': reminder.bookingId,
        },
      );
    }
  }

  // Schedule a single reminder
  Future<void> _scheduleReminder({
    required int id,
    required String title,
    required String body,
    required DateTime scheduledTime,
    required Map<String, dynamic> payload,
  }) async {
    if (scheduledTime.isBefore(DateTime.now())) return;
    
    const AndroidNotificationDetails androidDetails = AndroidNotificationDetails(
      'appointment_reminders',
      'Appointment Reminders',
      channelDescription: 'Notifications for upcoming appointments',
      importance: Importance.high,
      priority: Priority.high,
      showWhen: true,
      icon: '@mipmap/ic_launcher',
      largeIcon: DrawableResourceAndroidBitmap('@mipmap/ic_launcher'),
      styleInformation: BigTextStyleInformation(
        body,
        htmlFormatBigText: true,
        htmlFormatTitle: true,
      ),
      enableLights: true,
      ledColor: const Color(0xFF6366F1),
      enableVibration: true,
      vibrationPattern: [0, 1000, 500, 1000],
      playSound: true,
      sound: RawResourceAndroidNotificationSound('notification_sound'),
    );
    
    const DarwinNotificationDetails iOSDetails = DarwinNotificationDetails(
      presentAlert: true,
      presentBadge: true,
      presentSound: true,
      sound: 'notification_sound.aiff',
      badgeNumber: 1,
    );
    
    const NotificationDetails platformDetails = NotificationDetails(
      android: androidDetails,
      iOS: iOSDetails,
    );
    
    await _localNotifications.zonedSchedule(
      id,
      title,
      body,
      tz.TZDateTime.from(scheduledTime, tz.local),
      platformDetails,
      androidScheduleMode: AndroidScheduleMode.exactAllowWhileIdle,
      uiLocalNotificationDateInterpretation:
          UILocalNotificationDateInterpretation.absoluteTime,
      payload: _encodePayload(payload),
    );
  }

  // Show local notification
  Future<void> _showLocalNotification(PushNotification notification) async {
    const AndroidNotificationDetails androidDetails = AndroidNotificationDetails(
      'general_notifications',
      'General Notifications',
      channelDescription: 'General app notifications',
      importance: Importance.high,
      priority: Priority.high,
      showWhen: true,
      icon: '@mipmap/ic_launcher',
      largeIcon: notification.imageUrl != null
          ? FilePathAndroidBitmap(notification.imageUrl!)
          : null,
      styleInformation: BigTextStyleInformation(
        notification.body,
        htmlFormatBigText: true,
        htmlFormatTitle: true,
      ),
      enableLights: true,
      ledColor: const Color(0xFF6366F1),
      enableVibration: true,
      vibrationPattern: [0, 1000, 500, 1000],
      playSound: true,
      sound: _getNotificationSound(notification.type),
    );
    
    const DarwinNotificationDetails iOSDetails = DarwinNotificationDetails(
      presentAlert: true,
      presentBadge: true,
      presentSound: true,
      sound: _getNotificationSound(notification.type),
      badgeNumber: 1,
    );
    
    const NotificationDetails platformDetails = NotificationDetails(
      android: androidDetails,
      iOS: iOSDetails,
    );
    
    await _localNotifications.show(
      notification.hashCode,
      notification.title,
      notification.body,
      platformDetails,
      payload: _encodePayload(notification.toJson()),
    );
  }

  // Cancel notification
  Future<void> cancelNotification(int id) async {
    await _localNotifications.cancel(id);
  }

  // Cancel all notifications
  Future<void> cancelAllNotifications() async {
    await _localNotifications.cancelAll();
  }

  // Get pending notifications
  Future<List<PendingNotificationRequest>> getPendingNotifications() async {
    return await _localNotifications.pendingNotificationRequests();
  }

  // Update notification settings
  Future<void> updateNotificationSettings(NotificationSettings settings) async {
    _settings = settings;
    await _notificationRepository.saveNotificationSettings(settings);
  }

  // Send test notification
  Future<void> sendTestNotification() async {
    const AndroidNotificationDetails androidDetails = AndroidNotificationDetails(
      'test_notifications',
      'Test Notifications',
      channelDescription: 'Test notifications',
      importance: Importance.high,
      priority: Priority.high,
      showWhen: true,
      icon: '@mipmap/ic_launcher',
      enableLights: true,
      ledColor: const Color(0xFF6366F1),
      enableVibration: true,
      vibrationPattern: [0, 1000, 500, 1000],
      playSound: true,
    );
    
    const DarwinNotificationDetails iOSDetails = DarwinNotificationDetails(
      presentAlert: true,
      presentBadge: true,
      presentSound: true,
      badgeNumber: 1,
    );
    
    const NotificationDetails platformDetails = NotificationDetails(
      android: androidDetails,
      iOS: iOSDetails,
    );
    
    await _localNotifications.show(
      0,
      'Test Notification',
      'This is a test notification from Ngalula Wellness Center',
      platformDetails,
    );
  }

  // Helper methods
  String _encodePayload(Map<String, dynamic> payload) {
    return payload.entries
        .map((e) => '${e.key}=${e.value}')
        .join('&');
  }

  Map<String, dynamic> _decodePayload(String payload) {
    final Map<String, dynamic> result = {};
    final pairs = payload.split('&');
    for (final pair in pairs) {
      final keyValue = pair.split('=');
      if (keyValue.length == 2) {
        result[keyValue[0]] = keyValue[1];
      }
    }
    return result;
  }

  String _getNotificationSound(String? type) {
    switch (type) {
      case 'appointment_reminder':
        return 'appointment_reminder.wav';
      case 'payment_confirmation':
        return 'payment_confirmation.wav';
      case 'reward_earned':
        return 'reward_earned.wav';
      case 'welcome_to_spa':
        return 'welcome_sound.wav';
      default:
        return 'notification_sound.wav';
    }
  }

  // Navigation helpers
  Future<void> _navigateToAppointmentDetails(String bookingId) async {
    // Navigate to appointment details screen
    print('Navigate to appointment details: $bookingId');
  }

  Future<void> _navigateToPaymentHistory() async {
    // Navigate to payment history screen
    print('Navigate to payment history');
  }

  Future<void> _navigateToRewardsScreen() async {
    // Navigate to rewards screen
    print('Navigate to rewards screen');
  }

  Future<void> _navigateToCheckInScreen(String bookingId) async {
    // Navigate to check-in screen
    print('Navigate to check-in screen: $bookingId');
  }

  Future<void> _navigateToPromotionDetails(String promotionId) async {
    // Navigate to promotion details screen
    print('Navigate to promotion details: $promotionId');
  }

  Future<void> _navigateToHome() async {
    // Navigate to home screen
    print('Navigate to home');
  }

  // Notification permission status
  Future<bool> areNotificationsEnabled() async {
    if (Platform.isIOS) {
      final settings = await FirebaseMessaging.instance.getNotificationSettings();
      return settings.authorizationStatus == AuthorizationStatus.authorized;
    } else if (Platform.isAndroid) {
      final androidImplementation = _localNotifications.resolvePlatformSpecificImplementation<
          AndroidFlutterLocalNotificationsPlugin>();
      return androidImplementation?.areNotificationsEnabled() ?? false;
    }
    return false;
  }

  // Request notification permissions
  Future<bool> requestNotificationPermissions() async {
    if (Platform.isIOS) {
      final settings = await FirebaseMessaging.instance.requestPermission();
      return settings.authorizationStatus == AuthorizationStatus.authorized;
    } else if (Platform.isAndroid) {
      final androidImplementation = _localNotifications.resolvePlatformSpecificImplementation<
          AndroidFlutterLocalNotificationsPlugin>();
      return await androidImplementation?.requestNotificationsPermission() ?? false;
    }
    return false;
  }

  // Open notification settings
  Future<void> openNotificationSettings() async {
    if (Platform.isAndroid) {
      await _localNotifications.resolvePlatformSpecificImplementation<
          AndroidFlutterLocalNotificationsPlugin>()
          ?.createNotificationChannelGroup(NotificationChannelGroup(
        'appointment_reminders',
        'Appointment Reminders',
      ));
    }
  }

  // Dispose
  void dispose() {
    _pushNotificationStream?.close();
  }
}

// Supporting models
class NotificationSettings {
  final bool appointmentReminders;
  final bool promotions;
  final bool rewards;
  final bool news;
  final bool sound;
  final bool vibration;
  final bool badge;
  final bool quietHours;
  final TimeOfDay quietHoursStart;
  final TimeOfDay quietHoursEnd;
  
  const NotificationSettings({
    required this.appointmentReminders,
    required this.promotions,
    required this.rewards,
    required this.news,
    required this.sound,
    required this.vibration,
    required this.badge,
    required this.quietHours,
    required this.quietHoursStart,
    required this.quietHoursEnd,
  });
  
  static NotificationSettings defaultSettings() {
    return const NotificationSettings(
      appointmentReminders: true,
      promotions: true,
      rewards: true,
      news: false,
      sound: true,
      vibration: true,
      badge: true,
      quietHours: false,
      quietHoursStart: TimeOfDay(hour: 22, minute: 0),
      quietHoursEnd: TimeOfDay(hour: 8, minute: 0),
    );
  }
  
  NotificationSettings copyWith({
    bool? appointmentReminders,
    bool? promotions,
    bool? rewards,
    bool? news,
    bool? sound,
    bool? vibration,
    bool? badge,
    bool? quietHours,
    TimeOfDay? quietHoursStart,
    TimeOfDay? quietHoursEnd,
  }) {
    return NotificationSettings(
      appointmentReminders: appointmentReminders ?? this.appointmentReminders,
      promotions: promotions ?? this.promotions,
      rewards: rewards ?? this.rewards,
      news: news ?? this.news,
      sound: sound ?? this.sound,
      vibration: vibration ?? this.vibration,
      badge: badge ?? this.badge,
      quietHours: quietHours ?? this.quietHours,
      quietHoursStart: quietHoursStart ?? this.quietHoursStart,
      quietHoursEnd: quietHoursEnd ?? this.quietHoursEnd,
    );
  }
}
