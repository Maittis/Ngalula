import 'package:flutter/material.dart';

/// Placeholder filter chip widget
class FilterChip extends StatelessWidget {
  final String label;
  final bool selected;
  final VoidCallback onSelected;

  const FilterChip({
    Key? key,
    required this.label,
    required this.selected,
    required this.onSelected,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Material(
      child: Chip(
        label: Text(label),
        backgroundColor: selected ? Colors.blue : Colors.grey[200],
        labelStyle: TextStyle(
          color: selected ? Colors.white : Colors.black,
        ),
        onDeleted: selected ? onSelected : null,
      ),
    );
  }
}
