import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../../core/config/app_config.dart';
import '../../../core/config/routes/app_utils.dart';
import '../../../core/services/user_journey_service.dart';
import '../../../data/models/therapist.dart';
import '../../../data/models/service.dart';
import '../../../presentation/blocs/therapist/therapist_bloc.dart';
import '../../../presentation/widgets/common/loading_widget.dart';
import '../../../presentation/widgets/common/error_widget.dart';
import '../../../presentation/widgets/therapist/therapist_card.dart';
import '../../../presentation/widgets/common/search_bar_widget.dart';
import '../../../presentation/widgets/common/filter_chip_widget.dart';

class TherapistSelectionScreen extends StatefulWidget {
  final Service service;
  
  const TherapistSelectionScreen({
    super.key,
    required this.service,
  });

  @override
  State<TherapistSelectionScreen> createState() => _TherapistSelectionScreenState();
}

class _TherapistSelectionScreenState extends State<TherapistSelectionScreen> {
  List<Therapist> _therapists = [];
  List<Therapist> _filteredTherapists = [];
  bool _isLoading = true;
  String? _error;
  String _searchQuery = '';
  String _selectedSpecialization = 'all';
  String _sortBy = 'rating';
  double _minRating = 0.0;

  @override
  void initState() {
    super.initState();
    _loadTherapists();
  }

  Future<void> _loadTherapists() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      // Update user journey
      await UserJourneyService.updateJourneyStep(
        step: UserJourneyStep.chooseTherapist,
        data: {
          'service_id': widget.service.id,
          'service_name': widget.service.name,
        },
      );

      // Load therapists
      final therapists = await context.read<TherapistBloc>().getTherapistsForService(widget.service.id);
      
      setState(() {
        _therapists = therapists;
        _filteredTherapists = therapists;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = 'Failed to load therapists';
        _isLoading = false;
      });
    }
  }

  void _filterTherapists() {
    setState(() {
      _filteredTherapists = _therapists.where((therapist) {
        // Search filter
        final matchesSearch = _searchQuery.isEmpty || 
            therapist.name.toLowerCase().contains(_searchQuery.toLowerCase()) ||
            therapist.specializations.any((spec) => spec.toLowerCase().contains(_searchQuery.toLowerCase()));
        
        // Specialization filter
        final matchesSpecialization = _selectedSpecialization == 'all' ||
            therapist.specializations.contains(_selectedSpecialization);
        
        // Rating filter
        final matchesRating = therapist.rating >= _minRating;
        
        return matchesSearch && matchesSpecialization && matchesRating;
      }).toList();
      
      // Sort therapists
      _filteredTherapists.sort((a, b) {
        switch (_sortBy) {
          case 'rating':
            return b.rating.compareTo(a.rating);
          case 'experience':
            return b.yearsOfExperience.compareTo(a.yearsOfExperience);
          case 'price':
            return a.hourlyRate.compareTo(b.hourlyRate);
          default:
            return 0;
        }
      });
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Choose Therapist'),
        backgroundColor: Colors.white,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: Column(
        children: [
          // Search and Filters
          Container(
            padding: const EdgeInsets.all(16),
            color: Colors.white,
            child: Column(
              children: [
                // Search Bar
                SearchBarWidget(
                  hintText: 'Search therapists...',
                  onChanged: (value) {
                    _searchQuery = value;
                    _filterTherapists();
                  },
                ),
                const SizedBox(height: 16),
                
                // Filter Chips
                SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: [
                      FilterChipWidget(
                        label: 'All',
                        isSelected: _selectedSpecialization == 'all',
                        onTap: () {
                          _selectedSpecialization = 'all';
                          _filterTherapists();
                        },
                      ),
                      FilterChipWidget(
                        label: 'Massage',
                        isSelected: _selectedSpecialization == 'massage',
                        onTap: () {
                          _selectedSpecialization = 'massage';
                          _filterTherapists();
                        },
                      ),
                      FilterChipWidget(
                        label: 'Facial',
                        isSelected: _selectedSpecialization == 'facial',
                        onTap: () {
                          _selectedSpecialization = 'facial';
                          _filterTherapists();
                        },
                      ),
                      FilterChipWidget(
                        label: 'Hair',
                        isSelected: _selectedSpecialization == 'hair',
                        onTap: () {
                          _selectedSpecialization = 'hair';
                          _filterTherapists();
                        },
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 16),
                
                // Sort Options
                Row(
                  children: [
                    Expanded(
                      child: DropdownButtonFormField<String>(
                        value: _sortBy,
                        decoration: InputDecoration(
                          labelText: 'Sort by',
                          border: OutlineInputBorder(),
                          contentPadding: const EdgeInsets.symmetric(horizontal: 12),
                        ),
                        items: const [
                          DropdownMenuItem(value: 'rating', child: Text('Rating')),
                          DropdownMenuItem(value: 'experience', child: Text('Experience')),
                          DropdownMenuItem(value: 'price', child: Text('Price')),
                        ],
                        onChanged: (value) {
                          _sortBy = value!;
                          _filterTherapists();
                        },
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Slider(
                        value: _minRating,
                        min: 0.0,
                        max: 5.0,
                        divisions: 10,
                        label: 'Min Rating: ${_minRating.toStringAsFixed(1)}',
                        onChanged: (value) {
                          _minRating = value;
                          _filterTherapists();
                        },
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          
          // Therapist List
          Expanded(
            child: _isLoading
                ? const LoadingWidget()
                : _error != null
                    ? ErrorWidget(
                        message: _error!,
                        onRetry: _loadTherapists,
                      )
                    : _filteredTherapists.isEmpty
                        ? Center(
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(
                                  Icons.search_off,
                                  size: 64,
                                  color: Colors.grey[400],
                                ),
                                const SizedBox(height: 16),
                                Text(
                                  'No therapists found',
                                  style: TextStyle(
                                    fontSize: 18,
                                    color: Colors.grey[600],
                                  ),
                                ),
                              ],
                            ),
                          )
                        : ListView.builder(
                            padding: const EdgeInsets.all(16),
                            itemCount: _filteredTherapists.length,
                            itemBuilder: (context, index) {
                              final therapist = _filteredTherapists[index];
                              return TherapistCard(
                                therapist: therapist,
                                onTap: () => _selectTherapist(therapist),
                              );
                            },
                          ),
          ),
        ],
      ),
    );
  }

  void _selectTherapist(Therapist therapist) async {
    // Update user journey
    await UserJourneyService.updateJourneyStep(
      step: UserJourneyStep.chooseTherapist,
      data: {
        'therapist_id': therapist.id,
        'therapist_name': therapist.name,
        'therapist_rating': therapist.rating,
      },
    );

    // Navigate to time slot selection
    AppUtils.navigateToTimeSlotSelection(
      context,
      service: widget.service,
      therapist: therapist,
    );
  }
}
