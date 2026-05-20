class Reward {
  final String id;
  final String title;
  final String description;
  final String type;
  final int pointsRequired;
  final String imageUrl;
  final bool isAvailable;
  final int? stock;
  final DateTime? expiryDate;
  final Map<String, dynamic> metadata;

  Reward({
    required this.id,
    required this.title,
    required this.description,
    required this.type,
    required this.pointsRequired,
    required this.imageUrl,
    this.isAvailable = true,
    this.stock,
    this.expiryDate,
    this.metadata = const {},
  });

  factory Reward.fromJson(Map<String, dynamic> json) {
    return Reward(
      id: json['id'] ?? '',
      title: json['title'] ?? '',
      description: json['description'] ?? '',
      type: json['type'] ?? '',
      pointsRequired: json['points_required'] ?? 0,
      imageUrl: json['image_url'] ?? '',
      isAvailable: json['is_available'] ?? true,
      stock: json['stock'],
      expiryDate: json['expiry_date'] != null 
          ? DateTime.parse(json['expiry_date'])
          : null,
      metadata: json['metadata'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'description': description,
      'type': type,
      'points_required': pointsRequired,
      'image_url': imageUrl,
      'is_available': isAvailable,
      'stock': stock,
      'expiry_date': expiryDate?.toIso8601String(),
      'metadata': metadata,
    };
  }

  Reward copyWith({
    String? id,
    String? title,
    String? description,
    String? type,
    int? pointsRequired,
    String? imageUrl,
    bool? isAvailable,
    int? stock,
    DateTime? expiryDate,
    Map<String, dynamic>? metadata,
  }) {
    return Reward(
      id: id ?? this.id,
      title: title ?? this.title,
      description: description ?? this.description,
      type: type ?? this.type,
      pointsRequired: pointsRequired ?? this.pointsRequired,
      imageUrl: imageUrl ?? this.imageUrl,
      isAvailable: isAvailable ?? this.isAvailable,
      stock: stock ?? this.stock,
      expiryDate: expiryDate ?? this.expiryDate,
      metadata: metadata ?? this.metadata,
    );
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    return other is Reward && other.id == id;
  }

  @override
  int get hashCode => id.hashCode;

  @override
  String toString() {
    return 'Reward{id: $id, title: $title, type: $type}';
  }
}

// Sample data for testing
List<Reward> getSampleRewards() {
  return [
    Reward(
      id: '1',
      title: 'Free Massage',
      description: 'Complimentary 60-minute Swedish massage',
      type: 'service',
      pointsRequired: 500,
      imageUrl: 'https://picsum.photos/seed/reward1/200/200.jpg',
      isAvailable: true,
      stock: 10,
    ),
    Reward(
      id: '2',
      title: '20% Off Facial',
      description: 'Get 20% off any luxury facial treatment',
      type: 'discount',
      pointsRequired: 300,
      imageUrl: 'https://picsum.photos/seed/reward2/200/200.jpg',
      isAvailable: true,
      stock: 15,
    ),
    Reward(
      id: '3',
      title: 'Spa Day Pass',
      description: 'Full day access to all spa facilities',
      type: 'experience',
      pointsRequired: 1000,
      imageUrl: 'https://picsum.photos/seed/reward3/200/200.jpg',
      isAvailable: true,
      stock: 5,
    ),
    Reward(
      id: '4',
      title: 'Product Bundle',
      description: 'Exclusive skincare product set',
      type: 'product',
      pointsRequired: 750,
      imageUrl: 'https://picsum.photos/seed/reward4/200/200.jpg',
      isAvailable: true,
      stock: 20,
    ),
    Reward(
      id: '5',
      title: 'VIP Upgrade',
      description: 'Upgrade to VIP membership for 1 month',
      type: 'membership',
      pointsRequired: 2000,
      imageUrl: 'https://picsum.photos/seed/reward5/200/200.jpg',
      isAvailable: true,
      stock: 3,
    ),
    Reward(
      id: '6',
      title: 'Gift Card',
      description: '\$50 gift card for any service',
      type: 'gift_card',
      pointsRequired: 400,
      imageUrl: 'https://picsum.photos/seed/reward6/200/200.jpg',
      isAvailable: true,
      stock: 25,
    ),
  ];
}
