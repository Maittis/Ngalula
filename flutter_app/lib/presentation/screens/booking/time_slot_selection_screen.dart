import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:table_calendar/table_calendar.dart';
import '../../../core/config/app_config.dart';
import '../../../core/services/user_journey_service.dart';
import '../../../core/config/routes/app_utils.dart';
import '../../../data/models/time_slot.dart';
import '../../../data/models/therapist.dart';
import '../../../data/models/service.dart';
import '../../../presentation/blocs/booking/booking_bloc.dart';
import '../../../presentation/widgets/common/loading_widget.dart';
import '../../../presentation/widgets/common/error_widget.dart';
import '../../../presentation/widgets/booking/time_slot_card.dart';
import '../../../presentation/widgets/booking/selected_time_display.dart';
import '../../../presentation/widgets/common/empty_state_widget.dart';

class TimeSlotSelectionScreen extends StatefulWidget {
  final String therapistId;
  final String therapistName;
  final String therapistImage;
  final String serviceId;
  final String serviceName;
  final int serviceDuration;
  final DateTime initialDate;
  
  const TimeSlotSelectionScreen({
    super.key,
    required this.therapistId,
    required this.therapistName,
    required this.therapistImage,
    required this.serviceId,
    required this.serviceName,
    required this.serviceDuration,
    required this.initialDate,
  });

  @override
  State<TimeSlotSelectionScreen> createState() => _TimeSlotSelectionScreenState();
}

