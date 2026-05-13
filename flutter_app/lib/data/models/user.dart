class User {
  final String id;
  final String firstName;
  final String lastName;
  final String email;
  final String? phone;
  final String? avatar;
  final int? rewardPoints;
  final String? membershipTier;
  final DateTime? membershipExpiry;
  final List<String> preferences;
  final Map<String, dynamic> profile;

  User({
    required this.id,
    required this.firstName,
    required this.lastName,
    required this.email,
    this.phone,
    this.avatar,
    this.rewardPoints,
    this.membershipTier,
    this.membershipExpiry,
    this.preferences = const [],
    this.profile = const {},
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'] ?? '',
      firstName: json['first_name'] ?? '',
      lastName: json['last_name'] ?? '',
      email: json['email'] ?? '',
      phone: json['phone'],
      avatar: json['avatar'],
      rewardPoints: json['reward_points'],
      membershipTier: json['membership_tier'],
      membershipExpiry: json['membership_expiry'] != null 
          ? DateTime.parse(json['membership_expiry'])
          : null,
      preferences: List<String>.from(json['preferences'] ?? []),
      profile: json['profile'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'first_name': firstName,
      'last_name': lastName,
      'email': email,
      'phone': phone,
      'avatar': avatar,
      'reward_points': rewardPoints,
      'membership_tier': membershipTier,
      'membership_expiry': membershipExpiry?.toIso8601String(),
      'preferences': preferences,
      'profile': profile,
    };
  }

  User copyWith({
    String? id,
    String? firstName,
    String? lastName,
    String? email,
    String? phone,
    String? avatar,
    int? rewardPoints,
    String? membershipTier,
    DateTime? membershipExpiry,
    List<String>? preferences,
    Map<String, dynamic>? profile,
  }) {
    return User(
      id: id ?? this.id,
      firstName: firstName ?? this.firstName,
      lastName: lastName ?? this.lastName,
      email: email ?? this.email,
      phone: phone ?? this.phone,
      avatar: avatar ?? this.avatar,
      rewardPoints: rewardPoints ?? this.rewardPoints,
      membershipTier: membershipTier ?? this.membershipTier,
      membershipExpiry: membershipExpiry ?? this.membershipExpiry,
      preferences: preferences ?? this.preferences,
      profile: profile ?? this.profile,
    );
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    return other is User && other.id == id;
  }

  @override
  int get hashCode => id.hashCode;

  @override
  String toString() {
    return 'User{id: $id, name: $firstName $lastName}';
  }
}

// Sample data for testing
User getSampleUser() {
  return User(
    id: '1',
    firstName: 'John',
    lastName: 'Doe',
    email: 'john.doe@example.com',
    phone: '+250 788 123 456',
    avatar: 'https://picsum.photos/seed/user1/200/200.jpg',
    rewardPoints: 250,
    membershipTier: 'Gold',
    membershipExpiry: DateTime.now().add(const Duration(days: 365)),
    preferences: ['massage', 'facial', 'notifications'],
    profile: {
      'age': 35,
      'gender': 'male',
      'address': 'Kigali, Rwanda',
    },
  );
}
