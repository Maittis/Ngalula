import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/repositories/notification_repository.dart';

// States
abstract class NotificationState {}

class NotificationInitial extends NotificationState {}

class NotificationLoading extends NotificationState {}

class NotificationReady extends NotificationState {}

class NotificationError extends NotificationState {
  final String message;
  NotificationError(this.message);
}

// Events
abstract class NotificationEvent {}

class InitializeNotifications extends NotificationEvent {}

class ScheduleNotification extends NotificationEvent {
  final String title;
  final String body;
  final DateTime scheduledDate;
  ScheduleNotification({
    required this.title,
    required this.body,
    required this.scheduledDate,
  });
}

class CancelNotification extends NotificationEvent {
  final String notificationId;
  CancelNotification(this.notificationId);
}

class MarkNotificationRead extends NotificationEvent {
  final String notificationId;
  MarkNotificationRead(this.notificationId);
}

// Bloc
class NotificationBloc extends Bloc<NotificationEvent, NotificationState> {
  final NotificationRepository _notificationRepository;

  NotificationBloc(this._notificationRepository) : super(NotificationInitial()) {
    on<InitializeNotifications>(_onInitialize);
    on<ScheduleNotification>(_onSchedule);
    on<CancelNotification>(_onCancel);
    on<MarkNotificationRead>(_onMarkRead);
  }

  Future<void> _onInitialize(
      InitializeNotifications event, Emitter<NotificationState> emit) async {
    emit(NotificationLoading());
    try {
      await _notificationRepository.initialize();
      emit(NotificationReady());
    } catch (e) {
      emit(NotificationError(e.toString()));
    }
  }

  Future<void> _onSchedule(
      ScheduleNotification event, Emitter<NotificationState> emit) async {
    emit(NotificationLoading());
    try {
      await _notificationRepository.scheduleNotification(
        title: event.title,
        body: event.body,
        scheduledDate: event.scheduledDate,
      );
      emit(NotificationReady());
    } catch (e) {
      emit(NotificationError(e.toString()));
    }
  }

  Future<void> _onCancel(
      CancelNotification event, Emitter<NotificationState> emit) async {
    try {
      await _notificationRepository.cancelNotification(event.notificationId);
    } catch (_) {}
  }

  Future<void> _onMarkRead(
      MarkNotificationRead event, Emitter<NotificationState> emit) async {
    try {
      await _notificationRepository.markAsRead(event.notificationId);
    } catch (_) {}
  }
}
