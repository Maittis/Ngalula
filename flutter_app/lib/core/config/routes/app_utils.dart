import 'package:flutter/material.dart';
import '../../../data/models/service.dart';
import '../../../data/models/therapist.dart';
import '../../../data/models/time_slot.dart';
import 'app_router.dart';

/// AppUtils provides context-aware navigation helpers for the user journey flow.
/// These methods wrap RouteUtils to add specific parameters for booking screens.
class AppUtils {
  // Auth Navigation
  static void navigateToLogin(BuildContext context) {
    RouteUtils.navigateToLogin();
  }

  static void navigateToRegister(BuildContext context) {
    RouteUtils.navigateToRegister();
  }

  static void navigateToHome(BuildContext context) {
    RouteUtils.navigateToHome();
  }

  // Service Navigation
  static void navigateToServices(BuildContext context) {
    RouteUtils.navigateToServices();
  }

  static void navigateToServiceDetail(BuildContext context, String serviceId) {
    RouteUtils.navigateToServiceDetail(serviceId);
  }

  // Therapist Navigation
  static void navigateToTherapistSelection(
    BuildContext context, {
    required Service service,
  }) {
    // Store service context and navigate
    Navigator.pushNamed(
      context,
      '/therapists',
      arguments: {'service': service},
    );
  }

  static void navigateToTimeSlotSelection(
    BuildContext context, {
    required Service service,
    required Therapist therapist,
  }) {
    // Store booking context and navigate to time slot selection
    Navigator.pushNamed(
      context,
      '/booking/time-slot',
      arguments: {
        'service': service,
        'therapist': therapist,
      },
    );
  }

  // Payment Navigation
  static void navigateToPayment(BuildContext context) {
    Navigator.pushNamed(context, '/payment');
  }

  static void navigateToBookingConfirmation(BuildContext context) {
    Navigator.pushNamed(
      context,
      '/booking/confirmation',
      arguments: {'clearStack': true},
    );
  }

  static void navigateToAddPaymentMethod(BuildContext context) {
    Navigator.pushNamed(context, '/payment/method/add');
  }

  // Rewards & Rebook Navigation
  static void navigateToRewards(BuildContext context) {
    Navigator.pushNamed(context, '/rewards');
  }

  static void navigateToRebooking(BuildContext context, {String? bookingId}) {
    Navigator.pushNamed(
      context,
      '/rebook',
      arguments: {'booking_id': bookingId},
    );
  }

  static void navigateToBooking(BuildContext context) {
    Navigator.pushNamed(context, '/booking');
  }

  // Support & Settings Navigation
  static void navigateToSupport(BuildContext context) {
    RouteUtils.navigateToSupport();
  }

  static void navigateToProfile(BuildContext context) {
    RouteUtils.navigateToProfile();
  }

  static void navigateToSettings(BuildContext context) {
    RouteUtils.navigateToSettings();
  }

  // Notification Navigation
  static void navigateToNotifications(BuildContext context) {
    RouteUtils.navigateToNotifications();
  }

  // Booking History Navigation
  static void navigateToBookingHistory(BuildContext context) {
    RouteUtils.navigateToBookingHistory();
  }
}
