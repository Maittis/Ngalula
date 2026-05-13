class Service {
  final String id;
  final String name;
  final String description;
  final String category;
  final double price;
  final double originalPrice;
  final int duration;
  final String imageUrl;
  final List<String> features;
  final double rating;
  final int reviewCount;
  final bool isFeatured;
  final int discount;
  final List<String> benefits;
  final String therapistId;
  final bool isAvailable;

  Service({
    required this.id,
    required this.name,
    required this.description,
    required this.category,
    required this.price,
    this.originalPrice = 0.0,
    required this.duration,
    required this.imageUrl,
    this.features = const [],
    this.rating = 0.0,
    this.reviewCount = 0,
    this.isFeatured = false,
    this.discount = 0,
    this.benefits = const [],
    this.therapistId = '',
    this.isAvailable = true,
  });

  factory Service.fromJson(Map<String, dynamic> json) {
    return Service(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      category: json['category'] ?? '',
      price: (json['price'] ?? 0.0).toDouble(),
      originalPrice: (json['original_price'] ?? 0.0).toDouble(),
      duration: json['duration'] ?? 60,
      imageUrl: json['image_url'] ?? json['imageUrl'] ?? '',
      features: List<String>.from(json['features'] ?? []),
      rating: (json['rating'] ?? 0.0).toDouble(),
      reviewCount: json['review_count'] ?? 0,
      isFeatured: json['is_featured'] ?? false,
      discount: json['discount'] ?? 0,
      benefits: List<String>.from(json['benefits'] ?? []),
      therapistId: json['therapist_id'] ?? '',
      isAvailable: json['is_available'] ?? true,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'description': description,
      'category': category,
      'price': price,
      'original_price': originalPrice,
      'duration': duration,
      'image_url': imageUrl,
      'features': features,
      'rating': rating,
      'review_count': reviewCount,
      'is_featured': isFeatured,
      'discount': discount,
      'benefits': benefits,
      'therapist_id': therapistId,
      'is_available': isAvailable,
    };
  }

  Service copyWith({
    String? id,
    String? name,
    String? description,
    String? category,
    double? price,
    double? originalPrice,
    int? duration,
    String? imageUrl,
    List<String>? features,
    double? rating,
    int? reviewCount,
    bool? isFeatured,
    int? discount,
    List<String>? benefits,
    String? therapistId,
    bool? isAvailable,
  }) {
    return Service(
      id: id ?? this.id,
      name: name ?? this.name,
      description: description ?? this.description,
      category: category ?? this.category,
      price: price ?? this.price,
      originalPrice: originalPrice ?? this.originalPrice,
      duration: duration ?? this.duration,
      imageUrl: imageUrl ?? this.imageUrl,
      features: features ?? this.features,
      rating: rating ?? this.rating,
      reviewCount: reviewCount ?? this.reviewCount,
      isFeatured: isFeatured ?? this.isFeatured,
      discount: discount ?? this.discount,
      benefits: benefits ?? this.benefits,
      therapistId: therapistId ?? this.therapistId,
      isAvailable: isAvailable ?? this.isAvailable,
    );
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    return other is Service && other.id == id;
  }

  @override
  int get hashCode => id.hashCode;

  @override
  String toString() {
    return 'Service{id: $id, name: $name, category: $category, price: $price}';
  }
}

