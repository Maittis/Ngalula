import 'package:flutter/material.dart';

/// Placeholder filter chip widget
class FilterChipWidget extends StatefulWidget {
  final List<String> options;
  final Function(String) onSelected;
  final String? selectedValue;

  const FilterChipWidget({
    Key? key,
    required this.options,
    required this.onSelected,
    this.selectedValue,
  }) : super(key: key);

  @override
  State<FilterChipWidget> createState() => _FilterChipWidgetState();
}

class _FilterChipWidgetState extends State<FilterChipWidget> {
  late String _selectedValue;

  @override
  void initState() {
    super.initState();
    _selectedValue = widget.selectedValue ?? (widget.options.isNotEmpty ? widget.options.first : '');
  }

  @override
  Widget build(BuildContext context) {
    return Wrap(
      spacing: 8,
      children: widget.options
          .map((option) => FilterChip(
                label: Text(option),
                selected: _selectedValue == option,
                onSelected: (_) {
                  setState(() {
                    _selectedValue = option;
                  });
                  widget.onSelected(option);
                },
              ))
          .toList(),
    );
  }
}
