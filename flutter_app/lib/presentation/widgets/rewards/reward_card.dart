import 'package:flutter/material.dart';

/// Placeholder reward card widget
class RewardCard extends StatelessWidget {
  final dynamic reward;
  final VoidCallback onRedeem;

  const RewardCard({
    Key? key,
    required this.reward,
    required this.onRedeem,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.all(8),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              reward?.title ?? 'Reward',
              style: Theme.of(context).textTheme.titleLarge,
            ),
            const SizedBox(height: 8),
            Text(
              reward?.description ?? '',
              style: Theme.of(context).textTheme.bodySmall,
            ),
            const SizedBox(height: 16),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text('${reward?.points ?? 0} points'),
                ElevatedButton(
                  onPressed: onRedeem,
                  child: const Text('Redeem'),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
