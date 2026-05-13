import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import '../../../core/config/app_config.dart';
import '../../../core/config/routes/app_utils.dart';
import '../../../core/services/user_journey_service.dart';
import '../../../core/services/notification_service.dart';
import '../../../data/models/booking.dart';
import '../../../data/models/service.dart';
import '../../../data/models/therapist.dart';
import '../../../data/models/time_slot.dart';
import '../../../presentation/blocs/booking/booking_bloc.dart';
import '../../../presentation/widgets/common/loading_widget.dart';
import '../../../presentation/widgets/booking/booking_details_widget.dart';
import '../../../presentation/widgets/booking/countdown_widget.dart';

class BookingConfirmationScreen extends StatefulWidget {
  final Booking booking;
  
  const BookingConfirmationScreen({
    super.key,
    required this.booking,
  });

  @override
  State<BookingConfirmationScreen> createState() => _BookingConfirmationScreenState();
}

class _BookingConfirmationScreenState extends State<BookingConfirmationScreen> {
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _updateJourneyStep();
    _scheduleReminder();
  }

  Future<void> _updateJourneyStep() async {
    // Update user journey
    await UserJourneyService.updateJourneyStep(
      step: UserJourneyStep.receiveReminder,
      data: {
        'booking_id': widget.booking.id,
        'booking_confirmed': true,
        'service_id': widget.booking.serviceId,
        'therapist_id': widget.booking.therapistId,
      },
    );
  }

  Future<void> _scheduleReminder() async {
    // Schedule notification reminder
    await NotificationService.scheduleAppointmentReminder(
      bookingId: widget.booking.id,
      serviceName: widget.booking.serviceName,
      appointmentDate: widget.booking.date,
      appointmentTime: widget.booking.time,
    );
  }

  void _navigateToHome() {
    AppUtils.navigateToHome(context);
  }

  void _navigateToRewards() {
    AppUtils.navigateToRewards(context);
  }

  void _shareBooking() {
    // Share booking details
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Booking details shared!'),
        backgroundColor: Colors.green,
      ),
    );
  }

  void _addToCalendar() {
    // Add to calendar
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Added to calendar!'),
        backgroundColor: Colors.blue,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Booking Confirmed'),
        backgroundColor: Colors.white,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => Navigator.pop(context),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.share),
            onPressed: _shareBooking,
          ),
          IconButton(
            icon: const Icon(Icons.calendar_today),
            onPressed: _addToCalendar,
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Success Message
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [
                    Colors.green[400]!,
                    Colors.green[600]!,
                  ],
                ),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Column(
                children: [
                  Icon(
                    Icons.check_circle,
                    size: 64,
                    color: Colors.white,
                  ),
                  const SizedBox(height: 16),
                  const Text(
                    'Booking Confirmed!',
                    style: TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                    ),
                  ),
                  const SizedBox(height: 8),
                  const Text(
                    'Your appointment has been successfully booked',
                    style: TextStyle(
                      fontSize: 16,
                      color: Colors.white,
                    ),
                  ),
                ],
              ),
            ),
            
            const SizedBox(height: 24),
            
            // Booking Details
            BookingDetailsWidget(
              booking: widget.booking,
            ),
            
            const SizedBox(height: 24),
            
            // Countdown Timer
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.1),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Time Until Appointment',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: Colors.black87,
                    ),
                  ),
                  const SizedBox(height: 16),
                  CountdownWidget(
                    targetDate: widget.booking.date,
                    targetTime: widget.booking.time,
                  ),
                ],
              ),
            ),
            
            const SizedBox(height: 24),
            
            // Action Buttons
            Row(
              children: [
                Expanded(
                  child: ElevatedButton(
                    onPressed: _navigateToHome,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.blue[600],
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                    child: const Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(Icons.home),
                        const SizedBox(width: 8),
                        Text('Back to Home'),
                      ],
                    ),
                  ),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: ElevatedButton(
                    onPressed: _navigateToRewards,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.orange[600],
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                    child: const Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(Icons.emoji_events),
                        const SizedBox(width: 8),
                        Text('View Rewards'),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
