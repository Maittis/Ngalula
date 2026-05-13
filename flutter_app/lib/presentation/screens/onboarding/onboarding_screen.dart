import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../../core/config/app_config.dart';
import '../../../core/config/routes/app_utils.dart';
import '../../../core/services/user_journey_service.dart';
import '../../../presentation/widgets/common/animated_button.dart';
import '../../../presentation/widgets/common/gradient_background.dart';

class OnboardingScreen extends StatefulWidget {
  const OnboardingScreen({super.key});

  @override
  State<OnboardingScreen> createState() => _OnboardingScreenState();
}

class _OnboardingScreenState extends State<OnboardingScreen>
    with TickerProviderStateMixin {
  late PageController _pageController;
  late AnimationController _animationController;
  late Animation<double> _slideAnimation;
  late Animation<double> _fadeAnimation;
  
  int _currentPage = 0;
  bool _isLastPage = false;
  
  final List<OnboardingPage> _pages = [
    OnboardingPage(
      title: 'Welcome to Ngalula',
      subtitle: 'Your Wellness Journey Begins Here',
      description: 'Experience the perfect blend of relaxation and rejuvenation at our premier wellness center.',
      image: 'assets/images/onboarding/welcome.png',
      backgroundColor: Color(0xFF6366F1),
    ),
    OnboardingPage(
      title: 'Explore Our Services',
      subtitle: 'Discover Premium Wellness Treatments',
      description: 'From massages to facials, find the perfect treatment tailored to your needs.',
      image: 'assets/images/onboarding/services.png',
      backgroundColor: Color(0xFFEC4899),
    ),
    OnboardingPage(
      title: 'Choose Your Therapist',
      subtitle: 'Expert Care Professionals',
      description: 'Select from our team of certified therapists who are dedicated to your well-being.',
      image: 'assets/images/onboarding/therapists.png',
      backgroundColor: Color(0xFF10B981),
    ),
    OnboardingPage(
      title: 'Book Your Time',
      subtitle: 'Flexible Scheduling',
      description: 'Pick the perfect time that fits your schedule with our easy-to-use booking system.',
      image: 'assets/images/onboarding/booking.png',
      backgroundColor: Color(0xFFF59E0B),
    ),
    OnboardingPage(
      title: 'Pay Securely',
      subtitle: 'Multiple Payment Options',
      description: 'Choose from various payment methods including cards, mobile money, and more.',
      image: 'assets/images/onboarding/payment.png',
      backgroundColor: Color(0xFF8B5CF6),
    ),
    OnboardingPage(
      title: 'Receive Reminders',
      subtitle: 'Never Miss an Appointment',
      description: 'Get timely reminders for your appointments so you can relax and prepare.',
      image: 'assets/images/onboarding/reminders.png',
      backgroundColor: Color(0xFFEF4444),
    ),
    OnboardingPage(
      title: 'Visit Our Spa',
      subtitle: 'Your Oasis of Relaxation',
      description: 'Step into our tranquil environment and let our experts take care of you.',
      image: 'assets/images/onboarding/spa.png',
      backgroundColor: Color(0xFF14B8A6),
    ),
    OnboardingPage(
      title: 'Earn Rewards',
      subtitle: 'Loyalty Program Benefits',
      description: 'Earn points with every visit and unlock exclusive rewards and discounts.',
      image: 'assets/images/onboarding/rewards.png',
      backgroundColor: Color(0xFFA855F7),
    ),
    OnboardingPage(
      title: 'Rebook Easily',
      subtitle: 'One-Tap Rebooking',
      description: 'Quickly rebook your favorite treatments with our streamlined rebooking system.',
      image: 'assets/images/onboarding/rebooking.png',
      backgroundColor: Color(0xFF3B82F6),
    ),
  ];

  @override
  void initState() {
    super.initState();
    _pageController = PageController();
    _animationController = AnimationController(
      duration: const Duration(milliseconds: 800),
      vsync: this,
    );
    
    _slideAnimation = Tween<double>(
      begin: 0.0,
      end: 1.0,
    ).animate(CurvedAnimation(
      parent: _animationController,
      curve: Curves.easeInOut,
    ));
    
    _fadeAnimation = Tween<double>(
      begin: 0.0,
      end: 1.0,
    ).animate(CurvedAnimation(
      parent: _animationController,
      curve: Curves.easeInOut,
    ));
    
    _animationController.forward();
  }

  @override
  void dispose() {
    _pageController.dispose();
    _animationController.dispose();
    super.dispose();
  }

  void _onPageChanged(int page) {
    setState(() {
      _currentPage = page;
      _isLastPage = page == _pages.length - 1;
    });
    
    // Restart animation for each page
    _animationController.reset();
    _animationController.forward();
  }

  Future<void> _onNextPressed() async {
    if (_isLastPage) {
      await _completeOnboarding();
    } else {
      _pageController.nextPage(
        duration: const Duration(milliseconds: 300),
        curve: Curves.easeInOut,
      );
    }
  }

  Future<void> _onSkipPressed() async {
    await _completeOnboarding();
  }

  Future<void> _completeOnboarding() async {
    // Mark onboarding as completed
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool('onboarding_completed', true);
    
    // Initialize user journey
    final userJourneyService = UserJourneyService();
    await userJourneyService.initializeJourney();
    
    // Navigate to main app
    if (mounted) {
      RouteUtils.navigateToHome();
    }
  }

  Widget _buildPageIndicator() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: List.generate(
        _pages.length,
        (index) => AnimatedContainer(
          duration: const Duration(milliseconds: 300),
          margin: const EdgeInsets.symmetric(horizontal: 4),
          height: 8,
          width: _currentPage == index ? 24 : 8,
          decoration: BoxDecoration(
            color: _currentPage == index
                ? Colors.white
                : Colors.white.withOpacity(0.4),
            borderRadius: BorderRadius.circular(4),
          ),
        ),
      ),
    );
  }

  Widget _buildPageContent(OnboardingPage page) {
    return Column(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        // Image
        Expanded(
          flex: 3,
          child: Center(
            child: AnimatedBuilder(
              animation: _slideAnimation,
              builder: (context, child) {
                return Transform.translate(
                  offset: Offset(0, 50 * (1 - _slideAnimation.value)),
                  child: Opacity(
                    opacity: _fadeAnimation.value,
                    child: Image.asset(
                      page.image,
                      height: 250,
                      fit: BoxFit.contain,
                    ),
                  ),
                );
              },
            ),
          ),
        ),
        
        const SizedBox(height: 40),
        
        // Title
        AnimatedBuilder(
          animation: _fadeAnimation,
          builder: (context, child) {
            return Opacity(
              opacity: _fadeAnimation.value,
              child: Text(
                page.title,
                style: Theme.of(context).textTheme.headlineMedium?.copyWith(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                ),
                textAlign: TextAlign.center,
              ),
            );
          },
        ),
        
        const SizedBox(height: 8),
        
        // Subtitle
        AnimatedBuilder(
          animation: _fadeAnimation,
          builder: (context, child) {
            return Opacity(
              opacity: _fadeAnimation.value,
              child: Text(
                page.subtitle,
                style: Theme.of(context).textTheme.titleMedium?.copyWith(
                  color: Colors.white.withOpacity(0.9),
                ),
                textAlign: TextAlign.center,
              ),
            );
          },
        ),
        
        const SizedBox(height: 20),
        
        // Description
        AnimatedBuilder(
          animation: _fadeAnimation,
          builder: (context, child) {
            return Opacity(
              opacity: _fadeAnimation.value,
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 32),
                child: Text(
                  page.description,
                  style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                    color: Colors.white.withOpacity(0.8),
                    height: 1.5,
                  ),
                  textAlign: TextAlign.center,
                ),
              ),
            );
          },
        ),
        
        const SizedBox(height: 40),
        
        // Page indicator
        _buildPageIndicator(),
        
        const SizedBox(height: 40),
        
        // Buttons
        AnimatedBuilder(
          animation: _fadeAnimation,
          builder: (context, child) {
            return Opacity(
              opacity: _fadeAnimation.value,
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 32),
                child: Row(
                  children: [
                    // Skip button (only show on first few pages)
                    if (_currentPage < 3)
                      Expanded(
                        child: TextButton(
                          onPressed: _onSkipPressed,
                          child: Text(
                            'Skip',
                            style: TextStyle(
                              color: Colors.white.withOpacity(0.8),
                              fontSize: 16,
                            ),
                          ),
                        ),
                      )
                    else
                      const Expanded(child: SizedBox()),
                    
                    // Next/Get Started button
                    Expanded(
                      flex: 2,
                      child: AnimatedButton(
                        text: _isLastPage ? 'Get Started' : 'Next',
                        onPressed: _onNextPressed,
                        backgroundColor: Colors.white,
                        textColor: page.backgroundColor,
                        icon: _isLastPage ? Icons.check_circle : Icons.arrow_forward,
                      ),
                    ),
                  ],
                ),
              ),
            );
          },
        ),
        
        const SizedBox(height: 40),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: GradientBackground(
        colors: [
          _pages[_currentPage].backgroundColor,
          _pages[_currentPage].backgroundColor.withOpacity(0.8),
        ],
        child: SafeArea(
          child: PageView.builder(
            controller: _pageController,
            onPageChanged: _onPageChanged,
            itemCount: _pages.length,
            itemBuilder: (context, index) {
              return _buildPageContent(_pages[index]);
            },
          ),
        ),
      ),
    );
  }
}

class OnboardingPage {
  final String title;
  final String subtitle;
  final String description;
  final String image;
  final Color backgroundColor;
  
  OnboardingPage({
    required this.title,
    required this.subtitle,
    required this.description,
    required this.image,
    required this.backgroundColor,
  });
}

class GradientBackground extends StatelessWidget {
  final List<Color> colors;
  final Widget child;
  
  const GradientBackground({
    super.key,
    required this.colors,
    required this.child,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: colors,
        ),
      ),
      child: child,
    );
  }
}