// Sample data for testing
List<Service> getSampleServices() {
  return [
    Service(
      id: '1',
      name: 'Swedish Massage',
      description: 'Classic full-body massage using long, flowing strokes to relax muscles and improve circulation.',
      category: 'massage',
      price: 120.0,
      originalPrice: 150.0,
      duration: 60,
      imageUrl: 'https://picsum.photos/seed/massage1/400/300.jpg',
      features: ['Stress Relief', 'Muscle Relaxation', 'Improved Circulation'],
      rating: 4.8,
      reviewCount: 124,
      isFeatured: true,
      discount: 20,
      benefits: ['Reduces stress', 'Improves flexibility', 'Enhances mood'],
    ),
    Service(
      id: '2',
      name: 'Deep Tissue Massage',
      description: 'Therapeutic massage targeting deep muscle layers to release chronic tension and pain.',
      category: 'massage',
      price: 150.0,
      duration: 90,
      imageUrl: 'https://picsum.photos/seed/massage2/400/300.jpg',
      features: ['Chronic Pain Relief', 'Muscle Recovery', 'Injury Rehabilitation'],
      rating: 4.9,
      reviewCount: 89,
      isFeatured: true,
      discount: 0,
      benefits: ['Relieves pain', 'Improves posture', 'Enhances performance'],
    ),
    Service(
      id: '3',
      name: 'Rejuvenating Facial',
      description: 'Customized facial treatment to refresh and revitalize your skin for a glowing complexion.',
      category: 'facial',
      price: 100.0,
      duration: 60,
      imageUrl: 'https://picsum.photos/seed/facial1/400/300.jpg',
      features: ['Deep Cleansing', 'Hydration', 'Anti-Aging'],
      rating: 4.7,
      reviewCount: 156,
      isFeatured: true,
      discount: 0,
      benefits: ['Clears skin', 'Reduces wrinkles', 'Improves texture'],
    ),
    Service(
      id: '4',
      name: 'Luxury Facial',
      description: 'Premium facial treatment with advanced techniques and high-end skincare products.',
      category: 'facial',
      price: 180.0,
      originalPrice: 200.0,
      duration: 90,
      imageUrl: 'https://picsum.photos/seed/facial2/400/300.jpg',
      features: ['Advanced Treatments', 'Premium Products', 'Visible Results'],
      rating: 5.0,
      reviewCount: 67,
      isFeatured: true,
      discount: 10,
      benefits: ['Luxury experience', 'Visible results', 'Premium products'],
    ),
    Service(
      id: '5',
      name: 'Hair Cut & Style',
      description: 'Professional hair cutting and styling with personalized consultation.',
      category: 'hair',
      price: 80.0,
      duration: 45,
      imageUrl: 'https://picsum.photos/seed/hair1/400/300.jpg',
      features: ['Precision Cutting', 'Styling', 'Consultation'],
      rating: 4.6,
      reviewCount: 203,
      isFeatured: false,
      discount: 0,
      benefits: ['Fresh look', 'Professional style', 'Expert advice'],
    ),
    Service(
      id: '6',
      name: 'Hair Coloring',
      description: 'Professional hair coloring services using premium products for vibrant, lasting color.',
      category: 'hair',
      price: 120.0,
      duration: 120,
      imageUrl: 'https://picsum.photos/seed/hair2/400/300.jpg',
      features: ['Color Application', 'Highlights', 'Root Touch-up'],
      rating: 4.8,
      reviewCount: 145,
      isFeatured: false,
      discount: 0,
      benefits: ['Vibrant color', 'Healthy hair', 'Custom shades'],
    ),
    Service(
      id: '7',
      name: 'Manicure & Pedicure',
      description: 'Complete nail care treatment including manicure and pedicure with polish.',
      category: 'nails',
      price: 60.0,
      duration: 60,
      imageUrl: 'https://picsum.photos/seed/nails1/400/300.jpg',
      features: ['Manicure', 'Pedicure', 'Polish'],
      rating: 4.5,
      reviewCount: 178,
      isFeatured: false,
      discount: 0,
      benefits: ['Neat appearance', 'Healthy nails', 'Relaxing experience'],
    ),
    Service(
      id: '8',
      name: 'Gel Nails',
      description: 'Long-lasting gel nail application with your choice of color and design.',
      category: 'nails',
      price: 80.0,
      duration: 90,
      imageUrl: 'https://picsum.photos/seed/nails2/400/300.jpg',
      features: ['Gel Application', 'Design Options', 'Long-Lasting'],
      rating: 4.7,
      reviewCount: 92,
      isFeatured: false,
      discount: 0,
      benefits: ['Durable finish', 'Custom designs', 'Chip-resistant'],
    ),
    Service(
      id: '9',
      name: 'Meditation Session',
      description: 'Guided meditation session to promote mental clarity, stress relief, and inner peace.',
      category: 'wellness',
      price: 80.0,
      duration: 60,
      imageUrl: 'https://picsum.photos/seed/wellness1/400/300.jpg',
      features: ['Mental Clarity', 'Stress Reduction', 'Mindfulness'],
      rating: 4.9,
      reviewCount: 56,
      isFeatured: true,
      discount: 0,
      benefits: ['Reduces anxiety', 'Improves focus', 'Promotes relaxation'],
    ),
    Service(
      id: '10',
      name: 'Yoga Session',
      description: 'Personalized yoga session tailored to your skill level and wellness goals.',
      category: 'wellness',
      price: 90.0,
      duration: 75,
      imageUrl: 'https://picsum.photos/seed/wellness2/400/300.jpg',
      features: ['Flexibility', 'Strength', 'Balance'],
      rating: 4.8,
      reviewCount: 78,
      isFeatured: false,
      discount: 0,
      benefits: ['Improves flexibility', 'Builds strength', 'Enhances balance'],
    ),
  ];
}
