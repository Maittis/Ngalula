import 'package:flutter/material.dart';

/// Placeholder time slot card widget
class TimeSlotCard extends StatelessWidget {
  final String time;
  final bool isAvailable;
  final bool isSelected;
  final VoidCallback onTap;

  const TimeSlotCard({
    Key? key,
    required this.time,
    required this.isAvailable,
    required this.isSelected,
    required this.onTap,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.all(8),
      color: isSelected ? Colors.blue : (isAvailable ? Colors.white : Colors.grey[200]),
      child: InkWell(
        onTap: isAvailable ? onTap : null,
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Center(
            child: Text(
              time,
              style: Theme.of(context).textTheme.titleMedium?.copyWith(
                    color: isSelected ? Colors.white : Colors.black,
                  ),
            ),
          ),
        ),
      ),
    );
  }
}
