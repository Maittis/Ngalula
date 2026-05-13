import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

// States
abstract class ThemeState {
  final ThemeData themeData;
  final bool isDarkMode;
  ThemeState({required this.themeData, required this.isDarkMode});
}

class LightTheme extends ThemeState {
  LightTheme()
      : super(
          themeData: ThemeData(
            brightness: Brightness.light,
            primarySwatch: Colors.teal,
            primaryColor: const Color(0xFF00897B),
            colorScheme: ColorScheme.fromSeed(
              seedColor: const Color(0xFF00897B),
              brightness: Brightness.light,
            ),
            scaffoldBackgroundColor: const Color(0xFFF5F5F5),
            appBarTheme: const AppBarTheme(
              backgroundColor: Color(0xFF00897B),
              foregroundColor: Colors.white,
              elevation: 0,
            ),
            cardTheme: CardThemeData(
              elevation: 2,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
            elevatedButtonTheme: ElevatedButtonThemeData(
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF00897B),
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
            ),
            inputDecorationTheme: InputDecorationTheme(
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(8),
              ),
              contentPadding: const EdgeInsets.symmetric(
                horizontal: 16,
                vertical: 12,
              ),
            ),
            fontFamily: 'Poppins',
          ),
          isDarkMode: false,
        );
}

class DarkTheme extends ThemeState {
  DarkTheme()
      : super(
          themeData: ThemeData(
            brightness: Brightness.dark,
            primarySwatch: Colors.teal,
            primaryColor: const Color(0xFF26A69A),
            colorScheme: ColorScheme.fromSeed(
              seedColor: const Color(0xFF26A69A),
              brightness: Brightness.dark,
            ),
            scaffoldBackgroundColor: const Color(0xFF121212),
            appBarTheme: const AppBarTheme(
              backgroundColor: Color(0xFF1E1E1E),
              foregroundColor: Colors.white,
              elevation: 0,
            ),
            cardTheme: CardThemeData(
              elevation: 2,
              color: const Color(0xFF1E1E1E),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
            elevatedButtonTheme: ElevatedButtonThemeData(
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF26A69A),
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
            ),
            inputDecorationTheme: InputDecorationTheme(
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(8),
              ),
              contentPadding: const EdgeInsets.symmetric(
                horizontal: 16,
                vertical: 12,
              ),
            ),
            fontFamily: 'Poppins',
          ),
          isDarkMode: true,
        );
}

// Events
abstract class ThemeEvent {}

class ToggleTheme extends ThemeEvent {}

class SetTheme extends ThemeEvent {
  final bool isDarkMode;
  SetTheme(this.isDarkMode);
}

// Bloc
class ThemeBloc extends Bloc<ThemeEvent, ThemeState> {
  ThemeBloc() : super(LightTheme()) {
    on<ToggleTheme>(_onToggleTheme);
    on<SetTheme>(_onSetTheme);
  }

  void _onToggleTheme(ToggleTheme event, Emitter<ThemeState> emit) {
    if (state.isDarkMode) {
      emit(LightTheme());
    } else {
      emit(DarkTheme());
    }
  }

  void _onSetTheme(SetTheme event, Emitter<ThemeState> emit) {
    if (event.isDarkMode) {
      emit(DarkTheme());
    } else {
      emit(LightTheme());
    }
  }
}
