import 'package:flutter/material.dart';

class AppLocalizations {
  static const LocalizationsDelegate<AppLocalizations> delegate =
      _AppLocalizationsDelegate();

  static const Map<String, Map<String, String>> _localizedValues = {
    'en': {
      'app_title': 'Ngalula Spa',
      'explore_services': 'Explore Services',
      'select_service': 'Select Service',
      'choose_therapist': 'Choose Therapist',
      'pick_time': 'Pick Time',
      'pay': 'Pay',
      'confirm_booking': 'Confirm Booking',
      'receive_reminder': 'Receive Reminder',
      'visit_spa': 'Visit Spa',
      'earn_rewards': 'Earn Rewards',
      'rebook': 'Rebook',
      'loading': 'Loading...',
      'error': 'Error',
      'try_again': 'Try Again',
      'success': 'Success',
      'cancel': 'Cancel',
      'confirm': 'Confirm',
      'booking_confirmed': 'Booking Confirmed',
      'payment_successful': 'Payment Successful',
    },
  };

  String get appTitle => _localizedValues['en']?['app_title'] ?? '';
  String get exploreServices => _localizedValues['en']?['explore_services'] ?? '';
  String get selectService => _localizedValues['en']?['select_service'] ?? '';
  String get chooseTherapist => _localizedValues['en']?['choose_therapist'] ?? '';
  String get pickTime => _localizedValues['en']?['pick_time'] ?? '';
  String get pay => _localizedValues['en']?['pay'] ?? '';
  String get confirmBooking => _localizedValues['en']?['confirm_booking'] ?? '';
}

class _AppLocalizationsDelegate
    extends LocalizationsDelegate<AppLocalizations> {
  const _AppLocalizationsDelegate();

  @override
  bool isSupported(Locale locale) {
    return ['en'].contains(locale.languageCode);
  }

  @override
  Future<AppLocalizations> load(Locale locale) {
    return Future.value(AppLocalizations());
  }

  @override
  bool shouldReload(_AppLocalizationsDelegate old) {
    return false;
  }
}
