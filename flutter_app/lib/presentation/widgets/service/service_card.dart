import 'package:flutter/material.dart';

/// Placeholder service card widget
class ServiceCard extends StatelessWidget {
  final dynamic service;
  final VoidCallback onTap;

  const ServiceCard({
    Key? key,
    required this.service,
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
                service?.name ?? 'Service',
                style: Theme.of(context).textTheme.titleLarge,
              ),
              const SizedBox(height: 8),
              Text(
                service?.description ?? '',
                style: Theme.of(context).textTheme.bodySmall,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
              const SizedBox(height: 12),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    '\$${service?.price ?? 0}',
                    style: Theme.of(context).textTheme.titleMedium?.copyWith(
                          color: Colors.green,
                        ),
                  ),
                  Text('${service?.duration ?? 0} min'),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
