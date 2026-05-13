# Ngalula Spa Booking Journey

## Completed Flow

This document summarizes the Flutter booking journey implemented in the app.

### 1. Open App
- Entry point: flutter_app/lib/main.dart
- Home screen: flutter_app/lib/presentation/screens/home/home_screen.dart
- Quick action buttons: Book Now, Explore, Rebook, Rewards

### 2. Explore Spa
- Screen: flutter_app/lib/presentation/screens/services/service_exploration_screen.dart
- Uses ServiceCard and CategoryCard
- Includes search and category filters

### 3. Select Service
- Navigation helper: AppUtils.navigateToServiceDetail(context, serviceId)
- Service detail screen is referenced from the flow

### 4. Choose Therapist
- Screen: flutter_app/lib/presentation/screens/booking/therapist_selection_screen.dart
- Uses TherapistCard, SearchBarWidget, FilterChipWidget

### 5. Pick Time
- Screen: flutter_app/lib/presentation/screens/booking/time_slot_selection_screen.dart
- Uses calendar, time slot selection, and SelectedTimeDisplay

### 6. Pay
- Screens: flutter_app/lib/presentation/screens/payment/payment_screen.dart and flutter_app/lib/presentation/screens/booking/payment_flow_screen.dart
- Uses BookingSummaryCard and PaymentMethodCard

### 7. Receive Reminder
- Screen: flutter_app/lib/presentation/screens/booking/booking_confirmation_screen.dart
- Schedules reminders via NotificationService

### 8. Visit Spa
- Confirmation screen provides appointment details, countdown, and reminders

### 9. Earn Rewards
- Screen: flutter_app/lib/presentation/screens/rewards/rewards_screen.dart
- Uses RewardsSummaryWidget, PointsSummaryWidget, RewardCard, AchievementWidget

### 10. Rebook
- Screen: flutter_app/lib/presentation/screens/booking/rebooking_screen.dart
- Supports quick rebook and custom rebook options

## Supporting Files Added
- lib/core/config/routes/app_utils.dart
- lib/core/config/app_localizations.dart
- lib/core/config/themes/app_theme.dart

## Widget Stubs Added
- Loading, error, empty states
- Booking and payment widgets
- Reward and therapist widgets
- Filter and search UI components
