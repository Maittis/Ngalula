import 'package:flutter/material.dart';

/// Placeholder rebook option card widget
class RebookingOptionCard extends StatelessWidget {
  final dynamic rebookingOption;
  final VoidCallback onTap;

  const RebookingOptionCard({
    Key? key,
    required this.rebookingOption,
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
                rebookingOption?.title ?? 'Rebook Option',
                style: Theme.of(context).textTheme.titleLarge,
              ),
              const SizedBox(height: 8),
              Text(
                rebookingOption?.description ?? '',
                style: Theme.of(context).textTheme.bodySmall,
              ),
              const SizedBox(height: 12),
              ElevatedButton(
                onPressed: onTap,
                child: const Text('Select'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
