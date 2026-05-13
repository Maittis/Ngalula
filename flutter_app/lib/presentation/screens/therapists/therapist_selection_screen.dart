import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter_rating_bar/flutter_rating_bar.dart';
import 'package:shimmer/shimmer.dart';
import '../../../core/config/app_config.dart';
import '../../../core/services/user_journey_service.dart';
import '../../../core/config/routes/app_utils.dart';
import '../../../data/models/therapist.dart';
import '../../../data/models/service.dart';
import '../../../presentation/blocs/therapist/therapist_bloc.dart';
import '../../../presentation/widgets/common/loading_widget.dart';
import '../../../presentation/widgets/common/error_widget.dart';
import '../../../presentation/widgets/therapist/therapist_card.dart';
import '../../../presentation/widgets/common/empty_state_widget.dart';
import '../../../presentation/widgets/common/filter_chip.dart';
import '../../../presentation/widgets/common/search_bar.dart';

class TherapistSelectionScreen extends StatefulWidget {
  final String serviceId;
  final String serviceName;
  final DateTime selectedDate;
  
  const TherapistSelectionScreen({
    super.key,
    required this.serviceId,
    required this.serviceName,
    required this.selectedDate,
  });

  @override
  State<TherapistSelectionScreen> createState() => _TherapistSelectionScreenState();
}

