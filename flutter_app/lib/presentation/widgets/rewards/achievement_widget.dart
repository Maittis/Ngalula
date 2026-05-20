import 'package:flutter/material.dart';

/// Placeholder achievement widget
class AchievementWidget extends StatelessWidget {
  final String title;
  final String? description;
  final IconData icon;
  final bool isUnlocked;

  const AchievementWidget({
    Key? key,
    required this.title,
    this.description,
    required this.icon,
    this.isUnlocked = false,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.all(8),
      color: isUnlocked ? Colors.yellow[50] : Colors.grey[200],
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              icon,
              size: 48,
              color: isUnlocked ? Colors.amber : Colors.grey,
            ),
            const SizedBox(height: 8),
            Text(
              title,
              style: Theme.of(context).textTheme.titleMedium,
              textAlign: TextAlign.center,
            ),
            if (description != null) ...[
              const SizedBox(height: 4),
              Text(
                description!,
                style: Theme.of(context).textTheme.bodySmall,
                textAlign: TextAlign.center,
              ),
            ],
            if (!isUnlocked)
              Padding(
                padding: const EdgeInsets.only(top: 8),
                child: Text(
                  'Locked',
                  style: TextStyle(
                    color: Colors.grey[600],
                    fontSize: 12,
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }
}
