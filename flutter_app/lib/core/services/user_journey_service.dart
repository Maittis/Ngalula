import 'package:flutter/material.dart';
import 'package:get_storage/get_storage.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import '../config/app_config.dart';
import '../repositories/booking_repository.dart';
import '../repositories/service_repository.dart';
import '../repositories/therapist_repository.dart';
import '../repositories/payment_repository.dart';
import '../repositories/reward_repository.dart';
import '../repositories/notification_repository.dart';

class UserJourneyService {
  static final UserJourneyService _instance = UserJourneyService._internal();
  factory UserJourneyService() => _instance;
  UserJourneyService._internal();

  final BookingRepository _bookingRepository = BookingRepository();
  final ServiceRepository _serviceRepository = ServiceRepository();
  final TherapistRepository _therapistRepository = TherapistRepository();
  final PaymentRepository _paymentRepository = PaymentRepository();
  final RewardRepository _rewardRepository = RewardRepository();
  final NotificationRepository _notificationRepository = NotificationRepository();

  // User Journey State
  UserJourneyState _currentState = UserJourneyState.exploring;
  UserJourneyState get currentState => _currentState;

  // Journey Progress
  final Map<String, dynamic> _journeyData = {};
  Map<String, dynamic> get journeyData => Map.from(_journeyData);

  // Initialize user journey
  Future<void> initializeJourney() async {
    // Check if user is onboarding
    final isFirstLaunch = GetStorage().read('is_first_launch') ?? true;
    
    if (isFirstLaunch) {
      _currentState = UserJourneyState.onboarding;
    } else {
      _currentState = UserJourneyState.exploring;
    }
    
    // Load saved journey state
    await _loadJourneyState();
    
    // Setup notifications
    await _setupNotifications();
  }

  // Step 1: Explore Spa Services
  Future<List<ServiceCategory>> exploreSpaServices() async {
    _currentState = UserJourneyState.exploring;
    _journeyData['last_action'] = 'explore_services';
    await _saveJourneyState();
    
    try {
      final categories = await _serviceRepository.getCategories();
      final featuredServices = await _serviceRepository.getFeaturedServices();
      
      // Track user exploration
      await _trackUserAction('explore_services', {
        'categories_count': categories.length,
        'featured_services_count': featuredServices.length,
      });
      
      return categories;
    } catch (e) {
      throw UserJourneyException('Failed to explore services: $e');
    }
  }

  // Step 2: Select Service
  Future<ServiceDetails> selectService(String serviceId) async {
    _currentState = UserJourneyState.selecting_service;
    _journeyData['selected_service_id'] = serviceId;
    _journeyData['last_action'] = 'select_service';
    await _saveJourneyState();
    
    try {
      final serviceDetails = await _serviceRepository.getServiceDetails(serviceId);
      _journeyData['selected_service'] = serviceDetails.toJson();
      
      // Track service selection
      await _trackUserAction('select_service', {
        'service_id': serviceId,
        'service_name': serviceDetails.name,
        'service_category': serviceDetails.category,
        'service_price': serviceDetails.price,
      });
      
      return serviceDetails;
    } catch (e) {
      throw UserJourneyException('Failed to select service: $e');
    }
  }

  // Step 3: Choose Therapist
  Future<List<Therapist>> getAvailableTherapists(String serviceId, DateTime date) async {
    _currentState = UserJourneyState.choosing_therapist;
    _journeyData['selected_date'] = date.toIso8601String();
    _journeyData['last_action'] = 'choose_therapist';
    await _saveJourneyState();
    
    try {
      final therapists = await _therapistRepository.getAvailableTherapists(serviceId, date);
      
      // Track therapist selection start
      await _trackUserAction('choose_therapist', {
        'service_id': serviceId,
        'selected_date': date.toIso8601String(),
        'available_therapists_count': therapists.length,
      });
      
      return therapists;
    } catch (e) {
      throw UserJourneyException('Failed to get available therapists: $e');
    }
  }

  // Step 4: Select Therapist
  Future<TherapistDetails> selectTherapist(String therapistId) async {
    _currentState = UserJourneyState.selecting_therapist;
    _journeyData['selected_therapist_id'] = therapistId;
    _journeyData['last_action'] = 'select_therapist';
    await _saveJourneyState();
    
    try {
      final therapistDetails = await _therapistRepository.getTherapistDetails(therapistId);
      _journeyData['selected_therapist'] = therapistDetails.toJson();
      
      // Track therapist selection
      await _trackUserAction('select_therapist', {
        'therapist_id': therapistId,
        'therapist_name': therapistDetails.name,
        'therapist_rating': therapistDetails.averageRating,
      });
      
      return therapistDetails;
    } catch (e) {
      throw UserJourneyException('Failed to select therapist: $e');
    }
  }