class _TherapistSelectionScreenState extends State<TherapistSelectionScreen>
    with TickerProviderStateMixin {
  late UserJourneyService _journeyService;
  final TextEditingController _searchController = TextEditingController();
  final ScrollController _scrollController = ScrollController();
  
  List<Therapist> _therapists = [];
  List<Therapist> _filteredTherapists = [];
  bool _isLoading = true;
  String? _error;
  String _selectedSpecialization = 'all';
  String _selectedRating = 'all';
  bool _isSearching = false;
  SortOption _sortOption = SortOption.recommended;

  @override
  void initState() {
    super.initState();
    _journeyService = UserJourneyService();
    _loadTherapists();
    _setupSearchListener();
  }

  @override
  void dispose() {
    _searchController.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  void _setupSearchListener() {
    _searchController.addListener(() {
      _filterTherapists();
    });
  }

  Future<void> _loadTherapists() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      // Get available therapists for the selected service and date
      final therapists = await _journeyService.getAvailableTherapists(
        widget.serviceId,
        widget.selectedDate,
      );
      
      // Load therapist details
      context.read<TherapistBloc>().add(LoadTherapists());
      
      setState(() {
        _therapists = therapists;
        _filteredTherapists = therapists;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  void _filterTherapists() {
    final query = _searchController.text.toLowerCase();
    
    setState(() {
      _filteredTherapists = _therapists.where((therapist) {
        // Search filter
        final matchesSearch = query.isEmpty ||
            therapist.name.toLowerCase().contains(query) ||
            therapist.bio.toLowerCase().contains(query) ||
            therapist.specializations.any((spec) => spec.toLowerCase().contains(query));
        
        // Specialization filter
        final matchesSpecialization = _selectedSpecialization == 'all' ||
            therapist.specializations.contains(_selectedSpecialization);
        
        // Rating filter
        final matchesRating = _selectedRating == 'all' ||
            (_selectedRating == '4_plus' && therapist.averageRating >= 4.0) ||
            (_selectedRating == '3_plus' && therapist.averageRating >= 3.0) ||
            (_selectedRating == '5_only' && therapist.averageRating >= 4.8);
        
        return matchesSearch && matchesSpecialization && matchesRating;
      }).toList();
      
      // Apply sorting
      _sortTherapists();
    });
  }

  void _sortTherapists() {
    switch (_sortOption) {
      case SortOption.recommended:
        _filteredTherapists.sort((a, b) {
          // Sort by rating and availability
          final ratingDiff = b.averageRating.compareTo(a.averageRating);
          if (ratingDiff != 0) return ratingDiff;
          return a.totalReviews.compareTo(b.totalReviews);
        });
        break;
      case SortOption.rating:
        _filteredTherapists.sort((a, b) => b.averageRating.compareTo(a.averageRating));
        break;
      case SortOption.experience:
        _filteredTherapists.sort((a, b) => b.yearsExperience.compareTo(a.yearsExperience));
        break;
      case SortOption.price:
        _filteredTherapists.sort((a, b) => a.hourlyRate.compareTo(b.hourlyRate));
        break;
      case SortOption.name:
        _filteredTherapists.sort((a, b) => a.name.compareTo(b.name));
        break;
    }
  }

  void _onTherapistSelected(Therapist therapist) async {
    try {
      // Select therapist in user journey
      await _journeyService.selectTherapist(therapist.id);
      
      // Navigate to time slot selection
      RouteUtils.navigateToBooking();
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Failed to select therapist: $e'),
          backgroundColor: Theme.of(context).colorScheme.error,
        ),
      );
    }
  }

  void _onSpecializationChanged(String specialization) {
    setState(() {
      _selectedSpecialization = specialization;
      _filterTherapists();
    });
  }

  void _onRatingChanged(String rating) {
    setState(() {
      _selectedRating = rating;
      _filterTherapists();
    });
  }

  void _onSortChanged(SortOption option) {
    setState(() {
      _sortOption = option;
      _sortTherapists();
    });
  }

  void _onSearchTapped() {
    setState(() {
      _isSearching = !_isSearching;
      if (!_isSearching) {
        _searchController.clear();
        _filterTherapists();
      }
    });
  }

  Widget _buildAppBar() {
    return AppBar(
      title: _isSearching ? null : Text('Choose Therapist'),
      backgroundColor: Theme.of(context).colorScheme.surface,
      elevation: 0,
      foregroundColor: Theme.of(context).colorScheme.onSurface,
      actions: [
        IconButton(
          onPressed: _onSearchTapped,
          icon: Icon(
            _isSearching ? Icons.close : Icons.search,
            color: Theme.of(context).colorScheme.onSurface,
          ),
        ),
        PopupMenuButton<SortOption>(
          icon: Icon(
            Icons.sort,
            color: Theme.of(context).colorScheme.onSurface,
          ),
          onSelected: _onSortChanged,
          itemBuilder: (context) => [
            PopupMenuItem(
              value: SortOption.recommended,
              child: Row(
                children: [
                  Icon(Icons.star, size: 20),
                  SizedBox(width: 12),
                  Text('Recommended'),
                ],
              ),
            ),
            PopupMenuItem(
              value: SortOption.rating,
              child: Row(
                children: [
                  Icon(Icons.star_rate, size: 20),
                  SizedBox(width: 12),
                  Text('Rating'),
                ],
              ),
            ),
            PopupMenuItem(
              value: SortOption.experience,
              child: Row(
                children: [
                  Icon(Icons.work, size: 20),
                  SizedBox(width: 12),
                  Text('Experience'),
                ],
              ),
            ),
            PopupMenuItem(
              value: SortOption.price,
              child: Row(
                children: [
                  Icon(Icons.attach_money, size: 20),
                  SizedBox(width: 12),
                  Text('Price'),
                ],
              ),
            ),
            PopupMenuItem(
              value: SortOption.name,
              child: Row(
                children: [
                  Icon(Icons.sort_by_alpha, size: 20),
                  SizedBox(width: 12),
                  Text('Name'),
                ],
              ),
            ),
          ],
        ),
      ],
      bottom: _isSearching
          ? null
          : PreferredSize(
              preferredSize: const Size.fromHeight(60),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      widget.serviceName,
                      style: Theme.of(context).textTheme.titleMedium?.copyWith(
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        Icon(
                          Icons.calendar_today,
                          size: 16,
                          color: Theme.of(context).colorScheme.primary,
                        ),
                        const SizedBox(width: 4),
                        Text(
                          '${_formatDate(widget.selectedDate)}',
                          style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                            color: Theme.of(context).colorScheme.primary,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
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
      return '${date.day}/${date.month}/${date.year}';
    }
  }

  Widget _buildSearchBar() {
    if (!_isSearching) return const SizedBox.shrink();
    
    return Container(
      padding: const EdgeInsets.all(16),
      child: CustomSearchBar(
        controller: _searchController,
        hintText: 'Search therapists...',
        onClear: () {
          _searchController.clear();
          _filterTherapists();
        },
      ),
    );
  }

  Widget _buildFilters() {
    if (_isSearching) return const SizedBox.shrink();
    
    return Container(
      height: 120,
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Specialization filter
          SizedBox(
            height: 40,
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              itemCount: _getSpecializations().length + 1,
              itemBuilder: (context, index) {
                if (index == 0) {
                  return Padding(
                    padding: const EdgeInsets.only(right: 8),
                    child: _buildFilterChip('all', 'All Specializations', true),
                  );
                }
                
                final specialization = _getSpecializations()[index - 1];
                return Padding(
                  padding: const EdgeInsets.only(right: 8),
                  child: _buildFilterChip(specialization, specialization, false),
                );
              },
            ),
          ),
          const SizedBox(height: 8),
          // Rating filter
          SizedBox(
            height: 40,
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              itemCount: _getRatingOptions().length,
              itemBuilder: (context, index) {
                final rating = _getRatingOptions()[index];
                return Padding(
                  padding: const EdgeInsets.only(right: 8),
                  child: _buildFilterChip(rating['value'], rating['label'], false),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFilterChip(String value, String label, bool isAll) {
    final isSelected = (isAll && _selectedSpecialization == 'all') ||
        (!isAll && _selectedSpecialization == value) ||
        (isAll && _selectedRating == 'all') ||
        (!isAll && _selectedRating == value);
    
    return FilterChip(
      label: Text(label),
      selected: isSelected,
      onSelected: (selected) {
        if (selected) {
          if (isAll) {
            _onSpecializationChanged(value);
          } else {
            _onRatingChanged(value);
          }
        }
      },
      backgroundColor: Theme.of(context).colorScheme.surface,
      selectedColor: Theme.of(context).colorScheme.primary,
      labelStyle: TextStyle(
        color: isSelected
            ? Theme.of(context).colorScheme.onPrimary
            : Theme.of(context).colorScheme.onSurface,
        fontSize: 12,
      ),
      side: BorderSide(
        color: Theme.of(context).colorScheme.outline,
      ),
    );
  }

  List<String> _getSpecializations() {
    final specializations = <String>{};
    for (final therapist in _therapists) {
      specializations.addAll(therapist.specializations);
    }
    return specializations.toList();
  }

  List<Map<String, String>> _getRatingOptions() {
    return [
      {'value': 'all', 'label': 'All Ratings'},
      {'value': '5_only', 'label': '5 Stars'},
      {'value': '4_plus', 'label': '4+ Stars'},
      {'value': '3_plus', 'label': '3+ Stars'},
    ];
  }

  Widget _buildTherapistList() {
    if (_isLoading) {
      return _buildShimmerList();
    }

    if (_error != null) {
      return CustomErrorWidget(
        message: _error!,
        onRetry: _loadTherapists,
      );
    }

    if (_filteredTherapists.isEmpty) {
      return EmptyStateWidget(
        title: 'No Therapists Available',
        message: 'No therapists are available for the selected date and service.',
        icon: Icons.person_off,
        action: TextButton(
          onPressed: () {
            // Navigate back to date selection
            Navigator.pop(context);
          },
          child: const Text('Change Date'),
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _loadTherapists,
      child: ListView.builder(
        controller: _scrollController,
        padding: const EdgeInsets.all(16),
        itemCount: _filteredTherapists.length,
        itemBuilder: (context, index) {
          final therapist = _filteredTherapists[index];
          return Padding(
            padding: const EdgeInsets.only(bottom: 16),
            child: TherapistCard(
              therapist: therapist,
              onTap: () => _onTherapistSelected(therapist),
              showAvailability: true,
              selectedDate: widget.selectedDate,
            ),
          );
        },
      ),
    );
  }

  Widget _buildShimmerList() {
    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: 6,
      itemBuilder: (context, index) {
        return Padding(
          padding: const EdgeInsets.only(bottom: 16),
          child: Shimmer.fromColors(
            baseColor: Colors.grey[300]!,
            highlightColor: Colors.grey[100]!,
            child: Container(
              height: 140,
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

  Widget _buildStats() {
    if (_isSearching || _isLoading) return const SizedBox.shrink();
    
    return Container(
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Theme.of(context).colorScheme.primaryContainer,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _buildStatItem(
            'Available',
            '${_filteredTherapists.length}',
            Icons.people,
          ),
          _buildStatItem(
            'Avg Rating',
            _getAverageRating(),
            Icons.star,
          ),
          _buildStatItem(
            'Experience',
            _getAverageExperience(),
            Icons.work,
          ),
        ],
      ),
    );
  }

  Widget _buildStatItem(String label, String value, IconData icon) {
    return Column(
      children: [
        Icon(
          icon,
          color: Theme.of(context).colorScheme.primary,
          size: 24,
        ),
        const SizedBox(height: 4),
        Text(
          value,
          style: Theme.of(context).textTheme.titleLarge?.copyWith(
            fontWeight: FontWeight.bold,
            color: Theme.of(context).colorScheme.onPrimaryContainer,
          ),
        ),
        Text(
          label,
          style: Theme.of(context).textTheme.bodySmall?.copyWith(
            color: Theme.of(context).colorScheme.onPrimaryContainer,
          ),
        ),
      ],
    );
  }

  String _getAverageRating() {
    if (_filteredTherapists.isEmpty) return '0.0';
    
    final totalRating = _filteredTherapists
        .map((t) => t.averageRating)
        .reduce((a, b) => a + b, 0.0);
    
    return (totalRating / _filteredTherapists.length).toStringAsFixed(1);
  }

  String _getAverageExperience() {
    if (_filteredTherapists.isEmpty) return '0';
    
    final totalExperience = _filteredTherapists
        .map((t) => t.yearsExperience)
        .reduce((a, b) => a + b, 0);
    
    return (totalExperience / _filteredTherapists.length).toStringAsFixed(0);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: _buildAppBar(),
      body: Column(
        children: [
          _buildSearchBar(),
          _buildFilters(),
          _buildStats(),
          Expanded(
            child: _buildTherapistList(),
          ),
        ],
      ),
    );
  }
}

enum SortOption {
  recommended,
  rating,
  experience,
  price,
  name,
}
