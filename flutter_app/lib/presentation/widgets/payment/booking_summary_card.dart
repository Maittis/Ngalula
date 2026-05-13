import 'package:flutter/material.dart';

/// Placeholder booking summary widget
class BookingSummaryCard extends StatelessWidget {
  final dynamic bookingSummary;

  const BookingSummaryCard({
    Key? key,
    required this.bookingSummary,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.orange[50],
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.orange[200]!),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Order Summary',
            style: Theme.of(context).textTheme.titleLarge,
          ),
          const SizedBox(height: 16),
          _buildRow('Subtotal', '\$${bookingSummary?.subtotal ?? 0}'),
          _buildRow('Tax', '\$${bookingSummary?.tax ?? 0}'),
          const Divider(),
          _buildRow(
            'Total',
            '\$${bookingSummary?.totalAmount ?? 0}',
            bold: true,
          ),
        ],
      ),
    );
  }

  Widget _buildRow(String label, String value, {bool bold = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label),
          Text(
            value,
            style: TextStyle(
              fontWeight: bold ? FontWeight.bold : FontWeight.normal,
            ),
          ),
        ],
      ),
    );
  }
}
