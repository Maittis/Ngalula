import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../../presentation/screens/onboarding/onboarding_screen.dart';
import '../../presentation/screens/onboarding/login_screen.dart';
import '../../presentation/screens/onboarding/signup_screen.dart';
import '../../presentation/screens/onboarding/role_picker_screen.dart';
import '../../presentation/screens/home/home_screen.dart';

// Route names for navigation
class RouteNames {
  static const String splash = '/splash';
  static const String onboarding = '/onboarding';
  static const String login = '/login';
  static const String loginCustomer = '/login/customer';
  static const String loginTherapist = '/login/therapist';
  static const String loginAdmin = '/login/admin';
  static const String register = '/register';
  static const String registerCustomer = '/register/customer';
  static const String registerTherapist = '/register/therapist';
  static const String registerAdmin = '/register/admin';
  static const String forgotPassword = '/forgot-password';
  static const String home = '/home';
  static const String services = '/services';
  static const String therapists = '/therapists';
  static const String booking = '/booking';
  static const String profile = '/profile';
  static const String settings = '/settings';
  static const String notifications = '/notifications';
}

/// AppRouter provides GoRouter configuration for the app
class AppRouter {
  static final GoRouter router = GoRouter(
    initialLocation: '/onboarding',
    navigatorKey: RouteUtils.navigatorKey,
    routes: [
      GoRoute(
        path: '/onboarding',
        name: 'onboarding',
        builder: (context, state) => const OnboardingScreen(),
      ),
      // Role picker (landing page for choosing role)
      GoRoute(
        path: '/login',
        name: 'login',
        builder: (context, state) => const RolePickerScreen(),
      ),
      // Role-specific login routes
      GoRoute(
        path: '/login/customer',
        name: 'loginCustomer',
        builder: (context, state) => const LoginScreen(userType: 'customer'),
      ),
      GoRoute(
        path: '/login/therapist',
        name: 'loginTherapist',
        builder: (context, state) => const LoginScreen(userType: 'therapist'),
      ),
      GoRoute(
        path: '/login/admin',
        name: 'loginAdmin',
        builder: (context, state) => const LoginScreen(userType: 'admin'),
      ),
      // Role picker for registration
      GoRoute(
        path: '/register',
        name: 'register',
        builder: (context, state) => const RolePickerScreen(isRegister: true),
      ),
      // Role-specific register routes
      GoRoute(
        path: '/register/customer',
        name: 'registerCustomer',
        builder: (context, state) => const SignupScreen(userType: 'customer'),
      ),
      GoRoute(
        path: '/register/therapist',
        name: 'registerTherapist',
        builder: (context, state) => const SignupScreen(userType: 'therapist'),
      ),
      GoRoute(
        path: '/register/admin',
        name: 'registerAdmin',
        builder: (context, state) => const SignupScreen(userType: 'admin'),
      ),
      GoRoute(
        path: '/home',
        name: 'home',
        builder: (context, state) => const HomeScreen(),
      ),
      // Forgot password
      GoRoute(
        path: '/forgot-password',
        name: 'forgotPassword',
        builder: (context, state) => Scaffold(
          appBar: AppBar(title: const Text('Forgot Password')),
          body: const Center(child: Text('Forgot Password - Coming Soon')),
        ),
      ),
      // Catch-all for 404
      GoRoute(
        path: '/',
        builder: (context, state) => const OnboardingScreen(),
      ),
    ],
    errorBuilder: (context, state) => Scaffold(
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.error_outline, size: 64, color: Colors.red),
            const SizedBox(height: 16),
            Text('Page not found: ${state.error?.message ?? state.uri.toString()}'),
            const SizedBox(height: 16),
            ElevatedButton(
              onPressed: () => context.go('/onboarding'),
              child: const Text('Go Home'),
            ),
          ],
        ),
      ),
    ),
  );
}

/// RouteUtils provides helper methods for navigation
class RouteUtils {
  static final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();

  static BuildContext? get context => navigatorKey.currentContext;

  /// Navigate to the home screen
  static void navigateToHome() {
    if (context != null) {
      context!.go(RouteNames.home);
    }
  }

  /// Navigate to the login role picker
  static void navigateToLogin({String? userType}) {
    if (context != null) {
      if (userType != null) {
        context!.go('/login/$userType');
      } else {
        context!.go(RouteNames.login);
      }
    }
  }

  /// Navigate to the register role picker
  static void navigateToRegister({String? userType}) {
    if (context != null) {
      if (userType != null) {
        context!.go('/register/$userType');
      } else {
        context!.go(RouteNames.register);
      }
    }
  }

  /// Navigate to the onboarding screen
  static void navigateToOnboarding() {
    if (context != null) {
      context!.go(RouteNames.onboarding);
    }
  }

  /// Navigate back
  static void goBack() {
    if (context != null) {
      context!.pop();
    }
  }

  /// Navigate to a specific route
  static void navigateTo(String route) {
    if (context != null) {
      context!.go(route);
    }
  }

  /// Navigate to services
  static void navigateToServices() {
    if (context != null) {
      context!.go(RouteNames.services);
    }
  }

  /// Navigate to therapists
  static void navigateToTherapists() {
    if (context != null) {
      context!.go(RouteNames.therapists);
    }
  }

  /// Navigate to booking
  static void navigateToBooking() {
    if (context != null) {
      context!.go(RouteNames.booking);
    }
  }

  /// Navigate to profile
  static void navigateToProfile() {
    if (context != null) {
      context!.go(RouteNames.profile);
    }
  }

  /// Navigate to settings
  static void navigateToSettings() {
    if (context != null) {
      context!.go(RouteNames.settings);
    }
  }

  /// Navigate to notifications
  static void navigateToNotifications() {
    if (context != null) {
      context!.go(RouteNames.notifications);
    }
  }

  /// Navigate to forgot password
  static void navigateToForgotPassword() {
    if (context != null) {
      context!.go(RouteNames.forgotPassword);
    }
  }
}
