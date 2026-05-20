import 'package:flutter/material.dart';

/// Placeholder booking details widget
class BookingDetailsWidget extends StatelessWidget {
  final dynamic booking;

  const BookingDetailsWidget({
    Key? key,
    required this.booking,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        border: Border.all(color: Colors.grey[300]!),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Booking Details',
            style: Theme.of(context).textTheme.titleLarge,
          ),
          const SizedBox(height: 16),
          _buildDetailRow('Service', booking?.serviceName ?? 'N/A'),
          _buildDetailRow('Therapist', booking?.therapistName ?? 'N/A'),
          _buildDetailRow('Date', booking?.date.toString() ?? 'N/A'),
          _buildDetailRow('Time', booking?.time ?? 'N/A'),
          _buildDetailRow('Amount', '\$${booking?.amount ?? '0'}'),
        ],
      ),
    );
  }

  Widget _buildDetailRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label),
          Text(value, style: const TextStyle(fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }
}
