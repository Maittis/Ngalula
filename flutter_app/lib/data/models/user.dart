class User {
  final String id;
  final String firstName;
  final String lastName;
  final String email;
  final String? phone;
  final String? avatar;
  final String? userType;
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
    this.userType,
    this.rewardPoints,
    this.membershipTier,
    this.membershipExpiry,
    this.preferences = const [],
    this.profile = const {},
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id']?.toString() ?? '',
      firstName: json['first_name'] ?? json['name']?.toString().split(' ').first ?? '',
      lastName: json['last_name'] ?? json['name']?.toString().contains(' ') == true
          ? json['name'].toString().split(' ').sublist(1).join(' ')
          : '',
      email: json['email'] ?? '',
      phone: json['phone'],
      avatar: json['avatar'] ?? json['profile_photo_url'],
      userType: json['user_type'],
      rewardPoints: json['reward_points'],
      membershipTier: json['membership_tier'],
      membershipExpiry: json['membership_expiry'] != null
          ? DateTime.tryParse(json['membership_expiry'])
          : null,
      preferences: json['preferences'] != null
          ? List<String>.from(json['preferences'])
          : [],
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
      'user_type': userType,
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
    String? userType,
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
      userType: userType ?? this.userType,
      rewardPoints: rewardPoints ?? this.rewardPoints,
      membershipTier: membershipTier ?? this.membershipTier,
      membershipExpiry: membershipExpiry ?? this.membershipExpiry,
      preferences: preferences ?? this.preferences,
      profile: profile ?? this.profile,
    );
  }

  /// Check if user is a customer
  bool get isCustomer => userType == 'customer';

  /// Check if user is a therapist
  bool get isTherapist => userType == 'therapist';

  /// Check if user is an admin
  bool get isAdmin => userType == 'admin' || userType == 'super_admin';

  /// Check if user is staff (therapist, admin, etc.)
  bool get isStaff => isTherapist || isAdmin;

  /// Get display name
  String get displayName => '$firstName $lastName';

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    return other is User && other.id == id;
  }

  @override
  int get hashCode => id.hashCode;

  @override
  String toString() {
    return 'User{id: $id, name: $displayName, type: $userType}';
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
    userType: 'customer',
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