  // Step 5: Pick Time
  Future<List<TimeSlot>> getAvailableTimeSlots(String therapistId, DateTime date) async {
    _currentState = UserJourneyState.picking_time;
    _journeyData['last_action'] = 'pick_time';
    await _saveJourneyState();
    
    try {
      final timeSlots = await _bookingRepository.getAvailableTimeSlots(therapistId, date);
      
      // Track time slot selection start
      await _trackUserAction('pick_time', {
        'therapist_id': therapistId,
        'selected_date': date.toIso8601String(),
        'available_slots_count': timeSlots.length,
      });
      
      return timeSlots;
    } catch (e) {
      throw UserJourneyException('Failed to get available time slots: $e');
    }
  }

  // Step 6: Select Time Slot
  Future<void> selectTimeSlot(TimeSlot timeSlot) async {
    _currentState = UserJourneyState.selecting_time;
    _journeyData['selected_time_slot'] = timeSlot.toJson();
    _journeyData['last_action'] = 'select_time';
    await _saveJourneyState();
    
    // Track time slot selection
    await _trackUserAction('select_time', {
      'time_slot': timeSlot.toJson(),
      'start_time': timeSlot.startTime.toIso8601String(),
      'end_time': timeSlot.endTime.toIso8601String(),
    });
  }

  // Step 7: Pay
  Future<BookingConfirmation> processPayment(PaymentDetails paymentDetails) async {
    _currentState = UserJourneyState.processing_payment;
    _journeyData['payment_details'] = paymentDetails.toJson();
    _journeyData['last_action'] = 'process_payment';
    await _saveJourneyState();
    
    try {
      // Create booking
      final booking = await _bookingRepository.createBooking(
        serviceId: _journeyData['selected_service_id'],
        therapistId: _journeyData['selected_therapist_id'],
        timeSlot: TimeSlot.fromJson(_journeyData['selected_time_slot']),
        paymentDetails: paymentDetails,
      );
      
      // Process payment
      final paymentResult = await _paymentRepository.processPayment(
        bookingId: booking.id,
        amount: booking.totalAmount,
        paymentMethod: paymentDetails.method,
      );
      
      if (paymentResult.success) {
        _currentState = UserJourneyState.payment_successful;
        _journeyData['booking_id'] = booking.id;
        _journeyData['payment_id'] = paymentResult.paymentId;
        
        // Track successful payment
        await _trackUserAction('payment_successful', {
          'booking_id': booking.id,
          'payment_id': paymentResult.paymentId,
          'amount': booking.totalAmount,
          'payment_method': paymentDetails.method,
        });
        
        // Schedule reminders
        await _scheduleReminders(booking);
        
        // Update rewards
        await _updateRewards(booking);
        
        return BookingConfirmation(
          booking: booking,
          payment: paymentResult,
          estimatedArrivalTime: DateTime.now().add(Duration(minutes: 30)),
        );
      } else {
        throw UserJourneyException('Payment failed: ${paymentResult.errorMessage}');
      }
    } catch (e) {
      throw UserJourneyException('Failed to process payment: $e');
    }
  }

  // Step 8: Receive Reminders
  Future<void> _scheduleReminders(Booking booking) async {
    _currentState = UserJourneyState.waiting_for_appointment;
    _journeyData['last_action'] = 'schedule_reminders';
    await _saveJourneyState();
    
    try {
      // Schedule multiple reminders
      await _notificationRepository.scheduleReminder(
        bookingId: booking.id,
        type: NotificationType.appointment_reminder_24h,
        scheduledTime: booking.appointmentTime.subtract(Duration(hours: 24)),
      );
      
      await _notificationRepository.scheduleReminder(
        bookingId: booking.id,
        type: NotificationType.appointment_reminder_2h,
        scheduledTime: booking.appointmentTime.subtract(Duration(hours: 2)),
      );
      
      await _notificationRepository.scheduleReminder(
        bookingId: booking.id,
        type: NotificationType.appointment_reminder_30m,
        scheduledTime: booking.appointmentTime.subtract(Duration(minutes: 30)),
      );
      
      // Track reminder scheduling
      await _trackUserAction('schedule_reminders', {
        'booking_id': booking.id,
        'appointment_time': booking.appointmentTime.toIso8601String(),
      });
    } catch (e) {
      // Don't throw error for reminders, but log it
      print('Failed to schedule reminders: $e');
    }
  }

