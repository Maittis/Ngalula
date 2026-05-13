import 'package:flutter/material.dart';

/// Placeholder search bar widget
class SearchBar extends StatefulWidget {
  final Function(String) onChanged;
  final String? hintText;

  const SearchBar({
    Key? key,
    required this.onChanged,
    this.hintText,
  }) : super(key: key);

  @override
  State<SearchBar> createState() => _SearchBarState();
}

class _SearchBarState extends State<SearchBar> {
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
