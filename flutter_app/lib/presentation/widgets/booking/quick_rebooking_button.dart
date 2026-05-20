import 'package:flutter/material.dart';

/// Placeholder quick rebooking button widget
class QuickRebookingButton extends StatelessWidget {
  final VoidCallback onTap;
  final bool isLoading;

  const QuickRebookingButton({
    Key? key,
    required this.onTap,
    this.isLoading = false,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.green[100],
        borderRadius: BorderRadius.circular(12),
      ),
      child: InkWell(
        onTap: isLoading ? null : onTap,
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('Quick Rebook'),
                const SizedBox(height: 4),
                Text(
                  'Book the same service instantly',
                  style: Theme.of(context).textTheme.bodySmall,
                ),
              ],
            ),
            if (isLoading)
              const SizedBox(
                width: 20,
                height: 20,
                child: CircularProgressIndicator(),
              )
            else
              const Icon(Icons.arrow_forward_ios),
          ],
        ),
      ),
    );
  }
}
