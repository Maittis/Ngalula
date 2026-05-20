import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../data/models/therapist.dart';
import '../../../core/repositories/therapist_repository.dart';

// States
abstract class TherapistState {}

class TherapistInitial extends TherapistState {}

class TherapistLoading extends TherapistState {}

class TherapistsLoaded extends TherapistState {
  final List<Therapist> therapists;
  TherapistsLoaded(this.therapists);
}

class TherapistError extends TherapistState {
  final String message;
  TherapistError(this.message);
}

// Events
abstract class TherapistEvent {}

class LoadTherapists extends TherapistEvent {
  final String? serviceId;
  LoadTherapists({this.serviceId});
}

class LoadTherapistDetails extends TherapistEvent {
  final String therapistId;
  LoadTherapistDetails(this.therapistId);
}

class RateTherapist extends TherapistEvent {
  final String therapistId;
  final double rating;
  final String? review;
  RateTherapist({
    required this.therapistId,
    required this.rating,
    this.review,
  });
}

// Bloc
class TherapistBloc extends Bloc<TherapistEvent, TherapistState> {
  final TherapistRepository _therapistRepository;

  TherapistBloc(this._therapistRepository) : super(TherapistInitial()) {
    on<LoadTherapists>(_onLoadTherapists);
    on<LoadTherapistDetails>(_onLoadTherapistDetails);
    on<RateTherapist>(_onRateTherapist);
  }

  Future<void> _onLoadTherapists(
      LoadTherapists event, Emitter<TherapistState> emit) async {
    emit(TherapistLoading());
    try {
      final therapists = event.serviceId != null
          ? await _therapistRepository.getTherapistsForService(event.serviceId!)
          : await _therapistRepository.getAllTherapists();
      emit(TherapistsLoaded(therapists));
    } catch (e) {
      emit(TherapistError(e.toString()));
    }
  }

  Future<void> _onLoadTherapistDetails(
      LoadTherapistDetails event, Emitter<TherapistState> emit) async {
    emit(TherapistLoading());
    try {
      final therapists =
          await _therapistRepository.getTherapistsForService('');
      final therapist = therapists.firstWhere(
        (t) => t.id == event.therapistId,
        orElse: () => throw Exception('Therapist not found'),
      );
      emit(TherapistsLoaded([therapist]));
    } catch (e) {
      emit(TherapistError(e.toString()));
    }
  }

  Future<void> _onRateTherapist(
      RateTherapist event, Emitter<TherapistState> emit) async {
    try {
      await _therapistRepository.rateTherapist(
        event.therapistId,
        event.rating,
        event.review,
      );
      // Reload therapists to reflect updated ratings
      add(LoadTherapists());
    } catch (e) {
      emit(TherapistError(e.toString()));
    }
  }

  // Imperative method for direct calls (used by TherapistSelectionScreen)
  Future<List<Therapist>> getTherapistsForService(String serviceId) async {
    return await _therapistRepository.getTherapistsForService(serviceId);
  }
}
