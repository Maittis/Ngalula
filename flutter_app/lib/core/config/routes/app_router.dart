import 'package:auto_route/auto_route.dart';
import 'package:flutter/material.dart';

import '../../presentation/screens/auth/login_screen.dart';
import '../../presentation/screens/auth/register_screen.dart';
import '../../presentation/screens/auth/forgot_password_screen.dart';
import '../../presentation/screens/auth/otp_screen.dart';
import '../../presentation/screens/auth/biometric_screen.dart';
import '../../presentation/screens/onboarding/onboarding_screen.dart';
import '../../presentation/screens/home/home_screen.dart';
import '../../presentation/screens/services/services_screen.dart';
import '../../presentation/screens/services/service_detail_screen.dart';
import '../../presentation/screens/booking/booking_screen.dart';
import '../../presentation/screens/booking/booking_confirmation_screen.dart';
import '../../presentation/screens/booking/booking_history_screen.dart';
import '../../presentation/screens/therapists/therapists_screen.dart';
import '../../presentation/screens/therapists/therapist_detail_screen.dart';
import '../../presentation/screens/profile/profile_screen.dart';
import '../../presentation/screens/profile/edit_profile_screen.dart';
import '../../presentation/screens/profile/settings_screen.dart';
import '../../presentation/screens/payments/payment_screen.dart';
import '../../presentation/screens/payments/payment_history_screen.dart';
import '../../presentation/screens/payments/add_payment_method_screen.dart';
import '../../presentation/screens/notifications/notifications_screen.dart';
import '../../presentation/screens/inventory/inventory_screen.dart';
import '../../presentation/screens/inventory/product_detail_screen.dart';
import '../../presentation/screens/inventory/barcode_scan_screen.dart';
import '../../presentation/screens/chat/chat_screen.dart';
import '../../presentation/screens/chat/chat_list_screen.dart';
import '../../presentation/screens/reports/reports_screen.dart';
import '../../presentation/screens/reports/analytics_screen.dart';
import '../../presentation/screens/support/support_screen.dart';
import '../../presentation/screens/support/faq_screen.dart';
import '../../presentation/screens/support/contact_screen.dart';
import '../../presentation/screens/splash/splash_screen.dart';
import '../../presentation/screens/error/error_screen.dart';
import '../../presentation/screens/webview/webview_screen.dart';

part 'app_router.gr.dart';

@AutoRouterConfig(replaceInRouteName: 'Screen,Route')
class AppRouter extends $AppRouter {
  @override
  List<AutoRoute> get routes => [
    // Splash & Onboarding
    AutoRoute(page: SplashRoute.page, initial: true),
    AutoRoute(page: OnboardingRoute.page),

    // Authentication
    AutoRoute(page: LoginRoute.page),
    AutoRoute(page: RegisterRoute.page),
    AutoRoute(page: ForgotPasswordRoute.page),
    AutoRoute(page: OtpRoute.page),
    AutoRoute(page: BiometricRoute.page),

    // Main Navigation
    AutoRoute(page: HomeRoute.page, children: [
      AutoRoute(page: ServicesRoute.page),
      AutoRoute(page: ServiceDetailRoute.page),
      AutoRoute(page: TherapistsRoute.page),
      AutoRoute(page: TherapistDetailRoute.page),
      AutoRoute(page: BookingRoute.page),
      AutoRoute(page: BookingConfirmationRoute.page),
      AutoRoute(page: BookingHistoryRoute.page),
      AutoRoute(page: InventoryRoute.page),
      AutoRoute(page: ProductDetailRoute.page),
      AutoRoute(page: BarcodeScanRoute.page),
      AutoRoute(page: ReportsRoute.page),
      AutoRoute(page: AnalyticsRoute.page),
      AutoRoute(page: ChatListRoute.page),
      AutoRoute(page: ChatRoute.page),
      AutoRoute(page: NotificationsRoute.page),
      AutoRoute(page: ProfileRoute.page),
      AutoRoute(page: EditProfileRoute.page),
      AutoRoute(page: SettingsRoute.page),
      AutoRoute(page: PaymentRoute.page),
      AutoRoute(page: PaymentHistoryRoute.page),
      AutoRoute(page: AddPaymentMethodRoute.page),
      AutoRoute(page: SupportRoute.page),
      AutoRoute(page: FaqRoute.page),
      AutoRoute(page: ContactRoute.page),
    ]),

    // Error & WebView
    AutoRoute(page: ErrorRoute.page),
    AutoRoute(page: WebviewRoute.page),
  ];
}

