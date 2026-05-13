import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter_staggered_animations/flutter_staggered_animations.dart';
import '../../../core/config/app_config.dart';
import '../../../core/config/routes/app_utils.dart';
import '../../../data/models/service_category.dart';
import '../../../data/models/service.dart';
import '../../../presentation/blocs/service/service_bloc.dart';
import '../../../presentation/widgets/common/loading_widget.dart';
import '../../../presentation/widgets/common/error_widget.dart';
import '../../../presentation/widgets/service/service_category_card.dart';
import '../../../presentation/widgets/service/featured_service_card.dart';
import '../../../presentation/widgets/common/empty_state_widget.dart';

class FeaturedServicesScreen extends StatefulWidget {
  const FeaturedServicesScreen({super.key});

  @override
  State<FeaturedServicesScreen> createState() => _FeaturedServicesScreenState();
}

class _FeaturedServicesScreenState extends State<FeaturedServicesScreen>
    with TickerProviderStateMixin {
  late TabController _tabController;
  late ScrollController _scrollController;
  
  List<ServiceCategory> _categories = [];
  List<Service> _featuredServices = [];
  List<Service> _filteredServices = [];
  bool _isLoading = true;
  String? _error;
  String _selectedCategory = 'all';

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _scrollController = ScrollController();
    _loadData();
  }

  @override
  void dispose() {
    _tabController.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      // Load featured services
      context.read<ServiceBloc>().add(LoadFeaturedServices());
      context.read<ServiceBloc>().add(LoadServices());
      
      // Create service categories
      _categories = _createServiceCategories();
      
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

  List<ServiceCategory> _createServiceCategories() {
    return [
      ServiceCategory(
        id: 'massage',
        name: 'Massage',
        description: 'Therapeutic massage treatments',
        icon: Icons.spa,
        color: const Color(0xFF6366F1),
        serviceCount: 12,
        imageUrl: 'assets/images/services/massage.jpg',
      ),
      ServiceCategory(
        id: 'facial',
        name: 'Facial',
        description: 'Rejuvenating facial treatments',
        icon: Icons.face,
        color: const Color(0xFFEC4899),
        serviceCount: 8,
        imageUrl: 'assets/images/services/facial.jpg',
      ),
      ServiceCategory(
        id: 'hair',
        name: 'Hair',
        description: 'Professional hair services',
        icon: Icons.content_cut,
        color: const Color(0xFFF59E0B),
        serviceCount: 15,
        imageUrl: 'assets/images/services/hair.jpg',
      ),
      ServiceCategory(
        id: 'nails',
        name: 'Nails',
        description: 'Luxury nail treatments',
        icon: Icons.back_hand,
        color: const Color(0xFF10B981),
        serviceCount: 10,
        imageUrl: 'assets/images/services/nails.jpg',
      ),
      ServiceCategory(
        id: 'wellness',
        name: 'Wellness Therapy',
        description: 'Holistic wellness treatments',
        icon: Icons.self_improvement,
        color: const Color(0xFF8B5CF6),
        serviceCount: 6,
        imageUrl: 'assets/images/services/wellness.jpg',
      ),
      ServiceCategory(
        id: 'more',
        name: 'More Services',
        description: 'Additional treatments',
        icon: Icons.more_horiz,
        color: const Color(0xFF6B7280),
        serviceCount: 20,
        imageUrl: 'assets/images/services/more.jpg',
      ),
    ];
  }

  void _onCategorySelected(ServiceCategory category) {
    setState(() {
      _selectedCategory = category.id;
      _filterServices();
    });
  }

  void _filterServices() {
    if (_selectedCategory == 'all') {
      _filteredServices = _featuredServices;
    } else {
      _filteredServices = _featuredServices
          .where((service) => service.category == _selectedCategory)
          .toList();
    }
  }

  void _onServiceSelected(Service service) {
    // Navigate to service details
    RouteUtils.navigateToServiceDetail(service.id);
  }

  void _onSearchTapped() {
    // Navigate to search screen
    showSearch(
      context: context,
      delegate: ServiceSearchDelegate(_featuredServices),
    );
  }

  Widget _buildAppBar() {
    return SliverAppBar(
      expandedHeight: 120,
      floating: false,
      pinned: true,
      backgroundColor: Theme.of(context).colorScheme.surface,
      elevation: 0,
      flexibleSpace: FlexibleSpaceBar(
        title: Text(
          'Featured Services',
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
        ),
      ),
      actions: [
        IconButton(
          onPressed: _onSearchTapped,
          icon: Icon(
            Icons.search,
            color: Theme.of(context).colorScheme.onSurface,
          ),
        ),
        IconButton(
          onPressed: () {
            // Navigate to filter screen
          },
          icon: Icon(
            Icons.filter_list,
            color: Theme.of(context).colorScheme.onSurface,
          ),
        ),
      ],
      bottom: TabBar(
        controller: _tabController,
        tabs: const [
          Tab(
            icon: Icon(Icons.category),
            text: 'Categories',
          ),
          Tab(
            icon: Icons.star,
            text: 'Featured',
          ),
        ],
        labelColor: Theme.of(context).colorScheme.primary,
        unselectedLabelColor: Theme.of(context).colorScheme.onSurface.withOpacity(0.6),
        indicatorColor: Theme.of(context).colorScheme.primary,
      ),
    );
  }

  Widget _buildCategoriesTab() {
    if (_isLoading) {
      return _buildShimmerGrid();
    }

    if (_error != null) {
      return CustomErrorWidget(
        message: _error!,
        onRetry: _loadData,
      );
    }

    return CustomScrollView(
      slivers: [
        SliverPadding(
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
                return AnimationConfiguration.staggeredList(
                  position: index,
                  duration: const Duration(milliseconds: 375),
                  child: SlideAnimation(
                    verticalOffset: 50.0,
                    child: FadeInAnimation(
                      child: ServiceCategoryCard(
                        category: category,
                        onTap: () => _onCategorySelected(category),
                      ),
                    ),
                  ),
                );
              },
              childCount: _categories.length,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildFeaturedTab() {
    return BlocBuilder<ServiceBloc, ServiceState>(
      builder: (context, state) {
        if (state is ServiceLoading) {
          return _buildShimmerList();
        }

        if (state is ServiceError) {
          return CustomErrorWidget(
            message: state.message,
            onRetry: () => context.read<ServiceBloc>().add(LoadFeaturedServices()),
          );
        }

        if (state is FeaturedServicesLoaded) {
          _featuredServices = state.services;
          _filterServices();
          return _buildServicesList();
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

  Widget _buildServicesList() {
    if (_filteredServices.isEmpty) {
      return const SliverFillRemaining(
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
                final service = _filteredServices[index];
                return AnimationConfiguration.staggeredList(
                  position: index,
                  duration: const Duration(milliseconds: 375),
                  child: SlideAnimation(
                    verticalOffset: 50.0,
                    child: FadeInAnimation(
                      child: Padding(
                        padding: const EdgeInsets.only(bottom: 16),
                        child: FeaturedServiceCard(
                          service: service,
                          onTap: () => _onServiceSelected(service),
                        ),
                      ),
                    ),
                  ),
                );
              },
              childCount: _filteredServices.length,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildShimmerGrid() {
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
            return _buildShimmerCard();
          },
          childCount: 6,
        ),
      ),
    );
  }

  Widget _buildShimmerList() {
    return SliverPadding(
      padding: const EdgeInsets.all(16),
      sliver: SliverList(
        delegate: SliverChildBuilderDelegate(
          (context, index) {
            return Padding(
              padding: const EdgeInsets.only(bottom: 16),
              child: _buildShimmerCard(),
            );
          },
          childCount: 6,
        ),
      ),
    );
  }

  Widget _buildShimmerCard() {
    return Container(
      height: 200,
      decoration: BoxDecoration(
        color: Colors.grey[300],
        borderRadius: BorderRadius.circular(12),
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
            _buildAppBar(),
          ];
        },
        body: TabBarView(
          controller: _tabController,
          children: [
            _buildCategoriesTab(),
            _buildFeaturedTab(),
          ],
        ),
      ),
    );
  }
}

class ServiceSearchDelegate extends SearchDelegate<Service> {
  final List<Service> services;

  ServiceSearchDelegate(this.services);

  @override
  List<Widget> buildActions(BuildContext context) {
    return [
      IconButton(
        onPressed: () {
          query = '';
          close(context, null);
        },
        icon: const Icon(Icons.clear),
      ),
    ];
  }

  @override
  Widget buildLeading(BuildContext context) {
    return IconButton(
      onPressed: () => close(context, null),
      icon: const Icon(Icons.arrow_back),
    );
  }

  @override
  Widget buildResults(BuildContext context) {
    final results = services.where((service) =>
        service.name.toLowerCase().contains(query.toLowerCase()) ||
        service.description.toLowerCase().contains(query.toLowerCase())
    ).toList();

    if (results.isEmpty) {
      return const Center(
        child: Text('No services found'),
      );
    }

    return ListView.builder(
      itemCount: results.length,
      itemBuilder: (context, index) {
        final service = results[index];
        return ListTile(
          leading: CachedNetworkImage(
            imageUrl: service.imageUrl,
            width: 50,
            height: 50,
            fit: BoxFit.cover,
            placeholder: (context, url) => Container(
              width: 50,
              height: 50,
              color: Colors.grey[300],
              child: const Icon(Icons.spa),
            ),
            errorWidget: (context, url, error) => Container(
              width: 50,
              height: 50,
              color: Colors.grey[300],
              child: const Icon(Icons.spa),
            ),
          ),
          title: Text(service.name),
          subtitle: Text(service.description),
          trailing: Text('\$${service.price.toStringAsFixed(2)}'),
          onTap: () {
            close(context, service);
            // Navigate to service details
          },
        );
      },
    );
  }

  @override
  Widget buildSuggestions(BuildContext context) {
    final suggestions = services.where((service) =>
        service.name.toLowerCase().contains(query.toLowerCase())
    ).take(5).toList();

    return ListView.builder(
      itemCount: suggestions.length,
      itemBuilder: (context, index) {
        final service = suggestions[index];
        return ListTile(
          leading: CachedNetworkImage(
            imageUrl: service.imageUrl,
            width: 50,
            height: 50,
            fit: BoxFit.cover,
            placeholder: (context, url) => Container(
              width: 50,
              height: 50,
              color: Colors.grey[300],
              child: const Icon(Icons.spa),
            ),
            errorWidget: (context, url, error) => Container(
              width: 50,
              height: 50,
              color: Colors.grey[300],
              child: const Icon(Icons.spa),
            ),
          ),
          title: Text(service.name),
          subtitle: Text(service.description),
          trailing: Text('\$${service.price.toStringAsFixed(2)}'),
          onTap: () {
            close(context, service);
            // Navigate to service details
          },
        );
      },
    );
  }
}
