import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../data/models/booking.dart';
import '../../../core/repositories/booking_repository.dart';

// States
abstract class BookingState {}

class BookingInitial extends BookingState {}

class BookingLoading extends BookingState {}

class BookingDetailsLoaded extends BookingState {
  final Booking booking;
  BookingDetailsLoaded(this.booking);
}

class BookingListLoaded extends BookingState {
  final List<Booking> bookings;
  BookingListLoaded(this.bookings);
}

class BookingError extends BookingState {
  final String message;
  BookingError(this.message);
}

// Events
abstract class BookingEvent {}

class LoadBookingDetails extends BookingEvent {
  final String bookingId;
  LoadBookingDetails(this.bookingId);
}

class LoadUserBookings extends BookingEvent {
  final String userId;
  LoadUserBookings(this.userId);
}

class CancelBooking extends BookingEvent {
  final String bookingId;
  CancelBooking(this.bookingId);
}

class CreateBooking extends BookingEvent {
  final String serviceId;
  final String therapistId;
  final String timeSlotId;
  final double amount;
  CreateBooking({
    required this.serviceId,
    required this.therapistId,
    required this.timeSlotId,
    required this.amount,
  });
}

// Bloc
class BookingBloc extends Bloc<BookingEvent, BookingState> {
  final BookingRepository _bookingRepository;

  BookingBloc(this._bookingRepository) : super(BookingInitial()) {
    on<LoadBookingDetails>(_onLoadBookingDetails);
    on<LoadUserBookings>(_onLoadUserBookings);
    on<CancelBooking>(_onCancelBooking);
    on<CreateBooking>(_onCreateBooking);
  }

  Future<void> _onLoadBookingDetails(
      LoadBookingDetails event, Emitter<BookingState> emit) async {
    emit(BookingLoading());
    try {
      final booking = await _bookingRepository.getBookingDetails(event.bookingId);
      emit(BookingDetailsLoaded(booking));
    } catch (e) {
      emit(BookingError(e.toString()));
    }
  }

  Future<void> _onLoadUserBookings(
      LoadUserBookings event, Emitter<BookingState> emit) async {
    emit(BookingLoading());
    try {
      final bookings = await _bookingRepository.getUserBookings(event.userId);
      emit(BookingListLoaded(bookings));
    } catch (e) {
      emit(BookingError(e.toString()));
    }
  }

  Future<void> _onCancelBooking(
      CancelBooking event, Emitter<BookingState> emit) async {
    emit(BookingLoading());
    try {
      await _bookingRepository.cancelBooking(event.bookingId);
      // After cancellation, reload to reflect updated state
      final booking = await _bookingRepository.getBookingDetails(event.bookingId);
      emit(BookingDetailsLoaded(booking));
    } catch (e) {
      emit(BookingError(e.toString()));
    }
  }

  Future<void> _onCreateBooking(
      CreateBooking event, Emitter<BookingState> emit) async {
    emit(BookingLoading());
    try {
      final booking = await _bookingRepository.createBooking(
        serviceId: event.serviceId,
        therapistId: event.therapistId,
        timeSlotId: event.timeSlotId,
        amount: event.amount,
      );
      emit(BookingDetailsLoaded(booking));
    } catch (e) {
      emit(BookingError(e.toString()));
    }
  }
}