  // Step 9: Visit Spa (Check-in)
  Future<CheckInResult> checkIn(String bookingId) async {
    _currentState = UserJourneyState.checking_in;
    _journeyData['last_action'] = 'check_in';
    await _saveJourneyState();
    
    try {
      final checkInResult = await _bookingRepository.checkIn(bookingId);
      
      // Track check-in
      await _trackUserAction('check_in', {
        'booking_id': bookingId,
        'check_in_time': DateTime.now().toIso8601String(),
      });
      
      // Send welcome notification
      await _notificationRepository.sendNotification(
        type: NotificationType.welcome_to_spa,
        title: 'Welcome to Ngalula Wellness Center!',
        body: 'Your appointment is confirmed. Please check in at the reception.',
      );
      
      return checkInResult;
    } catch (e) {
      throw UserJourneyException('Failed to check in: $e');
    }
  }

  // Step 10: Earn Rewards
  Future<RewardUpdate> earnRewards(String bookingId) async {
    _currentState = UserJourneyState.earning_rewards;
    _journeyData['last_action'] = 'earn_rewards';
    await _saveJourneyState();
    
    try {
      final rewardUpdate = await _rewardRepository.processBookingReward(bookingId);
      
      // Track rewards earned
      await _trackUserAction('earn_rewards', {
        'booking_id': bookingId,
        'points_earned': rewardUpdate.pointsEarned,
        'new_level': rewardUpdate.newLevel,
        'rewards_unlocked': rewardUpdate.rewardsUnlocked.length,
      });
      
      // Send reward notification
      await _notificationRepository.sendNotification(
        type: NotificationType.reward_earned,
        title: 'Congratulations! You earned rewards!',
        body: 'You earned ${rewardUpdate.pointsEarned} points from your recent visit.',
      );
      
      return rewardUpdate;
    } catch (e) {
      throw UserJourneyException('Failed to earn rewards: $e');
    }
  }

  // Step 11: Rebook
  Future<RebookingOptions> getRebookingOptions(String bookingId) async {
    _currentState = UserJourneyState.rebooking;
    _journeyData['last_action'] = 'rebook';
    await _saveJourneyState();
    
    try {
      final rebookingOptions = await _bookingRepository.getRebookingOptions(bookingId);
      
      // Track rebooking interest
      await _trackUserAction('rebook', {
        'original_booking_id': bookingId,
        'rebooking_options_count': rebookingOptions.options.length,
      });
      
      return rebookingOptions;
    } catch (e) {
      throw UserJourneyException('Failed to get rebooking options: $e');
    }
  }

  // Quick Rebook (one-tap rebooking)
  Future<BookingConfirmation> quickRebook(String bookingId) async {
    _currentState = UserJourneyState.rebooking;
    _journeyData['last_action'] = 'quick_rebook';
    await _saveJourneyState();
    
    try {
      final rebookingResult = await _bookingRepository.quickRebook(bookingId);
      
      // Track quick rebooking
      await _trackUserAction('quick_rebook', {
        'original_booking_id': bookingId,
        'new_booking_id': rebookingResult.booking.id,
      });
      
      // Schedule reminders for new booking
      await _scheduleReminders(rebookingResult.booking);
      
      return rebookingResult;
    } catch (e) {
      throw UserJourneyException('Failed to quick rebook: $e');
    }
  }

  // Journey Helper Methods
  Future<void> _loadJourneyState() async {
    final savedState = GetStorage().read('user_journey_state');
    if (savedState != null) {
      _journeyData.addAll(savedState['data'] ?? {});
      _currentState = UserJourneyState.values.firstWhere(
        (state) => state.toString() == savedState['current_state'],
        orElse: () => UserJourneyState.exploring,
      );
    }
  }

  Future<void> _saveJourneyState() async {
    await GetStorage().write('user_journey_state', {
      'current_state': _currentState.toString(),
      'data': _journeyData,
      'updated_at': DateTime.now().toIso8601String(),
    });
  }

  Future<void> _setupNotifications() async {
    // Request notification permissions
    await FirebaseMessaging.instance.requestPermission();
    
    // Get FCM token
    final fcmToken = await FirebaseMessaging.instance.getToken();
    if (fcmToken != null) {
      await _notificationRepository.registerDeviceToken(fcmToken);
    }
  }

  Future<void> _trackUserAction(String action, Map<String, dynamic> data) async {
    // Track user journey analytics
    await _notificationRepository.trackEvent(
      eventName: 'user_journey_action',
      parameters: {
        'action': action,
        'current_state': _currentState.toString(),
        'timestamp': DateTime.now().toIso8601String(),
        ...data,
      },
    );
  }

