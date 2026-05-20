import 'package:flutter/material.dart';

/// Placeholder countdown widget
class CountdownWidget extends StatefulWidget {
  final DateTime? targetDate;
  final String? targetTime;

  const CountdownWidget({
    Key? key,
    this.targetDate,
    this.targetTime,
  }) : super(key: key);

  @override
  State<CountdownWidget> createState() => _CountdownWidgetState();
}

class _CountdownWidgetState extends State<CountdownWidget> {
  late Timer _timer;
  late Duration _duration;

  @override
  void initState() {
    super.initState();
    _calculateDuration();
    _timer = Timer.periodic(const Duration(seconds: 1), (_) {
      _calculateDuration();
      setState(() {});
    });
  }

  void _calculateDuration() {
    if (widget.targetDate != null) {
      final now = DateTime.now();
      _duration = widget.targetDate!.difference(now);
    } else {
      _duration = Duration.zero;
    }
  }

  @override
  void dispose() {
    _timer.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final hours = _duration.inHours % 24;
    final minutes = _duration.inMinutes % 60;
    final seconds = _duration.inSeconds % 60;

    return Center(
      child: Text(
        '${hours.toString().padLeft(2, '0')}:${minutes.toString().padLeft(2, '0')}:${seconds.toString().padLeft(2, '0')}',
        style: Theme.of(context).textTheme.headlineMedium?.copyWith(
              fontWeight: FontWeight.bold,
              color: Colors.blue,
            ),
      ),
    );
  }
}