class _TimeSlotSelectionScreenState extends State<TimeSlotSelectionScreen>
    with TickerProviderStateMixin {
  late UserJourneyService _journeyService;
  late TabController _tabController;
  
  DateTime _selectedDate = DateTime.now();
  DateTime _focusedDate = DateTime.now();
  TimeSlot? _selectedTimeSlot;
  List<TimeSlot> _availableTimeSlots = [];
  Map<DateTime, List<TimeSlot>> _timeSlotsByDate = {};
  bool _isLoading = true;
  String? _error;
  CalendarFormat _calendarFormat = CalendarFormat.week;

  @override
  void initState() {
    super.initState();
    _journeyService = UserJourneyService();
    _tabController = TabController(length: 2, vsync: this);
    _selectedDate = widget.initialDate;
    _focusedDate = widget.initialDate;
    _loadTimeSlots();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadTimeSlots() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      // Load available time slots for the selected date
      final timeSlots = await _journeyService.getAvailableTimeSlots(
        widget.therapistId,
        _selectedDate,
      );
      
      // Load time slots for the week
      await _loadWeeklyTimeSlots();
      
      setState(() {
        _availableTimeSlots = timeSlots;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  Future<void> _loadWeeklyTimeSlots() async {
    final startDate = _selectedDate.subtract(Duration(days: 3));
    final endDate = _selectedDate.add(Duration(days: 3));
    
    for (int i = 0; i <= 6; i++) {
      final date = startDate.add(Duration(days: i));
      try {
        final timeSlots = await _journeyService.getAvailableTimeSlots(
          widget.therapistId,
          date,
        );
        _timeSlotsByDate[date] = timeSlots;
      } catch (e) {
        _timeSlotsByDate[date] = [];
      }
    }
  }

  void _onDateSelected(DateTime selectedDate, DateTime focusedDate) {
    setState(() {
      _selectedDate = selectedDate;
      _focusedDate = focusedDate;
      _selectedTimeSlot = null;
      _availableTimeSlots = _timeSlotsByDate[selectedDate] ?? [];
    });
    
    // Load time slots for the selected date if not already loaded
    if (!_timeSlotsByDate.containsKey(selectedDate)) {
      _loadTimeSlotsForDate(selectedDate);
    }
  }

  Future<void> _loadTimeSlotsForDate(DateTime date) async {
    try {
      final timeSlots = await _journeyService.getAvailableTimeSlots(
        widget.therapistId,
        date,
      );
      setState(() {
        _timeSlotsByDate[date] = timeSlots;
        if (date == _selectedDate) {
          _availableTimeSlots = timeSlots;
        }
      });
    } catch (e) {
      setState(() {
        _timeSlotsByDate[date] = [];
        if (date == _selectedDate) {
          _availableTimeSlots = [];
        }
      });
    }
  }

  void _onTimeSlotSelected(TimeSlot timeSlot) async {
    setState(() {
      _selectedTimeSlot = timeSlot;
    });

    try {
      // Select time slot in user journey
      await _journeyService.selectTimeSlot(timeSlot);
      
      // Navigate to payment
      RouteUtils.navigateToPayment();
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Failed to select time slot: $e'),
          backgroundColor: Theme.of(context).colorScheme.error,
        ),
      );
    }
  }

  void _onCalendarFormatChanged() {
    setState(() {
      switch (_calendarFormat) {
        case CalendarFormat.month:
          _calendarFormat = CalendarFormat.twoWeeks;
          break;
        case CalendarFormat.twoWeeks:
          _calendarFormat = CalendarFormat.week;
          break;
        case CalendarFormat.week:
          _calendarFormat = CalendarFormat.month;
          break;
      }
    });
  }

  bool _isDayAvailable(DateTime day) {
    // Check if the day is in the past
    if (day.isBefore(DateTime.now().subtract(const Duration(days: 1)))) {
      return false;
    }
    
    // Check if the day is too far in the future (more than 30 days)
    if (day.isAfter(DateTime.now().add(const Duration(days: 30)))) {
      return false;
    }
    
    // Check if there are available time slots for this day
    final dayTimeSlots = _timeSlotsByDate[DateTime(day.year, day.month, day.day)];
    return dayTimeSlots != null && dayTimeSlots.isNotEmpty;
  }

  int _getAvailableSlotsCount(DateTime day) {
    final dayTimeSlots = _timeSlotsByDate[DateTime(day.year, day.month, day.day)];
    return dayTimeSlots?.where((slot) => slot.isAvailable).length ?? 0;
  }

  Widget _buildAppBar() {
    return AppBar(
      title: Text('Pick Time'),
      backgroundColor: Theme.of(context).colorScheme.surface,
      elevation: 0,
      foregroundColor: Theme.of(context).colorScheme.onSurface,
      actions: [
        IconButton(
          onPressed: _onCalendarFormatChanged,
          icon: Icon(
            _getCalendarFormatIcon(),
            color: Theme.of(context).colorScheme.onSurface,
          ),
        ),
      ],
    );
  }

  IconData _getCalendarFormatIcon() {
    switch (_calendarFormat) {
      case CalendarFormat.month:
        return Icons.calendar_view_month;
      case CalendarFormat.twoWeeks:
        return Icons.calendar_view_week;
      case CalendarFormat.week:
        return Icons.calendar_view_day;
    }
  }

  Widget _buildTherapistInfo() {
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
      child: Row(
        children: [
          CircleAvatar(
            radius: 30,
            backgroundImage: NetworkImage(widget.therapistImage),
            backgroundColor: Theme.of(context).colorScheme.surfaceVariant,
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  widget.therapistName,
                  style: Theme.of(context).textTheme.titleLarge?.copyWith(
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  widget.serviceName,
                  style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                    color: Theme.of(context).colorScheme.primary,
                  ),
                ),
                const SizedBox(height: 4),
                Row(
                  children: [
                    Icon(
                      Icons.access_time,
                      size: 16,
                      color: Theme.of(context).colorScheme.onSurfaceVariant,
                    ),
                    const SizedBox(width: 4),
                    Text(
                      '${widget.serviceDuration} minutes',
                      style: Theme.of(context).textTheme.bodySmall?.copyWith(
                        color: Theme.of(context).colorScheme.onSurfaceVariant,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCalendar() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16),
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
      child: TableCalendar<TimeSlot>(
        firstDay: DateTime.now(),
        lastDay: DateTime.now().add(const Duration(days: 30)),
        focusedDay: _focusedDate,
        selectedDayPredicate: (day) {
          return isSameDay(_selectedDate, day);
        },
        calendarFormat: _calendarFormat,
        onDaySelected: _onDateSelected,
        onPageChanged: (focusedDay) {
          _focusedDate = focusedDay;
          _loadWeeklyTimeSlots();
        },
        eventLoader: (day) {
          final dayTimeSlots = _timeSlotsByDate[DateTime(day.year, day.month, day.day)];
          return dayTimeSlots?.where((slot) => slot.isAvailable).toList() ?? [];
        },
        calendarStyle: CalendarStyle(
          defaultTextStyle: TextStyle(
            color: Theme.of(context).colorScheme.onSurface,
          ),
          selectedTextStyle: TextStyle(
            color: Theme.of(context).colorScheme.onPrimary,
            fontWeight: FontWeight.bold,
          ),
          todayTextStyle: TextStyle(
            color: Theme.of(context).colorScheme.primary,
            fontWeight: FontWeight.bold,
          ),
          weekendTextStyle: TextStyle(
            color: Theme.of(context).colorScheme.onSurface,
          ),
          outsideTextStyle: TextStyle(
            color: Theme.of(context).colorScheme.onSurface.withOpacity(0.4),
          ),
          unavailableTextStyle: TextStyle(
            color: Theme.of(context).colorScheme.onSurface.withOpacity(0.2),
          ),
          selectedDecoration: BoxDecoration(
            color: Theme.of(context).colorScheme.primary,
            shape: BoxShape.circle,
          ),
          todayDecoration: BoxDecoration(
            color: Theme.of(context).colorScheme.primary.withOpacity(0.2),
            shape: BoxShape.circle,
          ),
          markerDecoration: BoxDecoration(
            color: Theme.of(context).colorScheme.secondary,
            shape: BoxShape.circle,
          ),
        ),
        headerStyle: HeaderStyle(
          titleTextStyle: TextStyle(
            color: Theme.of(context).colorScheme.onSurface,
            fontWeight: FontWeight.bold,
          ),
          formatButtonTextStyle: TextStyle(
            color: Theme.of(context).colorScheme.primary,
          ),
          leftChevronIcon: Icon(
            Icons.chevron_left,
            color: Theme.of(context).colorScheme.onSurface,
          ),
          rightChevronIcon: Icon(
            Icons.chevron_right,
            color: Theme.of(context).colorScheme.onSurface,
          ),
        ),
        daysOfWeekStyle: DaysOfWeekStyle(
          weekdayStyle: TextStyle(
            color: Theme.of(context).colorScheme.onSurface,
            fontWeight: FontWeight.bold,
          ),
          weekendStyle: TextStyle(
            color: Theme.of(context).colorScheme.onSurface,
            fontWeight: FontWeight.bold,
          ),
        ),
        enabledDayPredicate: (day) {
          return _isDayAvailable(day);
        },
        calendarBuilders: CalendarBuilders<TimeSlot>(
          markerBuilder: (context, date, events) {
            if (events.isEmpty) return null;
            
            return Positioned(
              right: 1,
              bottom: 1,
              child: Container(
                width: 6,
                height: 6,
                decoration: BoxDecoration(
                  color: Theme.of(context).colorScheme.secondary,
                  shape: BoxShape.circle,
                ),
              ),
            );
          },
          defaultBuilder: (context, date, events) {
            final isAvailable = _isDayAvailable(date);
            final slotsCount = _getAvailableSlotsCount(date);
            
            return Container(
              margin: const EdgeInsets.all(4),
              decoration: BoxDecoration(
                color: isAvailable 
                    ? Theme.of(context).colorScheme.surface
                    : Theme.of(context).colorScheme.surface.withOpacity(0.3),
                borderRadius: BorderRadius.circular(8),
                border: Border.all(
                  color: isSameDay(_selectedDate, date)
                      ? Theme.of(context).colorScheme.primary
                      : Colors.transparent,
                  width: 2,
                ),
              ),
              child: Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text(
                      '${date.day}',
                      style: TextStyle(
                        color: isAvailable
                            ? Theme.of(context).colorScheme.onSurface
                            : Theme.of(context).colorScheme.onSurface.withOpacity(0.4),
                        fontWeight: isSameDay(_selectedDate, date)
                            ? FontWeight.bold
                            : FontWeight.normal,
                      ),
                    ),
                    if (slotsCount > 0 && !isSameDay(_selectedDate, date))
                      Text(
                        '$slotsCount',
                        style: TextStyle(
                          color: Theme.of(context).colorScheme.secondary,
                          fontSize: 10,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                  ],
                ),
              ),
            );
          },
        ),
      ),
    );
  }

  Widget _buildDateInfo() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Theme.of(context).colorScheme.primaryContainer,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Selected Date',
                style: Theme.of(context).textTheme.bodySmall?.copyWith(
                  color: Theme.of(context).colorScheme.onPrimaryContainer,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                _formatDate(_selectedDate),
                style: Theme.of(context).textTheme.titleMedium?.copyWith(
                  fontWeight: FontWeight.bold,
                  color: Theme.of(context).colorScheme.onPrimaryContainer,
                ),
              ),
            ],
          ),
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(
                'Available Slots',
                style: Theme.of(context).textTheme.bodySmall?.copyWith(
                  color: Theme.of(context).colorScheme.onPrimaryContainer,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                '${_availableTimeSlots.length}',
                style: Theme.of(context).textTheme.titleMedium?.copyWith(
                  fontWeight: FontWeight.bold,
                  color: Theme.of(context).colorScheme.onPrimaryContainer,
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
    final tomorrow = now.add(const Duration(days: 1));
    
    if (date.year == now.year && date.month == now.month && date.day == now.day) {
      return 'Today';
    } else if (date.year == tomorrow.year && date.month == tomorrow.month && date.day == tomorrow.day) {
      return 'Tomorrow';
    } else {
      return '${date.day} ${_getMonthName(date.month)} ${date.year}';
    }
  }

  String _getMonthName(int month) {
    const months = [
      'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
      'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
    ];
    return months[month - 1];
  }

  Widget _buildTimeSlotsList() {
    if (_isLoading) {
      return _buildShimmerList();
    }

    if (_error != null) {
      return CustomErrorWidget(
        message: _error!,
        onRetry: _loadTimeSlots,
      );
    }

    if (_availableTimeSlots.isEmpty) {
      return EmptyStateWidget(
        title: 'No Available Time Slots',
        message: 'No time slots are available for the selected date. Please try another date.',
        icon: Icons.access_time,
        action: TextButton(
          onPressed: () {
            // Navigate to previous day
            final previousDay = _selectedDate.subtract(const Duration(days: 1));
            if (_isDayAvailable(previousDay)) {
              _onDateSelected(previousDay, previousDay);
            }
          },
          child: const Text('Check Previous Day'),
        ),
      );
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          child: Text(
            'Available Time Slots',
            style: Theme.of(context).textTheme.titleLarge?.copyWith(
              fontWeight: FontWeight.bold,
            ),
          ),
        ),
        const SizedBox(height: 16),
        Expanded(
          child: ListView.builder(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            itemCount: _availableTimeSlots.length,
            itemBuilder: (context, index) {
              final timeSlot = _availableTimeSlots[index];
              return Padding(
                padding: const EdgeInsets.only(bottom: 12),
                child: TimeSlotCard(
                  timeSlot: timeSlot,
                  isSelected: _selectedTimeSlot?.startTime == timeSlot.startTime,
                  onTap: () => _onTimeSlotSelected(timeSlot),
                ),
              );
            },
          ),
        ),
      ],
    );
  }

  Widget _buildShimmerList() {
    return ListView.builder(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      itemCount: 6,
      itemBuilder: (context, index) {
        return Padding(
          padding: const EdgeInsets.only(bottom: 12),
          child: Shimmer.fromColors(
            baseColor: Colors.grey[300]!,
            highlightColor: Colors.grey[100]!,
            child: Container(
              height: 80,
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

  Widget _buildSelectedTimeDisplay() {
    if (_selectedTimeSlot == null) return const SizedBox.shrink();
    
    return Container(
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Theme.of(context).colorScheme.primary,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        children: [
          Icon(
            Icons.check_circle,
            color: Theme.of(context).colorScheme.onPrimary,
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Selected Time',
                  style: Theme.of(context).textTheme.bodySmall?.copyWith(
                    color: Theme.of(context).colorScheme.onPrimary,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  _formatTime(_selectedTimeSlot!.startTime),
                  style: Theme.of(context).textTheme.titleMedium?.copyWith(
                    fontWeight: FontWeight.bold,
                    color: Theme.of(context).colorScheme.onPrimary,
                  ),
                ),
              ],
            ),
          ),
          TextButton(
            onPressed: () {
              setState(() {
                _selectedTimeSlot = null;
              });
            },
            child: Text(
              'Change',
              style: TextStyle(
                color: Theme.of(context).colorScheme.onPrimary,
              ),
            ),
          ),
        ],
      ),
    );
  }

  String _formatTime(DateTime time) {
    return '${time.hour.toString().padLeft(2, '0')}:${time.minute.toString().padLeft(2, '0')}';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: _buildAppBar(),
      body: Column(
        children: [
          _buildTherapistInfo(),
          const SizedBox(height: 16),
          _buildCalendar(),
          const SizedBox(height: 16),
          _buildDateInfo(),
          const SizedBox(height: 16),
          Expanded(
            child: _buildTimeSlotsList(),
          ),
          _buildSelectedTimeDisplay(),
        ],
      ),
    );
  }
}
