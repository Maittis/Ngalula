import 'package:flutter/material.dart';

class ServiceCategory {
  final String id;
  final String name;
  final String description;
  final IconData icon;
  final Color color;
  final int serviceCount;
  final String imageUrl;
  final bool isPopular;
  final List<String> subcategories;

  ServiceCategory({
    required this.id,
    required this.name,
    required this.description,
    required this.icon,
    required this.color,
    required this.serviceCount,
    required this.imageUrl,
    this.isPopular = false,
    this.subcategories = const [],
  });

  factory ServiceCategory.fromJson(Map<String, dynamic> json) {
    return ServiceCategory(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      icon: _getIconFromString(json['icon'] ?? 'spa'),
      color: _getColorFromString(json['color'] ?? '#6366F1'),
      serviceCount: json['service_count'] ?? 0,
      imageUrl: json['image_url'] ?? json['imageUrl'] ?? '',
      isPopular: json['is_popular'] ?? false,
      subcategories: List<String>.from(json['subcategories'] ?? []),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'description': description,
      'icon': _getIconString(icon),
      'color': _getColorString(color),
      'service_count': serviceCount,
      'image_url': imageUrl,
      'is_popular': isPopular,
      'subcategories': subcategories,
    };
  }

  static IconData _getIconFromString(String iconString) {
    switch (iconString.toLowerCase()) {
      case 'spa':
        return Icons.spa;
      case 'face':
        return Icons.face;
      case 'content_cut':
        return Icons.content_cut;
      case 'back_hand':
        return Icons.back_hand;
      case 'self_improvement':
        return Icons.self_improvement;
      case 'more_horiz':
        return Icons.more_horiz;
      case 'favorite':
        return Icons.favorite;
      case 'star':
        return Icons.star;
      case 'local_florist':
        return Icons.local_florist;
      case 'healing':
        return Icons.healing;
      case 'spa_outlined':
        return Icons.spa_outlined;
      case 'face_outlined':
        return Icons.face_outlined;
      default:
        return Icons.spa;
    }
  }

  static Color _getColorFromString(String colorString) {
    try {
      return Color(int.parse(colorString.replace('#', '0xFF')));
    } catch (e) {
      return const Color(0xFF6366F1); // Default color
    }
  }

  static String _getIconString(IconData icon) {
    if (icon == Icons.spa) return 'spa';
    if (icon == Icons.face) return 'face';
    if (icon == Icons.content_cut) return 'content_cut';
    if (icon == Icons.back_hand) return 'back_hand';
    if (icon == Icons.self_improvement) return 'self_improvement';
    if (icon == Icons.more_horiz) return 'more_horiz';
    if (icon == Icons.favorite) return 'favorite';
    if (icon == Icons.star) return 'star';
    if (icon == Icons.local_florist) return 'local_florist';
    if (icon == Icons.healing) return 'healing';
    return 'spa';
  }

  static String _getColorString(Color color) {
    return '#${color.value.toRadixString(16).substring(2).toUpperCase()}';
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    return other is ServiceCategory && other.id == id;
  }

  @override
  int get hashCode => id.hashCode;

  @override
  String toString() {
    return 'ServiceCategory{id: $id, name: $name, serviceCount: $serviceCount}';
  }
}

// Sample data for testing
List<ServiceCategory> getSampleCategories() {
  return [
    ServiceCategory(
      id: 'massage',
      name: 'Massage',
      description: 'Therapeutic massage treatments',
      icon: Icons.spa,
      color: const Color(0xFF6366F1),
      serviceCount: 12,
      imageUrl: 'https://picsum.photos/seed/massage/400/300.jpg',
      isPopular: true,
      subcategories: ['Swedish', 'Deep Tissue', 'Hot Stone', 'Aromatherapy'],
    ),
    ServiceCategory(
      id: 'facial',
      name: 'Facial',
      description: 'Rejuvenating facial treatments',
      icon: Icons.face,
      color: const Color(0xFFEC4899),
      serviceCount: 8,
      imageUrl: 'https://picsum.photos/seed/facial/400/300.jpg',
      isPopular: true,
      subcategories: ['Classic', 'Deep Cleansing', 'Anti-Aging', 'Hydrating'],
    ),
    ServiceCategory(
      id: 'hair',
      name: 'Hair',
      description: 'Professional hair services',
      icon: Icons.content_cut,
      color: const Color(0xFFF59E0B),
      serviceCount: 15,
      imageUrl: 'https://picsum.photos/seed/hair/400/300.jpg',
      isPopular: false,
      subcategories: ['Cut & Style', 'Coloring', 'Treatment', 'Bridal'],
    ),
    ServiceCategory(
      id: 'nails',
      name: 'Nails',
      description: 'Luxury nail treatments',
      icon: Icons.back_hand,
      color: const Color(0xFF10B981),
      serviceCount: 10,
      imageUrl: 'https://picsum.photos/seed/nails/400/300.jpg',
      isPopular: false,
      subcategories: ['Manicure', 'Pedicure', 'Gel', 'Extensions'],
    ),
    ServiceCategory(
      id: 'wellness',
      name: 'Wellness Therapy',
      description: 'Holistic wellness treatments',
      icon: Icons.self_improvement,
      color: const Color(0xFF8B5CF6),
      serviceCount: 6,
      imageUrl: 'https://picsum.photos/seed/wellness/400/300.jpg',
      isPopular: false,
      subcategories: ['Meditation', 'Yoga', 'Reiki', 'Sound Therapy'],
    ),
    ServiceCategory(
      id: 'more',
      name: 'More Services',
      description: 'Additional treatments',
      icon: Icons.more_horiz,
      color: const Color(0xFF6B7280),
      serviceCount: 20,
      imageUrl: 'https://picsum.photos/seed/more/400/300.jpg',
      isPopular: false,
      subcategories: ['Body Wraps', 'Waxing', 'Packages', 'Group Sessions'],
    ),
  ];
}
