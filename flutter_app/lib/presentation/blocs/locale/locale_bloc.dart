import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

// States
abstract class LocaleState {
  final Locale locale;
  LocaleState(this.locale);
}

class EnglishLocale extends LocaleState {
  EnglishLocale() : super(const Locale('en', 'US'));
}

class SpanishLocale extends LocaleState {
  SpanishLocale() : super(const Locale('es', 'ES'));
}

class FrenchLocale extends LocaleState {
  FrenchLocale() : super(const Locale('fr', 'FR'));
}

class GermanLocale extends LocaleState {
  GermanLocale() : super(const Locale('de', 'DE'));
}

class ItalianLocale extends LocaleState {
  ItalianLocale() : super(const Locale('it', 'IT'));
}

class PortugueseLocale extends LocaleState {
  PortugueseLocale() : super(const Locale('pt', 'BR'));
}

// Events
abstract class LocaleEvent {}

class SetLocale extends LocaleEvent {
  final String languageCode;
  final String? countryCode;
  SetLocale(this.languageCode, {this.countryCode});
}

class ToggleToEnglish extends LocaleEvent {}

class ToggleToSpanish extends LocaleEvent {}

class ToggleToFrench extends LocaleEvent {}

// Bloc
class LocaleBloc extends Bloc<LocaleEvent, LocaleState> {
  LocaleBloc() : super(EnglishLocale()) {
    on<SetLocale>(_onSetLocale);
    on<ToggleToEnglish>((_, emit) => emit(EnglishLocale()));
    on<ToggleToSpanish>((_, emit) => emit(SpanishLocale()));
    on<ToggleToFrench>((_, emit) => emit(FrenchLocale()));
  }

  void _onSetLocale(SetLocale event, Emitter<LocaleState> emit) {
    switch (event.languageCode) {
      case 'es':
        emit(SpanishLocale());
        break;
      case 'fr':
        emit(FrenchLocale());
        break;
      case 'de':
        emit(GermanLocale());
        break;
      case 'it':
        emit(ItalianLocale());
        break;
      case 'pt':
        emit(PortugueseLocale());
        break;
      default:
        emit(EnglishLocale());
    }
  }
}