  // Journey Progress Tracking
  double get journeyProgress {
    final totalSteps = UserJourneyState.values.length;
    final currentStepIndex = UserJourneyState.values.indexOf(_currentState);
    return (currentStepIndex + 1) / totalSteps;
  }

  // Journey Recommendations
  Future<List<JourneyRecommendation>> getRecommendations() async {
    final recommendations = <JourneyRecommendation>[];
    
    // Based on current state, provide relevant recommendations
    switch (_currentState) {
      case UserJourneyState.exploring:
        recommendations.add(JourneyRecommendation(
          type: RecommendationType.popular_service,
          title: 'Try our Signature Massage',
          description: 'Our most popular service with 98% satisfaction rate',
          action: 'explore_service',
          data: {'service_id': 'signature-massage'},
        ));
        break;
        
      case UserJourneyState.selecting_service:
        recommendations.add(JourneyRecommendation(
          type: RecommendationType.therapist_suggestion,
          title: 'Recommended Therapist',
          description: 'Based on your preferences, we recommend Sarah Johnson',
          action: 'select_therapist',
          data: {'therapist_id': 'sarah-johnson'},
        ));
        break;
        
      case UserJourneyState.waiting_for_appointment:
        recommendations.add(JourneyRecommendation(
          type: RecommendationType.preparation_tip,
          title: 'Prepare for Your Visit',
          description: 'Arrive 10 minutes early and wear comfortable clothing',
          action: 'view_tips',
          data: {'tip_type': 'preparation'},
        ));
        break;
        
      case UserJourneyState.payment_successful:
        recommendations.add(JourneyRecommendation(
          type: RecommendationType.add_on_service,
          title: 'Add Aromatherapy',
          description: 'Enhance your experience with essential oils',
          action: 'add_service',
          data: {'add_on_id': 'aromatherapy'},
        ));
        break;
        
      default:
        break;
    }
    
    return recommendations;
  }

  // Reset Journey
  Future<void> resetJourney() async {
    _currentState = UserJourneyState.exploring;
    _journeyData.clear();
    await _saveJourneyState();
  }

  // Get Journey Summary
  JourneySummary getJourneySummary() {
    return JourneySummary(
      currentState: _currentState,
      progress: journeyProgress,
      lastAction: _journeyData['last_action'],
      selectedService: _journeyData['selected_service'],
      selectedTherapist: _journeyData['selected_therapist'],
      selectedTimeSlot: _journeyData['selected_time_slot'],
      bookingId: _journeyData['booking_id'],
    );
  }
}

// User Journey States
enum UserJourneyState {
  onboarding,
  exploring,
  selecting_service,
  choosing_therapist,
  selecting_therapist,
  picking_time,
  selecting_time,
  processing_payment,
  payment_successful,
  waiting_for_appointment,
  checking_in,
  in_appointment,
  earning_rewards,
  rebooking,
  completed,
}

// User Journey Exception
class UserJourneyException implements Exception {
  final String message;
  UserJourneyException(this.message);
  
  @override
  String toString() => 'UserJourneyException: $message';
}

// Supporting Models
class ServiceCategory {
  final String id;
  final String name;
  final String description;
  final String imageUrl;
  final int serviceCount;
  
  ServiceCategory({
    required this.id,
    required this.name,
    required this.description,
    required this.imageUrl,
    required this.serviceCount,
  });
}

class ServiceDetails {
  final String id;
  final String name;
  final String description;
  final String category;
  final double price;
  final int duration;
  final List<String> benefits;
  final String imageUrl;
  final double rating;
  final int reviewCount;
  
  ServiceDetails({
    required this.id,
    required this.name,
    required this.description,
    required this.category,
    required this.price,
    required this.duration,
    required this.benefits,
    required this.imageUrl,
    required this.rating,
    required this.reviewCount,
  });
  
  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'description': description,
      'category': category,
      'price': price,
      'duration': duration,
      'benefits': benefits,
      'imageUrl': imageUrl,
      'rating': rating,
      'reviewCount': reviewCount,
    };
  }
}

class TherapistDetails {
  final String id;
  final String name;
  final String bio;
  final String imageUrl;
  final double averageRating;
  final int totalReviews;
  final List<String> specializations;
  final int yearsExperience;
  final String education;
  
  TherapistDetails({
    required this.id,
    required this.name,
    required this.bio,
    required this.imageUrl,
    required this.averageRating,
    required this.totalReviews,
    required this.specializations,
    required this.yearsExperience,
    required this.education,
  });
  
  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'bio': bio,
      'imageUrl': imageUrl,
      'averageRating': averageRating,
      'totalReviews': totalReviews,
      'specializations': specializations,
      'yearsExperience': yearsExperience,
      'education': education,
    };
  }
}

