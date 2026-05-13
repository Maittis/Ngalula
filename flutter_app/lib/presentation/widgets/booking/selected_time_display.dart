import 'package:flutter/material.dart';

/// Placeholder selected time display widget
class SelectedTimeDisplay extends StatelessWidget {
  final String? selectedTime;

  const SelectedTimeDisplay({
    Key? key,
    this.selectedTime,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.blue[100],
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Selected Time'),
          const SizedBox(height: 8),
          Text(
            selectedTime ?? 'No time selected',
            style: Theme.of(context).textTheme.titleLarge,
          ),
        ],
      ),
    );
  }
}
