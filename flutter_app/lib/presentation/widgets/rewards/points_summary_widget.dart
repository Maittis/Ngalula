import 'package:flutter/material.dart';

/// Placeholder point summary widget
class PointsSummaryWidget extends StatelessWidget {
  const PointsSummaryWidget({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.blue[50],
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Your Points',
            style: Theme.of(context).textTheme.titleLarge,
          ),
          const SizedBox(height: 16),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Available'),
                  Text('1,250', style: Theme.of(context).textTheme.headlineSmall),
                ],
              ),
              Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  const Text('Lifetime'),
                  Text('5,000', style: Theme.of(context).textTheme.headlineSmall),
                ],
              ),
            ],
          ),
        ],
      ),
    );
  }
}
