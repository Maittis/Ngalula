import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:shimmer/shimmer.dart';
import '../../../core/config/app_config.dart';
import '../../../core/services/user_journey_service.dart';
import '../../../core/config/routes/app_utils.dart';
import '../../../data/models/service_category.dart';
import '../../../data/models/service.dart';
import '../../../presentation/blocs/service/service_bloc.dart';
import '../../../presentation/widgets/common/loading_widget.dart';
import '../../../presentation/widgets/common/error_widget.dart';
import '../../../presentation/widgets/common/search_bar.dart';
import '../../../presentation/widgets/service/service_card.dart';
import '../../../presentation/widgets/service/category_card.dart';
import '../../../presentation/widgets/common/empty_state_widget.dart';

class ServiceExplorationScreen extends StatefulWidget {
  const ServiceExplorationScreen({super.key});

  @override
  State<ServiceExplorationScreen> createState() => _ServiceExplorationScreenState();
}

class _ServiceExplorationScreenState extends State<ServiceExplorationScreen>
    with TickerProviderStateMixin {
  late TabController _tabController;
  late UserJourneyService _journeyService;
  final TextEditingController _searchController = TextEditingController();
  final ScrollController _scrollController = ScrollController();
  
  bool _isSearching = false;
  String _selectedCategory = 'all';
  List<ServiceCategory> _categories = [];
  List<Service> _services = [];
  List<Service> _featuredServices = [];
  List<Service> _filteredServices = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _journeyService = UserJourneyService();
    _tabController = TabController(length: 3, vsync: this);
    _loadData();
    _setupSearchListener();
  }

  @override
  void dispose() {
    _tabController.dispose();
    _searchController.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  void _setupSearchListener() {
    _searchController.addListener(() {
      _filterServices();
    });
  }

  Future<void> _loadData() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      // Load categories
      final categories = await _journeyService.exploreSpaServices();
      
      // Load featured services
      context.read<ServiceBloc>().add(LoadFeaturedServices());
      context.read<ServiceBloc>().add(LoadServices());
      
      setState(() {
        _categories = categories;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  void _filterServices() {
    final query = _searchController.text.toLowerCase();
    
    setState(() {
      if (query.isEmpty && _selectedCategory == 'all') {
        _filteredServices = _services;
      } else {
        _filteredServices = _services.where((service) {
          final matchesSearch = query.isEmpty || 
              service.name.toLowerCase().contains(query) ||
              service.description.toLowerCase().contains(query) ||
              service.category.toLowerCase().contains(query);
          
          final matchesCategory = _selectedCategory == 'all' ||
              service.category == _selectedCategory;
          
          return matchesSearch && matchesCategory;
        }).toList();
      }
    });
  }

  void _onCategorySelected(String categoryId) {
    setState(() {
      _selectedCategory = categoryId;
      _filterServices();
    });
  }

  void _onServiceSelected(Service service) {
    // Navigate to service details
    RouteUtils.navigateToServiceDetail(service.id);
  }

  void _onSearchTapped() {
    setState(() {
      _isSearching = !_isSearching;
      if (!_isSearching) {
        _searchController.clear();
        _filterServices();
      }
    });
  }

  Widget _buildHeader() {
    return SliverAppBar(
      expandedHeight: 200,
      floating: false,
      pinned: true,
      backgroundColor: Theme.of(context).colorScheme.surface,
      elevation: 0,
      flexibleSpace: FlexibleSpaceBar(
        title: _isSearching
            ? null
            : Text(
                'Explore Our Services',
                style: TextStyle(
                  color: Theme.of(context).colorScheme.onSurface,
                  fontWeight: FontWeight.bold,
                ),
              ),
        background: Container(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [
                Theme.of(context).colorScheme.primary.withOpacity(0.8),
                Theme.of(context).colorScheme.secondary.withOpacity(0.6),
              ],
            ),
          ),
          child: Stack(
            children: [
              // Background pattern
              Positioned.fill(
                child: Container(
                  decoration: BoxDecoration(
                    image: DecorationImage(
                      image: AssetImage('assets/images/spa_background.jpg'),
                      fit: BoxFit.cover,
                      colorFilter: ColorFilter.mode(
                        Colors.black.withOpacity(0.3),
                        BlendMode.darken,
                      ),
                    ),
                  ),
                ),
              ),
              // Welcome message
              Positioned(
                bottom: 20,
                left: 20,
                right: 20,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Welcome to Ngalula',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    SizedBox(height: 8),
                    Text(
                      'Discover our premium wellness services',
                      style: TextStyle(
                        color: Colors.white.withOpacity(0.9),
                        fontSize: 16,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
      actions: [
        IconButton(
          onPressed: _onSearchTapped,
          icon: Icon(
            _isSearching ? Icons.close : Icons.search,
            color: Theme.of(context).colorScheme.onSurface,
          ),
        ),
      ],
      bottom: _isSearching
          ? null
          : TabBar(
              controller: _tabController,
              tabs: const [
                Tab(text: 'Categories'),
                Tab(text: 'Featured'),
                Tab(text: 'All Services'),
              ],
              labelColor: Theme.of(context).colorScheme.primary,
              unselectedLabelColor: Theme.of(context).colorScheme.onSurface.withOpacity(0.6),
              indicatorColor: Theme.of(context).colorScheme.primary,
            ),
    );
  }

  Widget _buildSearchBar() {
    if (!_isSearching) return const SliverToBoxAdapter(child: SizedBox.shrink());
    
    return SliverToBoxAdapter(
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Theme.of(context).colorScheme.surface,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.1),
              blurRadius: 4,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: CustomSearchBar(
          controller: _searchController,
          hintText: 'Search services...',
          onClear: () {
            _searchController.clear();
            _filterServices();
          },
        ),
      ),
    );
  }

  Widget _buildCategoryFilter() {
    if (_isSearching) return const SliverToBoxAdapter(child: SizedBox.shrink());
    
    return SliverToBoxAdapter(
      child: Container(
        height: 80,
        padding: const EdgeInsets.symmetric(vertical: 8),
        child: ListView.builder(
          scrollDirection: Axis.horizontal,
          padding: const EdgeInsets.symmetric(horizontal: 16),
          itemCount: _categories.length + 1,
          itemBuilder: (context, index) {
            if (index == 0) {
              // "All" category
              return Padding(
                padding: const EdgeInsets.only(right: 12),
                child: _buildCategoryChip('all', 'All', true),
              );
            }
            
            final category = _categories[index - 1];
            return Padding(
              padding: const EdgeInsets.only(right: 12),
              child: _buildCategoryChip(category.id, category.name, false),
            );
          },
        ),
      ),
    );
  }

  Widget _buildCategoryChip(String categoryId, String label, bool isAll) {
    final isSelected = _selectedCategory == categoryId;
    
    return FilterChip(
      label: Text(label),
      selected: isSelected,
      onSelected: (selected) {
        if (selected) {
          _onCategorySelected(categoryId);
        }
      },
      backgroundColor: Theme.of(context).colorScheme.surface,
      selectedColor: Theme.of(context).colorScheme.primary,
      labelStyle: TextStyle(
        color: isSelected
            ? Theme.of(context).colorScheme.onPrimary
            : Theme.of(context).colorScheme.onSurface,
      ),
      side: BorderSide(
        color: Theme.of(context).colorScheme.outline,
      ),
    );
  }

  Widget _buildCategoriesTab() {
    if (_isLoading) {
      return SliverFillRemaining(
        child: _buildShimmerGrid(),
      );
    }

    if (_error != null) {
      return SliverFillRemaining(
        child: CustomErrorWidget(
          message: _error!,
          onRetry: _loadData,
        ),
      );
    }

    return SliverPadding(
      padding: const EdgeInsets.all(16),
      sliver: SliverGrid(
        grid: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          crossAxisSpacing: 16,
          mainAxisSpacing: 16,
          childAspectRatio: 0.8,
        ),
        delegate: SliverChildBuilderDelegate(
          (context, index) {
            final category = _categories[index];
            return CategoryCard(
              category: category,
              onTap: () {
                // Filter services by category
                _onCategorySelected(category.id);
                _tabController.animateTo(2); // Switch to "All Services" tab
              },
            );
          },
          childCount: _categories.length,
        ),
      ),
    );
  }

  Widget _buildFeaturedTab() {
    return BlocBuilder<ServiceBloc, ServiceState>(
      builder: (context, state) {
        if (state is ServiceLoading) {
          return SliverFillRemaining(
            child: _buildShimmerList(),
          );
        }

        if (state is ServiceError) {
          return SliverFillRemaining(
            child: CustomErrorWidget(
              message: state.message,
              onRetry: () => context.read<ServiceBloc>().add(LoadFeaturedServices()),
            ),
          );
        }

        if (state is FeaturedServicesLoaded) {
          _featuredServices = state.services;
          return _buildServiceList(_featuredServices, 'Featured Services');
        }

        return const SliverFillRemaining(
          child: EmptyStateWidget(
            title: 'No Featured Services',
            message: 'Check back later for our featured services.',
            icon: Icons.star_outline,
          ),
        );
      },
    );
  }

  Widget _buildAllServicesTab() {
    return BlocBuilder<ServiceBloc, ServiceState>(
      builder: (context, state) {
        if (state is ServiceLoading) {
          return SliverFillRemaining(
            child: _buildShimmerList(),
          );
        }

        if (state is ServiceError) {
          return SliverFillRemaining(
            child: CustomErrorWidget(
              message: state.message,
              onRetry: () => context.read<ServiceBloc>().add(LoadServices()),
            ),
          );
        }

        if (state is ServicesLoaded) {
          _services = state.services;
          _filterServices();
          return _buildServiceList(_filteredServices, 'All Services');
        }

        return const SliverFillRemaining(
          child: EmptyStateWidget(
            title: 'No Services Available',
            message: 'Check back later for our available services.',
            icon: Icons.spa_outlined,
          ),
        );
      },
    );
  }

  Widget _buildServiceList(List<Service> services, String title) {
    if (services.isEmpty) {
      return SliverFillRemaining(
        child: EmptyStateWidget(
          title: 'No Services Found',
          message: 'Try adjusting your search or filters.',
          icon: Icons.search_off,
        ),
      );
    }

    return CustomScrollView(
      slivers: [
        SliverPadding(
          padding: const EdgeInsets.all(16),
          sliver: SliverList(
            delegate: SliverChildBuilderDelegate(
              (context, index) {
                return Padding(
                  padding: const EdgeInsets.only(bottom: 16),
                  child: ServiceCard(
                    service: services[index],
                    onTap: () => _onServiceSelected(services[index]),
                  ),
                );
              },
              childCount: services.length,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildShimmerGrid() {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: GridView.builder(
        grid: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          crossAxisSpacing: 16,
          mainAxisSpacing: 16,
          childAspectRatio: 0.8,
        ),
        itemCount: 6,
        itemBuilder: (context, index) {
          return Shimmer.fromColors(
            baseColor: Colors.grey[300]!,
            highlightColor: Colors.grey[100]!,
            child: Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildShimmerList() {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: ListView.builder(
        itemCount: 6,
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
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: NestedScrollView(
        controller: _scrollController,
        headerSliverBuilder: (context, innerBoxIsScrolled) {
          return [
            _buildHeader(),
            _buildSearchBar(),
            _buildCategoryFilter(),
          ];
        },
        body: TabBarView(
          controller: _tabController,
          children: [
            _buildCategoriesTab(),
            _buildFeaturedTab(),
            _buildAllServicesTab(),
          ],
        ),
      ),
      floatingActionButton: _selectedCategory != 'all'
          ? FloatingActionButton.extended(
              onPressed: () {
                _onCategorySelected('all');
                _tabController.animateTo(0); // Switch to Categories tab
              },
              icon: const Icon(Icons.filter_list_off),
              label: const Text('Clear Filter'),
            )
          : null,
    );
  }
}
