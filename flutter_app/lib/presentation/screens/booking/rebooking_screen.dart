import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../../core/config/app_config.dart';
import '../../../core/services/user_journey_service.dart';
import '../../../core/config/routes/app_utils.dart';
import '../../../data/models/booking.dart';
import '../../../data/models/rebooking_option.dart';
import '../../../presentation/blocs/booking/booking_bloc.dart';
import '../../../presentation/widgets/common/loading_widget.dart';
import '../../../presentation/widgets/common/error_widget.dart';
import '../../../presentation/widgets/booking/rebooking_option_card.dart';
import '../../../presentation/widgets/booking/quick_rebooking_button.dart';
import '../../../presentation/widgets/common/empty_state_widget.dart';

class RebookingScreen extends StatefulWidget {
  final String bookingId;
  
  const RebookingScreen({
    super.key,
    required this.bookingId,
  });

  @override
  State<RebookingScreen> createState() => _RebookingScreenState();
}

class _RebookingScreenState extends State<RebookingScreen>
    with TickerProviderStateMixin {
  late UserJourneyService _journeyService;
  late TabController _tabController;
  
  Booking? _originalBooking;
  List<RebookingOption> _rebookingOptions = [];
  bool _isLoading = true;
  bool _isQuickRebooking = false;
  String? _error;

  @override
  void initState() {
    super.initState();
    _journeyService = UserJourneyService();
    _tabController = TabController(length: 2, vsync: this);
    _loadRebookingData();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadRebookingData() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      // Get rebooking options
      final rebookingOptions = await _journeyService.getRebookingOptions(widget.bookingId);
      _rebookingOptions = rebookingOptions.options;
      
      // Load original booking details
      context.read<BookingBloc>().add(LoadBookingDetails(widget.bookingId));
      
      setState(() {
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  Future<void> _quickRebook() async {
    setState(() {
      _isQuickRebooking = true;
    });

    try {
      // Perform quick rebooking
      final confirmation = await _journeyService.quickRebook(widget.bookingId);
      
      // Show success message
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Booking rebooked successfully!'),
          backgroundColor: Theme.of(context).colorScheme.primary,
          duration: const Duration(seconds: 3),
        ),
      );

      // Navigate to booking confirmation
      RouteUtils.pushAndClearStack('/booking/confirmation');
    } catch (e) {
      setState(() {
        _isQuickRebooking = false;
      });
      
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Failed to rebook: $e'),
          backgroundColor: Theme.of(context).colorScheme.error,
        ),
      );
    }
  }

  Future<void> _selectRebookingOption(RebookingOption option) async {
    // Navigate to time slot selection with pre-filled data
    RouteUtils.navigateToBooking();
  }

  Future<void> _customRebook() async {
    // Navigate to service exploration for custom booking
    RouteUtils.navigateToServices();
  }

  Widget _buildAppBar() {
    return AppBar(
      title: Text('Rebook Appointment'),
      backgroundColor: Theme.of(context).colorScheme.surface,
      elevation: 0,
      foregroundColor: Theme.of(context).colorScheme.onSurface,
      actions: [
        IconButton(
          onPressed: () {
            // Show booking history
            RouteUtils.navigateToBookingHistory();
          },
          icon: Icon(
            Icons.history,
            color: Theme.of(context).colorScheme.onSurface,
          ),
        ),
      ],
    );
  }

  Widget _buildOriginalBookingInfo() {
    return BlocBuilder<BookingBloc, BookingState>(
      builder: (context, state) {
        if (state is BookingLoading) {
          return _buildShimmerBookingInfo();
        }

        if (state is BookingError) {
          return CustomErrorWidget(
            message: state.message,
            onRetry: () => context.read<BookingBloc>().add(LoadBookingDetails(widget.bookingId)),
          );
        }

        if (state is BookingDetailsLoaded) {
          _originalBooking = state.booking;
          return _buildBookingInfoCard(_originalBooking!);
        }

        return const SizedBox.shrink();
      },
    );
  }

  Widget _buildShimmerBookingInfo() {
    return Container(
      margin: const EdgeInsets.all(16),
      height: 120,
      decoration: BoxDecoration(
        color: Theme.of(context).colorScheme.surface,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.1),
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Shimmer.fromColors(
        baseColor: Colors.grey[300]!,
        highlightColor: Colors.grey[100]!,
        child: Container(
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
          ),
        ),
      ),
    );
  }

  Widget _buildBookingInfoCard(Booking booking) {
    return Container(
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Theme.of(context).colorScheme.surface,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.1),
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Previous Appointment',
            style: Theme.of(context).textTheme.titleMedium?.copyWith(
              fontWeight: FontWeight.bold,
              color: Theme.of(context).colorScheme.onSurfaceVariant,
            ),
          ),
          const SizedBox(height: 12),
          Row(
            children: [
            CircleAvatar(
              radius: 20,
              backgroundImage: NetworkImage(booking.therapistImage ?? ''),
              backgroundColor: Theme.of(context).colorScheme.surfaceVariant,
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    booking.serviceName,
                    style: Theme.of(context).textTheme.titleSmall?.copyWith(
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    booking.therapistName,
                    style: Theme.of(context).textTheme.bodySmall?.copyWith(
                      color: Theme.of(context).colorScheme.onSurfaceVariant,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
          const SizedBox(height: 12),
          Row(
            children: [
              Icon(
                Icons.calendar_today,
                size: 16,
                color: Theme.of(context).colorScheme.primary,
              ),
              const SizedBox(width: 4),
              Text(
                _formatDate(booking.appointmentTime),
                style: Theme.of(context).textTheme.bodySmall?.copyWith(
                  color: Theme.of(context).colorScheme.primary,
                ),
              ),
              const SizedBox(width: 16),
              Icon(
                Icons.access_time,
                size: 16,
                color: Theme.of(context).colorScheme.primary,
              ),
              const SizedBox(width: 4),
              Text(
                _formatTime(booking.appointmentTime),
                style: Theme.of(context).textTheme.bodySmall?.copyWith(
                  color: Theme.of(context).colorScheme.primary,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  String _formatDate(DateTime date) {
    final now = DateTime.now();
    final difference = date.difference(now);
    
    if (difference.inDays == 0) {
      return 'Today';
    } else if (difference.inDays == 1) {
      return 'Tomorrow';
    } else if (difference.inDays > 0 && difference.inDays <= 7) {
      return '${difference.inDays} days from now';
    } else {
      return '${date.day}/${date.month}/${date.year}';
    }
  }

  String _formatTime(DateTime time) {
    return '${time.hour.toString().padLeft(2, '0')}:${time.minute.toString().padLeft(2, '0')}';
  }

  Widget _buildQuickRebookingSection() {
    if (_rebookingOptions.isEmpty) return const SizedBox.shrink();
    
    // Find the recommended option (same service, same therapist, similar time)
    final recommendedOption = _rebookingOptions.firstWhere(
      (option) => option.isRecommended,
      orElse: () => _rebookingOptions.first,
    );
    
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16),
      child: QuickRebookingButton(
        option: recommendedOption,
        isLoading: _isQuickRebooking,
        onTap: _quickRebook,
      ),
    );
  }

  Widget _buildRebookingOptionsTab() {
    if (_isLoading) {
      return _buildShimmerOptionsList();
    }

    if (_error != null) {
      return CustomErrorWidget(
        message: _error!,
        onRetry: _loadRebookingData,
      );
    }

    if (_rebookingOptions.isEmpty) {
      return EmptyStateWidget(
        title: 'No Rebooking Options Available',
        message: 'Please try again later or contact our support team.',
        icon: Icons.event_busy,
        action: TextButton(
          onPressed: _customRebook,
          child: const Text('Book Custom Appointment'),
        ),
      );
    }

    return Column(
      children: [
        // Quick rebooking section
        _buildQuickRebookingSection(),
        
        // All rebooking options
        Expanded(
          child: ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: _rebookingOptions.length,
            itemBuilder: (context, index) {
              final option = _rebookingOptions[index];
              return Padding(
                padding: const EdgeInsets.only(bottom: 12),
                child: RebookingOptionCard(
                  option: option,
                  onTap: () => _selectRebookingOption(option),
                ),
              );
            },
          ),
        ),
      ],
    );
  }

  Widget _buildCustomRebookingTab() {
    return Column(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        // Icon
        Container(
          width: 100,
          height: 100,
          decoration: BoxDecoration(
            color: Theme.of(context).colorScheme.primaryContainer,
            shape: BoxShape.circle,
          ),
          child: Icon(
            Icons.calendar_today,
            size: 50,
            color: Theme.of(context).colorScheme.primary,
          ),
        ),
        
        const SizedBox(height: 24),
        
        // Title
        Text(
          'Custom Booking',
          style: Theme.of(context).textTheme.headlineSmall?.copyWith(
            fontWeight: FontWeight.bold,
          ),
        ),
        
        const SizedBox(height: 12),
        
        // Description
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 32),
          child: Text(
            'Choose a different service, therapist, or time for your next appointment.',
            style: Theme.of(context).textTheme.bodyMedium?.copyWith(
              color: Theme.of(context).colorScheme.onSurfaceVariant,
            ),
            textAlign: TextAlign.center,
          ),
        ),
        
        const SizedBox(height: 32),
        
        // Options
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          child: Column(
            children: [
              // Same service, different therapist/time
              _buildCustomOption(
                icon: Icons.spa,
                title: 'Same Service',
                description: 'Book the same service with different therapist or time',
                onTap: () => _navigateToSameService(),
              ),
              
              const SizedBox(height: 16),
              
              // Different service
              _buildCustomOption(
                icon: Icons.category,
                title: 'Different Service',
                description: 'Explore our other wellness services',
                onTap: () => _navigateToDifferentService(),
              ),
              
              const SizedBox(height: 16),
              
              // Package deal
              _buildCustomOption(
                icon: Icons.local_offer,
                title: 'Package Deal',
                description: 'Save money with our service packages',
                onTap: () => _navigateToPackages(),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildCustomOption({
    required IconData icon,
    required String title,
    required String description,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(12),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          border: Border.all(
            color: Theme.of(context).colorScheme.outline,
          ),
          borderRadius: BorderRadius.circular(12),
        ),
        child: Row(
          children: [
            Container(
              width: 50,
              height: 50,
              decoration: BoxDecoration(
                color: Theme.of(context).colorScheme.primaryContainer,
                shape: BoxShape.circle,
              ),
              child: Icon(
                icon,
                color: Theme.of(context).colorScheme.primary,
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: Theme.of(context).textTheme.titleMedium?.copyWith(
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    description,
                    style: Theme.of(context).textTheme.bodySmall?.copyWith(
                      color: Theme.of(context).colorScheme.onSurfaceVariant,
                    ),
                  ),
                ],
              ),
            ),
            Icon(
              Icons.arrow_forward_ios,
              color: Theme.of(context).colorScheme.onSurfaceVariant,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildShimmerOptionsList() {
    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: 4,
      itemBuilder: (context, index) {
        return Padding(
          padding: const EdgeInsets.only(bottom: 16),
          child: Shimmer.fromColors(
            baseColor: Colors.grey[300]!,
            highlightColor: Colors.grey[100]!,
            child: Container(
              height: 120,
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
              ),
            ),
          ),
        );
      },
    );
  }

  void _navigateToSameService() {
    if (_originalBooking != null) {
      // Navigate to therapist selection with same service
      RouteUtils.navigateToTherapistSelection(
        _originalBooking!.serviceId,
        _originalBooking!.serviceName,
      );
    }
  }

  void _navigateToDifferentService() {
    // Navigate to service exploration
    RouteUtils.navigateToServices();
  }

  void _navigateToPackages() {
    // Navigate to packages screen
    RouteUtils.navigateToPackages();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: _buildAppBar(),
      body: Column(
        children: [
          _buildOriginalBookingInfo(),
          Expanded(
            child: TabBarView(
              controller: _tabController,
              children: [
                _buildRebookingOptionsTab(),
                _buildCustomRebookingTab(),
              ],
            ),
          ),
        ],
      ),
      bottomNavigationBar: TabBar(
        controller: _tabController,
        tabs: const [
          Tab(
            icon: Icon(Icons.schedule),
            text: 'Quick Rebook',
          ),
          Tab(
            icon: Icons.edit_calendar,
            text: 'Custom Booking',
          ),
        ],
        labelColor: Theme.of(context).colorScheme.primary,
        unselectedLabelColor: Theme.of(context).colorScheme.onSurfaceVariant,
        indicatorColor: Theme.of(context).colorScheme.primary,
      ),
    );
  }
}
