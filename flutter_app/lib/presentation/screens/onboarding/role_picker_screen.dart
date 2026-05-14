import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../core/config/routes/app_router.dart';

class RolePickerScreen extends StatefulWidget {
  final bool isRegister;

  const RolePickerScreen({super.key, this.isRegister = false});

  @override
  State<RolePickerScreen> createState() => _RolePickerScreenState();
}

class _RolePickerScreenState extends State<RolePickerScreen>
    with SingleTickerProviderStateMixin {
  late AnimationController _animationController;
  late Animation<double> _fadeAnimation;
  late Animation<Offset> _slideAnimation;

  @override
  void initState() {
    super.initState();
    _animationController = AnimationController(
      duration: const Duration(milliseconds: 800),
      vsync: this,
    );
    _fadeAnimation = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(parent: _animationController, curve: Curves.easeIn),
    );
    _slideAnimation = Tween<Offset>(
      begin: const Offset(0, 0.3),
      end: Offset.zero,
    ).animate(
      CurvedAnimation(parent: _animationController, curve: Curves.easeOutBack),
    );
    _animationController.forward();
  }

  @override
  void dispose() {
    _animationController.dispose();
    super.dispose();
  }

  void _navigateToRole(String role) {
    if (widget.isRegister) {
      context.go('/register/$role');
    } else {
      context.go('/login/$role');
    }
  }

  @override
  Widget build(BuildContext context) {
    final isRegister = widget.isRegister;
    final title = isRegister ? 'Create Account' : 'Welcome Back';
    final subtitle = isRegister
        ? 'Choose how you want to join us'
        : 'Choose your portal to sign in';

    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [
              Color(0xFF1E1B4B),
              Color(0xFF312E81),
              Color(0xFF4338CA),
            ],
          ),
        ),
        child: SafeArea(
          child: Center(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(24),
              child: FadeTransition(
                opacity: _fadeAnimation,
                child: SlideTransition(
                  position: _slideAnimation,
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      // Logo
                      Container(
                        width: 100,
                        height: 100,
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.15),
                          shape: BoxShape.circle,
                        ),
                        child: const Icon(
                          Icons.spa,
                          size: 50,
                          color: Colors.white,
                        ),
                      ),
                      const SizedBox(height: 24),
                      Text(
                        'Ngalula Wellness',
                        style: Theme.of(context)
                            .textTheme
                            .headlineMedium
                            ?.copyWith(
                              color: Colors.white,
                              fontWeight: FontWeight.bold,
                            ),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        title,
                        style: Theme.of(context)
                            .textTheme
                            .titleLarge
                            ?.copyWith(
                              color: Colors.white.withOpacity(0.9),
                            ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        subtitle,
                        style: TextStyle(
                          color: Colors.white.withOpacity(0.7),
                          fontSize: 15,
                        ),
                      ),
                      const SizedBox(height: 48),

                      // Customer
                      _buildRoleCard(
                        icon: Icons.person_outline,
                        label: 'Customer',
                        description: 'Book appointments & manage your wellness journey',
                        color: const Color(0xFF6366F1),
                        gradientColors: const [Color(0xFF6366F1), Color(0xFF8B5CF6)],
                        onTap: () => _navigateToRole('customer'),
                      ),
                      const SizedBox(height: 16),

                      // Therapist
                      _buildRoleCard(
                        icon: Icons.medical_services_outlined,
                        label: 'Therapist',
                        description: 'Manage your schedule, earnings & clients',
                        color: const Color(0xFF10B981),
                        gradientColors: const [Color(0xFF10B981), Color(0xFF059669)],
                        onTap: () => _navigateToRole('therapist'),
                      ),
                      const SizedBox(height: 16),

                      // Admin
                      _buildRoleCard(
                        icon: Icons.shield_outlined,
                        label: 'Admin',
                        description: 'Manage the wellness center & staff',
                        color: const Color(0xFFDC2626),
                        gradientColors: const [Color(0xFFDC2626), Color(0xFFB91C1C)],
                        onTap: () => _navigateToRole('admin'),
                      ),
                      const SizedBox(height: 32),

                      // Back button
                      TextButton.icon(
                        onPressed: () => context.go('/onboarding'),
                        icon: const Icon(Icons.arrow_back, color: Colors.white70),
                        label: Text(
                          'Back to Onboarding',
                          style: TextStyle(color: Colors.white.withOpacity(0.7)),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildRoleCard({
    required IconData icon,
    required String label,
    required String description,
    required Color color,
    required List<Color> gradientColors,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        decoration: BoxDecoration(
          gradient: LinearGradient(
            colors: gradientColors,
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: color.withOpacity(0.3),
              blurRadius: 15,
              offset: const Offset(0, 5),
            ),
          ],
        ),
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Row(
            children: [
              Container(
                width: 56,
                height: 56,
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Icon(icon, color: Colors.white, size: 28),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      label,
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      description,
                      style: TextStyle(
                        color: Colors.white.withOpacity(0.8),
                        fontSize: 13,
                      ),
                    ),
                  ],
                ),
              ),
              Icon(Icons.arrow_forward_ios, color: Colors.white.withOpacity(0.8), size: 18),
            ],
          ),
        ),
      ),
    );
  }
}
