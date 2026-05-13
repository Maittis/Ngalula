class TimeSlot {
  final String id;
  final DateTime date;
  final String startTime;
  final String endTime;
  final bool isAvailable;
  final bool isBooked;
  final String? therapistId;
  final String? serviceId;

  TimeSlot({
    required this.id,
    required this.date,
    required this.startTime,
    required this.endTime,
    this.isAvailable = true,
    this.isBooked = false,
    this.therapistId,
    this.serviceId,
  });

  factory TimeSlot.fromJson(Map<String, dynamic> json) {
    return TimeSlot(
      id: json['id'] ?? '',
      date: DateTime.parse(json['date'] ?? ''),
      startTime: json['start_time'] ?? '',
      endTime: json['end_time'] ?? '',
      isAvailable: json['is_available'] ?? true,
      isBooked: json['is_booked'] ?? false,
      therapistId: json['therapist_id'],
      serviceId: json['service_id'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'date': date.toIso8601String(),
      'start_time': startTime,
      'end_time': endTime,
      'is_available': isAvailable,
      'is_booked': isBooked,
      'therapist_id': therapistId,
      'service_id': serviceId,
    };
  }

  TimeSlot copyWith({
    String? id,
    DateTime? date,
    String? startTime,
    String? endTime,
    bool? isAvailable,
    bool? isBooked,
    String? therapistId,
    String? serviceId,
  }) {
    return TimeSlot(
      id: id ?? this.id,
      date: date ?? this.date,
      startTime: startTime ?? this.startTime,
      endTime: endTime ?? this.endTime,
      isAvailable: isAvailable ?? this.isAvailable,
      isBooked: isBooked ?? this.isBooked,
      therapistId: therapistId ?? this.therapistId,
      serviceId: serviceId ?? this.serviceId,
    );
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    return other is TimeSlot && other.id == id;
  }

  @override
  int get hashCode => id.hashCode;

  @override
  String toString() {
    return 'TimeSlot{id: $id, date: $date, startTime: $startTime}';
  }
}

// Sample data for testing
List<TimeSlot> getSampleTimeSlots() {
  final today = DateTime.now();
  return [
    // Morning slots
    TimeSlot(
      id: '1',
      date: today,
      startTime: '09:00',
      endTime: '10:00',
      isAvailable: true,
    ),
    TimeSlot(
      id: '2',
      date: today,
      startTime: '10:00',
      endTime: '11:00',
      isAvailable: true,
    ),
    TimeSlot(
      id: '3',
      date: today,
      startTime: '11:00',
      endTime: '12:00',
      isAvailable: true,
    ),
    // Afternoon slots
    TimeSlot(
      id: '4',
      date: today,
      startTime: '14:00',
      endTime: '15:00',
      isAvailable: true,
    ),
    TimeSlot(
      id: '5',
      date: today,
      startTime: '15:00',
      endTime: '16:00',
      isAvailable: true,
    ),
    TimeSlot(
      id: '6',
      date: today,
      startTime: '16:00',
      endTime: '17:00',
      isAvailable: true,
    ),
    // Evening slots
    TimeSlot(
      id: '7',
      date: today,
      startTime: '18:00',
      endTime: '19:00',
      isAvailable: true,
    ),
    TimeSlot(
      id: '8',
      date: today,
      startTime: '19:00',
      endTime: '20:00',
      isAvailable: true,
    ),
  ];
}
