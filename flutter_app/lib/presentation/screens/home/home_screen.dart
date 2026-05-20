import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import '../../../core/config/app_config.dart';
import '../../../core/config/routes/app_utils.dart';
import '../../../core/services/user_journey_service.dart';
import '../../../data/models/user.dart';
import '../../../presentation/blocs/auth/auth_bloc.dart';
import '../../../presentation/widgets/common/loading_widget.dart';
import '../../../presentation/widgets/service/service_category_card.dart';
import '../../../presentation/widgets/booking/quick_booking_card.dart';
import '../../../presentation/widgets/rewards/rewards_summary_widget.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  User? _user;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadUserData();
  }

  Future<void> _loadUserData() async {
    final authState = context.read<AuthBloc>().state;
    if (authState is AuthAuthenticated) {
      setState(() {
        _user = authState.user;
        _isLoading = false;
      });
    } else {
      setState(() {
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Scaffold(
        body: LoadingWidget(),
      );
    }

    return Scaffold(
      body: CustomScrollView(
        slivers: [
          // Welcome Header
          SliverAppBar(
            expandedHeight: 200,
            floating: false,
            pinned: true,
            backgroundColor: Colors.white,
            flexibleSpace: FlexibleSpaceBar(
              title: Container(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Welcome back, ${_user?.firstName ?? 'Guest'}!',
                      style: const TextStyle(
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                        color: Colors.black87,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'Ready for your next wellness journey?',
                      style: TextStyle(
                        fontSize: 16,
                        color: Colors.grey[600],
                      ),
                    ),
                  ],
                ),
              ),
              background: Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [
                      Colors.blue[400]!,
                      Colors.purple[400]!,
                    ],
                  ),
                ),
              ),
            ),
          ),

          // Quick Actions
          SliverToBoxAdapter(
            child: Container(
              margin: const EdgeInsets.all(16),
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
                    'Quick Actions',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: Colors.black87,
                    ),
                  ),
                  const SizedBox(height: 16),
                  Row(
                    children: [
                      Expanded(
                        child: _buildQuickAction(
                          icon: Icons.spa,
                          label: 'Book Now',
                          color: Colors.blue[600]!,
                          onTap: () => AppUtils.navigateToBooking(context),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: _buildQuickAction(
                          icon: Icons.explore,
                          label: 'Explore',
                          color: Colors.green[600]!,
                          onTap: () => AppUtils.navigateToServices(context),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  Row(
                    children: [
                      Expanded(
                        child: _buildQuickAction(
                          icon: Icons.history,
                          label: 'Rebook',
                          color: Colors.orange[600]!,
                          onTap: () => AppUtils.navigateToRebooking(context),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: _buildQuickAction(
                          icon: Icons.emoji_events,
                          label: 'Rewards',
                          color: Colors.purple[600]!,
                          onTap: () => AppUtils.navigateToRewards(context),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
          const SliverToBoxAdapter(child: SizedBox(height: 16)),

          // User Journey Progress
          SliverToBoxAdapter(
            child: Container(
              margin: const EdgeInsets.symmetric(horizontal: 16),
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
                    'Your Wellness Journey',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: Colors.black87,
                    ),
                  ),
                  const SizedBox(height: 16),
                  _buildJourneyProgress(),
                ],
              ),
            ),
          ),
          const SliverToBoxAdapter(child: SizedBox(height: 16)),

          // Featured Services
          SliverToBoxAdapter(
            child: Container(
              margin: const EdgeInsets.symmetric(horizontal: 16),
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
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text(
                        'Featured Services',
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                          color: Colors.black87,
                        ),
                      ),
                      TextButton(
                        onPressed: () => AppUtils.navigateToServices(context),
                        child: const Text('See All'),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),
                  SizedBox(
                    height: 200,
                    child: ListView.builder(
                      scrollDirection: Axis.horizontal,
                      itemCount: 6,
                      itemBuilder: (context, index) {
                        return Container(
                          width: 150,
                          margin: const EdgeInsets.only(right: 12),
                          child: ServiceCategoryCard(
                            category: _getSampleCategories()[index],
                            onTap: () => AppUtils.navigateToServiceDetail(context, _getSampleCategories()[index].id),
                          ),
                        );
                      },
                    ),
                  ),
                ],
              ),
            ),
          ),
          const SliverToBoxAdapter(child: SizedBox(height: 16)),

          // Rewards Summary
          SliverToBoxAdapter(
            child: Container(
              margin: const EdgeInsets.symmetric(horizontal: 16),
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
              child: const RewardsSummaryWidget(),
            ),
          ),
          const SliverToBoxAdapter(child: SizedBox(height: 100)),
        ],
      ),
    );
  }

  Widget _buildQuickAction({
    required IconData icon,
    required String label,
    required Color color,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: color.withOpacity(0.1),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: color.withOpacity(0.3)),
        ),
        child: Column(
          children: [
            Icon(icon, color: color, size: 24),
            const SizedBox(height: 8),
            Text(
              label,
              style: TextStyle(
                color: color,
                fontWeight: FontWeight.w600,
                fontSize: 14,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildJourneyProgress() {
    final journeySteps = [
      {'icon': Icons.spa, 'label': 'Explore Spa', 'completed': true},
      {'icon': Icons.favorite, 'label': 'Select Service', 'completed': true},
      {'icon': Icons.person, 'label': 'Choose Therapist', 'completed': false},
      {'icon': Icons.schedule, 'label': 'Pick Time', 'completed': false},
      {'icon': Icons.payment, 'label': 'Pay', 'completed': false},
      {'icon': Icons.notifications, 'label': 'Receive Reminder', 'completed': false},
      {'icon': Icons.location_on, 'label': 'Visit Spa', 'completed': false},
      {'icon': Icons.emoji_events, 'label': 'Earn Rewards', 'completed': false},
      {'icon': Icons.history, 'label': 'Rebook', 'completed': false},
    ];

    return Column(
      children: [
        LinearProgressIndicator(
          value: 0.25, // 2 out of 8 steps completed
          backgroundColor: Colors.grey[300],
          valueColor: AlwaysStoppedAnimation<Color>(Colors.blue[600]!),
        ),
        const SizedBox(height: 16),
        SizedBox(
          height: 120,
          child: ListView.builder(
            scrollDirection: Axis.horizontal,
            itemCount: journeySteps.length,
            itemBuilder: (context, index) {
              final step = journeySteps[index];
              return Container(
                width: 100,
                margin: const EdgeInsets.only(right: 12),
                child: Column(
                  children: [
                    Container(
                      width: 60,
                      height: 60,
                      decoration: BoxDecoration(
                        color: step['completed'] ? Colors.green[100] : Colors.grey[100],
                        borderRadius: BorderRadius.circular(30),
                        border: Border.all(
                          color: step['completed'] ? Colors.green[600]! : Colors.grey[400]!,
                          width: 2,
                        ),
                      ),
                      child: Icon(
                        step['icon'],
                        color: step['completed'] ? Colors.green[600] : Colors.grey[600],
                        size: 24,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      step['label'],
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w500,
                        color: step['completed'] ? Colors.green[600] : Colors.grey[600],
                      ),
                      textAlign: TextAlign.center,
                    ),
                  ],
                ),
              );
            },
          ),
        ),
      ],
    );
  }

  List<ServiceCategory> _getSampleCategories() {
    return [
      ServiceCategory(
        id: 'massage',
        name: 'Massage',
        description: 'Relaxing massage therapies',
        icon: Icons.spa,
        color: Colors.blue[600]!,
        serviceCount: 12,
        imageUrl: 'https://picsum.photos/seed/massage/200/200.jpg',
      ),
      ServiceCategory(
        id: 'facial',
        name: 'Facial',
        description: 'Rejuvenating facial treatments',
        icon: Icons.face,
        color: Colors.pink[600]!,
        serviceCount: 8,
        imageUrl: 'https://picsum.photos/seed/facial/200/200.jpg',
      ),
      ServiceCategory(
        id: 'hair',
        name: 'Hair',
        description: 'Professional hair services',
        icon: Icons.content_cut,
        color: Colors.orange[600]!,
        serviceCount: 15,
        imageUrl: 'https://picsum.photos/seed/hair/200/200.jpg',
      ),
      ServiceCategory(
        id: 'nails',
        name: 'Nails',
        description: 'Luxury nail treatments',
        icon: Icons.back_hand,
        color: Colors.green[600]!,
        serviceCount: 10,
        imageUrl: 'https://picsum.photos/seed/nails/200/200.jpg',
      ),
      ServiceCategory(
        id: 'wellness',
        name: 'Wellness',
        description: 'Holistic wellness therapies',
        icon: Icons.self_improvement,
        color: Colors.purple[600]!,
        serviceCount: 6,
        imageUrl: 'https://picsum.photos/seed/wellness/200/200.jpg',
      ),
      ServiceCategory(
        id: 'more',
        name: 'More',
        description: 'Additional services',
        icon: Icons.more_horiz,
        color: Colors.grey[600]!,
        serviceCount: 20,
        imageUrl: 'https://picsum.photos/seed/more/200/200.jpg',
      ),
    ];
  }
}
