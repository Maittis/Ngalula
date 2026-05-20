import 'package:flutter/material.dart';

/// Placeholder search bar widget
class SearchBarWidget extends StatefulWidget {
  final Function(String) onChanged;
  final String? hintText;

  const SearchBarWidget({
    Key? key,
    required this.onChanged,
    this.hintText,
  }) : super(key: key);

  @override
  State<SearchBarWidget> createState() => _SearchBarWidgetState();
}

class _SearchBarWidgetState extends State<SearchBarWidget> {
  final TextEditingController _controller = TextEditingController();

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return TextField(
      controller: _controller,
      decoration: InputDecoration(
        hintText: widget.hintText ?? 'Search...',
        prefixIcon: const Icon(Icons.search),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
        ),
      ),
      onChanged: widget.onChanged,
    );
  }
}
