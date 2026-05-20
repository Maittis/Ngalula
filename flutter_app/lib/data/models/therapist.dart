class Therapist {
  final String id;
  final String name;
  final String bio;
  final String avatar;
  final List<String> specializations;
  final double rating;
  final int reviewCount;
  final int yearsOfExperience;
  final double hourlyRate;
  final List<String> certifications;
  final List<String> languages;
  final bool isAvailable;
  final Map<String, dynamic> availability;

  Therapist({
    required this.id,
    required this.name,
    required this.bio,
    required this.avatar,
    required this.specializations,
    this.rating = 0.0,
    this.reviewCount = 0,
    this.yearsOfExperience = 0,
    this.hourlyRate = 0.0,
    this.certifications = const [],
    this.languages = const [],
    this.isAvailable = true,
    this.availability = const {},
  });

  factory Therapist.fromJson(Map<String, dynamic> json) {
    return Therapist(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      bio: json['bio'] ?? '',
      avatar: json['avatar'] ?? '',
      specializations: List<String>.from(json['specializations'] ?? []),
      rating: (json['rating'] ?? 0.0).toDouble(),
      reviewCount: json['review_count'] ?? 0,
      yearsOfExperience: json['years_of_experience'] ?? 0,
      hourlyRate: (json['hourly_rate'] ?? 0.0).toDouble(),
      certifications: List<String>.from(json['certifications'] ?? []),
      languages: List<String>.from(json['languages'] ?? []),
      isAvailable: json['is_available'] ?? true,
      availability: json['availability'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'bio': bio,
      'avatar': avatar,
      'specializations': specializations,
      'rating': rating,
      'review_count': reviewCount,
      'years_of_experience': yearsOfExperience,
      'hourly_rate': hourlyRate,
      'certifications': certifications,
      'languages': languages,
      'is_available': isAvailable,
      'availability': availability,
    };
  }

  Therapist copyWith({
    String? id,
    String? name,
    String? bio,
    String? avatar,
    List<String>? specializations,
    double? rating,
    int? reviewCount,
    int? yearsOfExperience,
    double? hourlyRate,
    List<String>? certifications,
    List<String>? languages,
    bool? isAvailable,
    Map<String, dynamic>? availability,
  }) {
    return Therapist(
      id: id ?? this.id,
      name: name ?? this.name,
      bio: bio ?? this.bio,
      avatar: avatar ?? this.avatar,
      specializations: specializations ?? this.specializations,
      rating: rating ?? this.rating,
      reviewCount: reviewCount ?? this.reviewCount,
      yearsOfExperience: yearsOfExperience ?? this.yearsOfExperience,
      hourlyRate: hourlyRate ?? this.hourlyRate,
      certifications: certifications ?? this.certifications,
      languages: languages ?? this.languages,
      isAvailable: isAvailable ?? this.isAvailable,
      availability: availability ?? this.availability,
    );
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    return other is Therapist && other.id == id;
  }

  @override
  int get hashCode => id.hashCode;

  @override
  String toString() {
    return 'Therapist{id: $id, name: $name, rating: $rating}';
  }
}

// Sample data for testing
List<Therapist> getSampleTherapists() {
  return [
    Therapist(
      id: '1',
      name: 'Dr. Sarah Johnson',
      bio: 'Licensed massage therapist with 10+ years of experience in Swedish, Deep Tissue, and Sports Massage.',
      avatar: 'https://picsum.photos/seed/therapist1/200/200.jpg',
      specializations: ['Swedish Massage', 'Deep Tissue', 'Sports Massage'],
      rating: 4.8,
      reviewCount: 124,
      yearsOfExperience: 12,
      hourlyRate: 120.0,
      certifications: ['Licensed Massage Therapist', 'CPR Certified'],
      languages: ['English', 'Spanish'],
      isAvailable: true,
    ),
    Therapist(
      id: '2',
      name: 'Emily Chen',
      bio: 'Expert esthetician specializing in luxury facials and advanced skincare treatments.',
      avatar: 'https://picsum.photos/seed/therapist2/200/200.jpg',
      specializations: ['Facial', 'Chemical Peels', 'Microdermabrasion'],
      rating: 4.9,
      reviewCount: 89,
      yearsOfExperience: 8,
      hourlyRate: 150.0,
      certifications: ['Licensed Esthetician', 'Advanced Skincare Certified'],
      languages: ['English', 'Mandarin'],
      isAvailable: true,
    ),
    Therapist(
      id: '3',
      name: 'Michael Rodriguez',
      bio: 'Professional hairstylist with expertise in cutting, coloring, and styling.',
      avatar: 'https://picsum.photos/seed/therapist3/200/200.jpg',
      specializations: ['Hair Cutting', 'Hair Coloring', 'Hair Treatment'],
      rating: 4.7,
      reviewCount: 156,
      yearsOfExperience: 15,
      hourlyRate: 100.0,
      certifications: ['Licensed Cosmetologist', 'Color Specialist'],
      languages: ['English', 'Spanish'],
      isAvailable: true,
    ),
    Therapist(
      id: '4',
      name: 'Lisa Wang',
      bio: 'Certified nail technician specializing in gel nails, nail art, and extensions.',
      avatar: 'https://picsum.photos/seed/therapist4/200/200.jpg',
      specializations: ['Manicure', 'Pedicure', 'Gel Nails', 'Nail Art'],
      rating: 4.6,
      reviewCount: 78,
      yearsOfExperience: 6,
      hourlyRate: 80.0,
      certifications: ['Licensed Nail Technician', 'Gel Nail Specialist'],
      languages: ['English', 'Cantonese'],
      isAvailable: true,
    ),
    Therapist(
      id: '5',
      name: 'James Thompson',
      bio: 'Certified wellness coach and meditation instructor.',
      avatar: 'https://picsum.photos/seed/therapist5/200/200.jpg',
      specializations: ['Meditation', 'Yoga', 'Reiki', 'Sound Therapy'],
      rating: 5.0,
      reviewCount: 56,
      yearsOfExperience: 10,
      hourlyRate: 90.0,
      certifications: ['Certified Meditation Instructor', 'Reiki Master', 'RYT-200'],
      languages: ['English', 'French'],
      isAvailable: true,
    ),
  ];
}