// Route names for navigation
class RouteNames {
  static const String splash = '/splash';
  static const String onboarding = '/onboarding';
  static const String login = '/login';
  static const String register = '/register';
  static const String forgotPassword = '/forgot-password';
  static const String otp = '/otp';
  static const String biometric = '/biometric';
  static const String home = '/home';
  static const String services = '/services';
  static const String serviceDetail = '/services/:id';
  static const String therapists = '/therapists';
  static const String therapistDetail = '/therapists/:id';
  static const String booking = '/booking';
  static const String bookingConfirmation = '/booking/confirmation';
  static const String bookingHistory = '/booking/history';
  static const String inventory = '/inventory';
  static const String productDetail = '/inventory/:id';
  static const String barcodeScan = '/inventory/scan';
  static const String reports = '/reports';
  static const String analytics = '/reports/analytics';
  static const String chatList = '/chat';
  static const String chat = '/chat/:id';
  static const String notifications = '/notifications';
  static const String profile = '/profile';
  static const String editProfile = '/profile/edit';
  static const String settings = '/settings';
  static const String payment = '/payment';
  static const String paymentHistory = '/payment/history';
  static const String addPaymentMethod = '/payment/method/add';
  static const String support = '/support';
  static const String faq = '/support/faq';
  static const String contact = '/support/contact';
  static const String error = '/error';
  static const String webview = '/webview';
}

// Navigation helper class
class NavigationHelper {
  static final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();

  static BuildContext? get context => navigatorKey.currentContext;

  static void push(String route, {Object? arguments}) {
    navigatorKey.currentState?.pushNamed(route, arguments: arguments);
  }

  static void pushReplacement(String route, {Object? arguments}) {
    navigatorKey.currentState?.pushReplacementNamed(route, arguments: arguments);
  }

  static void pushAndClearStack(String route, {Object? arguments}) {
    navigatorKey.currentState?.pushNamedAndRemoveUntil(
      route,
      (route) => false,
      arguments: arguments,
    );
  }

  static void pop<T>([T? result]) {
    navigatorKey.currentState?.pop<T>(result);
  }

  static void popUntil(String route) {
    navigatorKey.currentState?.popUntil(ModalRoute.withName(route));
  }

  static bool canPop() {
    return navigatorKey.currentState?.canPop() ?? false;
  }

  static void popAll() {
    navigatorKey.currentState?.popUntil((route) => route.isFirst);
  }
}

// Route guards for authentication
class AuthGuard extends AutoRouteGuard {
  @override
  void onNavigation(NavigationResolver resolver, StackRouter router) {
    // Check if user is authenticated
    // If not, redirect to login
    // If authenticated, continue to the route
    resolver.next();
  }
}

// Route guards for onboarding
class OnboardingGuard extends AutoRouteGuard {
  @override
  void onNavigation(NavigationResolver resolver, StackRouter router) {
    // Check if user has completed onboarding
    // If not, redirect to onboarding
    // If completed, continue to the route
    resolver.next();
  }
}

// Route guards for biometric authentication
class BiometricGuard extends AutoRouteGuard {
  @override
  void onNavigation(NavigationResolver resolver, StackRouter router) {
    // Check if biometric authentication is required
    // If required, redirect to biometric screen
    // If not required or already authenticated, continue to the route
    resolver.next();
  }
}

// Route transition utilities
class RouteTransitions {
  static const Duration defaultDuration = Duration(milliseconds: 300);
  static const Duration fastDuration = Duration(milliseconds: 150);
  static const Duration slowDuration = Duration(milliseconds: 500);

  static Widget slideTransition(
    BuildContext context,
    Animation<double> animation,
    Widget child,
  ) {
    return SlideTransition(
      position: Tween<Offset>(
        begin: const Offset(1.0, 0.0),
        end: Offset.zero,
      ).animate(CurvedAnimation(
        parent: animation,
        curve: Curves.easeInOut,
      )),
      child: child,
    );
  }

  static Widget fadeTransition(
    BuildContext context,
    Animation<double> animation,
    Widget child,
  ) {
    return FadeTransition(
      opacity: animation,
      child: child,
    );
  }

  static Widget scaleTransition(
    BuildContext context,
    Animation<double> animation,
    Widget child,
  ) {
    return ScaleTransition(
      scale: animation,
      child: child,
    );
  }

  static Widget rotationTransition(
    BuildContext context,
    Animation<double> animation,
    Widget child,
  ) {
    return RotationTransition(
      turns: animation,
      child: child,
    );
  }
}

// Route utilities
class RouteUtils {
  static String getServiceDetailRoute(String serviceId) {
    return '${RouteNames.serviceDetail.replaceAll(':id', serviceId)}';
  }

