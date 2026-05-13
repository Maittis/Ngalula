import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:get_storage/get_storage.dart';

import 'app/app.dart';
import 'core/config/app_config.dart';
import 'core/config/firebase_config.dart';
import 'core/config/routes/app_router.dart';
import 'core/config/themes/app_theme.dart';
import 'core/services/notification_service.dart';
import 'core/services/websocket_service.dart';
import 'data/repositories/auth_repository.dart';
import 'data/repositories/user_repository.dart';
import 'data/repositories/booking_repository.dart';
import 'data/repositories/service_repository.dart';
import 'data/repositories/payment_repository.dart';
import 'data/repositories/notification_repository.dart';
import 'data/repositories/inventory_repository.dart';
import 'presentation/blocs/auth/auth_bloc.dart';
import 'presentation/blocs/booking/booking_bloc.dart';
import 'presentation/blocs/service/service_bloc.dart';
import 'presentation/blocs/payment/payment_bloc.dart';
import 'presentation/blocs/notification/notification_bloc.dart';
import 'presentation/blocs/inventory/inventory_bloc.dart';
import 'presentation/blocs/theme/theme_bloc.dart';
import 'presentation/blocs/locale/locale_bloc.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Load environment variables
  await dotenv.load(fileName: '.env');
  
  // Initialize Firebase
  await Firebase.initializeApp(
    options: FirebaseConfig.currentPlatform,
  );
  
  // Initialize GetStorage
  await GetStorage.init();
  
  // Initialize services
  await NotificationService.initialize();
  await WebSocketService.initialize();
  
  // Get FCM token
  final fcmToken = await FirebaseMessaging.instance.getToken();
  if (fcmToken != null) {
    debugPrint('FCM Token: $fcmToken');
    // Save FCM token to local storage or send to server
    await GetStorage().write('fcm_token', fcmToken);
  }
  
  // Initialize repositories
  final authRepository = AuthRepository();
  final userRepository = UserRepository();
  final bookingRepository = BookingRepository();
  final serviceRepository = ServiceRepository();
  final paymentRepository = PaymentRepository();
  final notificationRepository = NotificationRepository();
  final inventoryRepository = InventoryRepository();
  
  runApp(
    MultiRepositoryProvider(
      providers: [
        RepositoryProvider(create: (_) => authRepository),
        RepositoryProvider(create: (_) => userRepository),
        RepositoryProvider(create: (_) => bookingRepository),
        RepositoryProvider(create: (_) => serviceRepository),
        RepositoryProvider(create: (_) => paymentRepository),
        RepositoryProvider(create: (_) => notificationRepository),
        RepositoryProvider(create: (_) => inventoryRepository),
      ],
      child: MultiBlocProvider(
        providers: [
          BlocProvider(create: (_) => AuthBloc(authRepository)),
          BlocProvider(create: (_) => BookingBloc(bookingRepository)),
          BlocProvider(create: (_) => ServiceBloc(serviceRepository)),
          BlocProvider(create: (_) => PaymentBloc(paymentRepository)),
          BlocProvider(create: (_) => NotificationBloc(notificationRepository)),
          BlocProvider(create: (_) => InventoryBloc(inventoryRepository)),
          BlocProvider(create: (_) => ThemeBloc()),
          BlocProvider(create: (_) => LocaleBloc()),
        ],
        child: const NgalulaApp(),
      ),
    ),
  );
}

class NgalulaApp extends StatelessWidget {
  const NgalulaApp({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<ThemeBloc, ThemeState>(
      builder: (context, themeState) {
        return BlocBuilder<LocaleBloc, LocaleState>(
          builder: (context, localeState) {
            return MaterialApp.router(
              title: AppConfig.appName,
              debugShowCheckedModeBanner: AppConfig.isDebugMode,
              theme: AppTheme.lightTheme,
              darkTheme: AppTheme.darkTheme,
              themeMode: themeState.themeMode,
              locale: localeState.locale,
              localizationsDelegates: const [
                AppLocalizations.delegate,
                GlobalMaterialLocalizations.delegate,
                GlobalWidgetsLocalizations.delegate,
                GlobalCupertinoLocalizations.delegate,
              ],
              supportedLocales: const [
                Locale('en', 'US'),
                Locale('es', 'ES'),
                Locale('fr', 'FR'),
                Locale('de', 'DE'),
                Locale('it', 'IT'),
                Locale('pt', 'BR'),
              ],
              routerConfig: AppRouter.router,
              builder: (context, child) {
                return MediaQuery(
                  data: MediaQuery.of(context).copyWith(
                    textScaleFactor: 1.0, // Prevent text scaling
                  ),
                  child: child!,
                );
              },
            );
          },
        );
      },
    );
  }
}
