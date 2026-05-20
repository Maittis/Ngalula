import 'package:flutter/material.dart';
import '../../../data/models/therapist.dart';

/// Placeholder therapist card widget
class TherapistCard extends StatelessWidget {
  final Therapist therapist;
  final VoidCallback onTap;

  const TherapistCard({
    Key? key,
    required this.therapist,
    required this.onTap,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.all(8),
      child: InkWell(
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                therapist.name,
                style: Theme.of(context).textTheme.titleLarge,
              ),
              const SizedBox(height: 4),
              Text(
                therapist.specializations.join(', '),
                style: Theme.of(context).textTheme.bodySmall,
              ),
              const SizedBox(height: 8),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      const Icon(Icons.star, color: Colors.orange, size: 16),
                      const SizedBox(width: 4),
                      Text('${therapist.rating}'),
                    ],
                  ),
                  Text('\$${therapist.hourlyRate}/hr'),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