class TimeSlot {
  final DateTime startTime;
  final DateTime endTime;
  final bool isAvailable;
  final String? bookingId;
  
  TimeSlot({
    required this.startTime,
    required this.endTime,
    required this.isAvailable,
    this.bookingId,
  });
  
  Map<String, dynamic> toJson() {
    return {
      'startTime': startTime.toIso8601String(),
      'endTime': endTime.toIso8601String(),
      'isAvailable': isAvailable,
      'bookingId': bookingId,
    };
  }
  
  static TimeSlot fromJson(Map<String, dynamic> json) {
    return TimeSlot(
      startTime: DateTime.parse(json['startTime']),
      endTime: DateTime.parse(json['endTime']),
      isAvailable: json['isAvailable'],
      bookingId: json['bookingId'],
    );
  }
}

class PaymentDetails {
  final String method;
  final String? cardToken;
  final String? mobileMoneyNumber;
  final double amount;
  final String currency;
  
  PaymentDetails({
    required this.method,
    this.cardToken,
    this.mobileMoneyNumber,
    required this.amount,
    required this.currency,
  });
  
  Map<String, dynamic> toJson() {
    return {
      'method': method,
      'cardToken': cardToken,
      'mobileMoneyNumber': mobileMoneyNumber,
      'amount': amount,
      'currency': currency,
    };
  }
}

class BookingConfirmation {
  final Booking booking;
  final PaymentResult payment;
  final DateTime estimatedArrivalTime;
  
  BookingConfirmation({
    required this.booking,
    required this.payment,
    required this.estimatedArrivalTime,
  });
}

class Booking {
  final String id;
  final String serviceId;
  final String therapistId;
  final DateTime appointmentTime;
  final double totalAmount;
  final String status;
  
  Booking({
    required this.id,
    required this.serviceId,
    required this.therapistId,
    required this.appointmentTime,
    required this.totalAmount,
    required this.status,
  });
}

class PaymentResult {
  final bool success;
  final String? paymentId;
  final String? errorMessage;
  
  PaymentResult({
    required this.success,
    this.paymentId,
    this.errorMessage,
  });
}

class CheckInResult {
  final bool success;
  final String? roomNumber;
  final String? therapistName;
  final DateTime? estimatedStartTime;
  
  CheckInResult({
    required this.success,
    this.roomNumber,
    this.therapistName,
    this.estimatedStartTime,
  });
}

class RewardUpdate {
  final int pointsEarned;
  final String newLevel;
  final List<String> rewardsUnlocked;
  final int totalPoints;
  
  RewardUpdate({
    required this.pointsEarned,
    required this.newLevel,
    required this.rewardsUnlocked,
    required this.totalPoints,
  });
}

class RebookingOptions {
  final List<RebookingOption> options;
  final String originalServiceName;
  final String originalTherapistName;
  
  RebookingOptions({
    required this.options,
    required this.originalServiceName,
    required this.originalTherapistName,
  });
}

class RebookingOption {
  final DateTime suggestedDate;
  final List<TimeSlot> availableSlots;
  final bool isRecommended;
  
  RebookingOption({
    required this.suggestedDate,
    required this.availableSlots,
    required this.isRecommended,
  });
}

class JourneyRecommendation {
  final RecommendationType type;
  final String title;
  final String description;
  final String action;
  final Map<String, dynamic> data;
  
  JourneyRecommendation({
    required this.type,
    required this.title,
    required this.description,
    required this.action,
    required this.data,
  });
}

enum RecommendationType {
  popular_service,
  therapist_suggestion,
  time_slot_suggestion,
  add_on_service,
  preparation_tip,
  reward_opportunity,
}

class JourneySummary {
  final UserJourneyState currentState;
  final double progress;
  final String? lastAction;
  final Map<String, dynamic>? selectedService;
  final Map<String, dynamic>? selectedTherapist;
  final Map<String, dynamic>? selectedTimeSlot;
  final String? bookingId;
  
  JourneySummary({
    required this.currentState,
    required this.progress,
    this.lastAction,
    this.selectedService,
    this.selectedTherapist,
    this.selectedTimeSlot,
    this.bookingId,
  });
}

enum NotificationType {
  appointment_reminder_24h,
  appointment_reminder_2h,
  appointment_reminder_30m,
  welcome_to_spa,
  reward_earned,
  payment_confirmation,
  booking_confirmation,
  check_in_reminder,
}
