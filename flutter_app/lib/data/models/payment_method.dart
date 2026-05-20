class PaymentMethod {
  final String id;
  final String type;
  final String lastFour;
  final String holderName;
  final String expiryDate;
  final bool isDefault;
  final bool isExpired;

  PaymentMethod({
    required this.id,
    required this.type,
    required this.lastFour,
    required this.holderName,
    required this.expiryDate,
    this.isDefault = false,
    this.isExpired = false,
  });

  factory PaymentMethod.fromJson(Map<String, dynamic> json) {
    return PaymentMethod(
      id: json['id'] ?? '',
      type: json['type'] ?? '',
      lastFour: json['last_four'] ?? '',
      holderName: json['holder_name'] ?? '',
      expiryDate: json['expiry_date'] ?? '',
      isDefault: json['is_default'] ?? false,
      isExpired: json['is_expired'] ?? false,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'type': type,
      'last_four': lastFour,
      'holder_name': holderName,
      'expiry_date': expiryDate,
      'is_default': isDefault,
      'is_expired': isExpired,
    };
  }

  PaymentMethod copyWith({
    String? id,
    String? type,
    String? lastFour,
    String? holderName,
    String? expiryDate,
    bool? isDefault,
    bool? isExpired,
  }) {
    return PaymentMethod(
      id: id ?? this.id,
      type: type ?? this.type,
      lastFour: lastFour ?? this.lastFour,
      holderName: holderName ?? this.holderName,
      expiryDate: expiryDate ?? this.expiryDate,
      isDefault: isDefault ?? this.isDefault,
      isExpired: isExpired ?? this.isExpired,
    );
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    return other is PaymentMethod && other.id == id;
  }

  @override
  int get hashCode => id.hashCode;

  @override
  String toString() {
    return 'PaymentMethod{id: $id, type: $type}';
  }
}

// Sample data for testing
List<PaymentMethod> getSamplePaymentMethods() {
  return [
    PaymentMethod(
      id: '1',
      type: 'visa',
      lastFour: '4242',
      holderName: 'John Doe',
      expiryDate: '12/25',
      isDefault: true,
      isExpired: false,
    ),
    PaymentMethod(
      id: '2',
      type: 'mastercard',
      lastFour: '5555',
      holderName: 'John Doe',
      expiryDate: '08/24',
      isDefault: false,
      isExpired: false,
    ),
    PaymentMethod(
      id: '3',
      type: 'mobile_money',
      lastFour: 'MTN',
      holderName: '+250 788 123 456',
      expiryDate: '',
      isDefault: false,
      isExpired: false,
    ),
  ];
}