  static String getTherapistDetailRoute(String therapistId) {
    return '${RouteNames.therapistDetail.replaceAll(':id', therapistId)}';
  }

  static String getProductDetailRoute(String productId) {
    return '${RouteNames.productDetail.replaceAll(':id', productId)}';
  }

  static String getChatRoute(String chatId) {
    return '${RouteNames.chat.replaceAll(':id', chatId)}';
  }

  static Map<String, String> extractRouteParams(String route) {
    final uri = Uri.parse(route);
    return uri.queryParameters;
  }

  static String? extractRouteParam(String route, String param) {
    final params = extractRouteParams(route);
    return params[param];
  }

  static bool isRouteActive(String route) {
    final context = NavigationHelper.context;
    if (context == null) return false;
    
    final routeState = context.router.routeData;
    return routeState.path == route;
  }

  static String getCurrentRoute() {
    final context = NavigationHelper.context;
    if (context == null) return '';
    
    final routeState = context.router.routeData;
    return routeState.path;
  }

  static bool canGoBack() {
    return NavigationHelper.canPop();
  }

  static void goBack() {
    NavigationHelper.pop();
  }

  static void goBackTo(String route) {
    NavigationHelper.popUntil(route);
  }

  static void navigateToHome() {
    NavigationHelper.pushAndClearStack(RouteNames.home);
  }

  static void navigateToLogin() {
    NavigationHelper.pushReplacement(RouteNames.login);
  }

  static void navigateToRegister() {
    NavigationHelper.push(RouteNames.register);
  }

  static void navigateToForgotPassword() {
    NavigationHelper.push(RouteNames.forgotPassword);
  }

  static void navigateToOtp(String email) {
    NavigationHelper.push(RouteNames.otp, arguments: email);
  }

  static void navigateToBiometric() {
    NavigationHelper.push(RouteNames.biometric);
  }

  static void navigateToOnboarding() {
    NavigationHelper.pushReplacement(RouteNames.onboarding);
  }

  static void navigateToServices() {
    NavigationHelper.push(RouteNames.services);
  }

  static void navigateToServiceDetail(String serviceId) {
    NavigationHelper.push(getServiceDetailRoute(serviceId));
  }

  static void navigateToTherapists() {
    NavigationHelper.push(RouteNames.therapists);
  }

  static void navigateToTherapistDetail(String therapistId) {
    NavigationHelper.push(getTherapistDetailRoute(therapistId));
  }

  static void navigateToBooking() {
    NavigationHelper.push(RouteNames.booking);
  }

  static void navigateToBookingConfirmation() {
    NavigationHelper.push(RouteNames.bookingConfirmation);
  }

  static void navigateToBookingHistory() {
    NavigationHelper.push(RouteNames.bookingHistory);
  }

  static void navigateToInventory() {
    NavigationHelper.push(RouteNames.inventory);
  }

  static void navigateToProductDetail(String productId) {
    NavigationHelper.push(getProductDetailRoute(productId));
  }

  static void navigateToBarcodeScan() {
    NavigationHelper.push(RouteNames.barcodeScan);
  }

  static void navigateToReports() {
    NavigationHelper.push(RouteNames.reports);
  }

  static void navigateToAnalytics() {
    NavigationHelper.push(RouteNames.analytics);
  }

  static void navigateToChatList() {
    NavigationHelper.push(RouteNames.chatList);
  }

  static void navigateToChat(String chatId) {
    NavigationHelper.push(getChatRoute(chatId));
  }

  static void navigateToNotifications() {
    NavigationHelper.push(RouteNames.notifications);
  }

  static void navigateToProfile() {
    NavigationHelper.push(RouteNames.profile);
  }

  static void navigateToEditProfile() {
    NavigationHelper.push(RouteNames.editProfile);
  }

  static void navigateToSettings() {
    NavigationHelper.push(RouteNames.settings);
  }

  static void navigateToPayment() {
    NavigationHelper.push(RouteNames.payment);
  }

  static void navigateToPaymentHistory() {
    NavigationHelper.push(RouteNames.paymentHistory);
  }

  static void navigateToAddPaymentMethod() {
    NavigationHelper.push(RouteNames.addPaymentMethod);
  }

  static void navigateToSupport() {
    NavigationHelper.push(RouteNames.support);
  }

  static void navigateToFaq() {
    NavigationHelper.push(RouteNames.faq);
  }

  static void navigateToContact() {
    NavigationHelper.push(RouteNames.contact);
  }

  static void navigateToError(String error) {
    NavigationHelper.push(RouteNames.error, arguments: error);
  }

  static void navigateToWebview(String url, String title) {
    NavigationHelper.push(RouteNames.webview, arguments: {'url': url, 'title': title});
  }
}
