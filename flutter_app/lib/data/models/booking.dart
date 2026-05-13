class Booking {
  final String id;
  final String serviceId;
  final String serviceName;
  final String therapistId;
  final String therapistName;
  final DateTime date;
  final String time;
  final double amount;
  final String status;
  final DateTime createdAt;
  final Map<String, dynamic> additionalData;

  Booking({
    required this.id,
    required this.serviceId,
    required this.serviceName,
    required this.therapistId,
    required this.therapistName,
    required this.date,
    required this.time,
    required this.amount,
    this.status = 'pending',
    required this.createdAt,
    this.additionalData = const {},
  });

  factory Booking.fromJson(Map<String, dynamic> json) {
    return Booking(
      id: json['id'] ?? '',
      serviceId: json['service_id'] ?? '',
      serviceName: json['service_name'] ?? '',
      therapistId: json['therapist_id'] ?? '',
      therapistName: json['therapist_name'] ?? '',
      date: DateTime.parse(json['date'] ?? ''),
      time: json['time'] ?? '',
      amount: (json['amount'] ?? 0.0).toDouble(),
      status: json['status'] ?? 'pending',
      createdAt: DateTime.parse(json['created_at'] ?? ''),
      additionalData: json['additional_data'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'service_id': serviceId,
      'service_name': serviceName,
      'therapist_id': therapistId,
      'therapist_name': therapistName,
      'date': date.toIso8601String(),
      'time': time,
      'amount': amount,
      'status': status,
      'created_at': createdAt.toIso8601String(),
      'additional_data': additionalData,
    };
  }

  Booking copyWith({
    String? id,
    String? serviceId,
    String? serviceName,
    String? therapistId,
    String? therapistName,
    DateTime? date,
    String? time,
    double? amount,
    String? status,
    DateTime? createdAt,
    Map<String, dynamic>? additionalData,
  }) {
    return Booking(
      id: id ?? this.id,
      serviceId: serviceId ?? this.serviceId,
      serviceName: serviceName ?? this.serviceName,
      therapistId: therapistId ?? this.therapistId,
      therapistName: therapistName ?? this.therapistName,
      date: date ?? this.date,
      time: time ?? this.time,
      amount: amount ?? this.amount,
      status: status ?? this.status,
      createdAt: createdAt ?? this.createdAt,
      additionalData: additionalData ?? this.additionalData,
    );
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    return other is Booking && other.id == id;
  }

  @override
  int get hashCode => id.hashCode;

  @override
  String toString() {
    return 'Booking{id: $id, service: $serviceName, therapist: $therapistName}';
  }
}

// Sample data for testing
List<Booking> getSampleBookings() {
  final now = DateTime.now();
  return [
    Booking(
      id: '1',
      serviceId: 'massage',
      serviceName: 'Swedish Massage',
      therapistId: '1',
      therapistName: 'Dr. Sarah Johnson',
      date: now.add(const Duration(days: 2)),
      time: '10:00',
      amount: 120.0,
      status: 'confirmed',
      createdAt: now.subtract(const Duration(days: 7)),
    ),
    Booking(
      id: '2',
      serviceId: 'facial',
      serviceName: 'Luxury Facial',
      therapistId: '2',
      therapistName: 'Emily Chen',
      date: now.add(const Duration(days: 5)),
      time: '14:00',
      amount: 150.0,
      status: 'confirmed',
      createdAt: now.subtract(const Duration(days: 14)),
    ),
    Booking(
      id: '3',
      serviceId: 'nails',
      serviceName: 'Gel Nails',
      therapistId: '4',
      therapistName: 'Lisa Wang',
      date: now.add(const Duration(days: 7)),
      time: '16:00',
      amount: 80.0,
      status: 'completed',
      createdAt: now.subtract(const Duration(days: 21)),
    ),
  ];
}
