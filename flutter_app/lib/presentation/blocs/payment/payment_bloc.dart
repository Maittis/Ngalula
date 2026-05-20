import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../data/models/payment_method.dart';
import '../../../core/repositories/payment_repository.dart';

// States
abstract class PaymentState {}

class PaymentInitial extends PaymentState {}

class PaymentLoading extends PaymentState {}

class PaymentMethodsLoaded extends PaymentState {
  final List<PaymentMethod> paymentMethods;
  PaymentMethodsLoaded(this.paymentMethods);
}

class PaymentProcessing extends PaymentState {}

class PaymentSuccess extends PaymentState {
  final String transactionId;
  PaymentSuccess(this.transactionId);
}

class PaymentError extends PaymentState {
  final String message;
  PaymentError(this.message);
}

// Events
abstract class PaymentEvent {}

class LoadPaymentMethods extends PaymentEvent {}

class AddPaymentMethod extends PaymentEvent {
  final PaymentMethod paymentMethod;
  AddPaymentMethod(this.paymentMethod);
}

class RemovePaymentMethod extends PaymentEvent {
  final String paymentMethodId;
  RemovePaymentMethod(this.paymentMethodId);
}

class ProcessPayment extends PaymentEvent {
  final String paymentMethodId;
  final String serviceId;
  final String? therapistId;
  final String? timeSlotId;
  final double amount;
  ProcessPayment({
    required this.paymentMethodId,
    required this.serviceId,
    this.therapistId,
    this.timeSlotId,
    required this.amount,
  });
}

class SetDefaultPaymentMethod extends PaymentEvent {
  final String paymentMethodId;
  SetDefaultPaymentMethod(this.paymentMethodId);
}

// Bloc
class PaymentBloc extends Bloc<PaymentEvent, PaymentState> {
  final PaymentRepository _paymentRepository;

  PaymentBloc(this._paymentRepository) : super(PaymentInitial()) {
    on<LoadPaymentMethods>(_onLoadPaymentMethods);
    on<AddPaymentMethod>(_onAddPaymentMethod);
    on<RemovePaymentMethod>(_onRemovePaymentMethod);
    on<ProcessPayment>(_onProcessPayment);
    on<SetDefaultPaymentMethod>(_onSetDefaultPaymentMethod);
  }

  Future<void> _onLoadPaymentMethods(
      LoadPaymentMethods event, Emitter<PaymentState> emit) async {
    emit(PaymentLoading());
    try {
      final methods = await _paymentRepository.getPaymentMethods();
      emit(PaymentMethodsLoaded(methods));
    } catch (e) {
      emit(PaymentError(e.toString()));
    }
  }

  Future<void> _onAddPaymentMethod(
      AddPaymentMethod event, Emitter<PaymentState> emit) async {
    emit(PaymentLoading());
    try {
      await _paymentRepository.addPaymentMethod(event.paymentMethod);
      // Reload the list after adding
      final methods = await _paymentRepository.getPaymentMethods();
      emit(PaymentMethodsLoaded(methods));
    } catch (e) {
      emit(PaymentError(e.toString()));
    }
  }

  Future<void> _onRemovePaymentMethod(
      RemovePaymentMethod event, Emitter<PaymentState> emit) async {
    emit(PaymentLoading());
    try {
      await _paymentRepository.removePaymentMethod(event.paymentMethodId);
      final methods = await _paymentRepository.getPaymentMethods();
      emit(PaymentMethodsLoaded(methods));
    } catch (e) {
      emit(PaymentError(e.toString()));
    }
  }

  Future<void> _onProcessPayment(
      ProcessPayment event, Emitter<PaymentState> emit) async {
    emit(PaymentProcessing());
    try {
      final transactionId = await _paymentRepository.processPayment(
        paymentMethodId: event.paymentMethodId,
        serviceId: event.serviceId,
        therapistId: event.therapistId,
        timeSlotId: event.timeSlotId,
        amount: event.amount,
      );
      emit(PaymentSuccess(transactionId));
    } catch (e) {
      emit(PaymentError(e.toString()));
    }
  }

  Future<void> _onSetDefaultPaymentMethod(
      SetDefaultPaymentMethod event, Emitter<PaymentState> emit) async {
    try {
      await _paymentRepository.setDefaultPaymentMethod(event.paymentMethodId);
      final methods = await _paymentRepository.getPaymentMethods();
      emit(PaymentMethodsLoaded(methods));
    } catch (e) {
      if (state is PaymentMethodsLoaded) {
        emit(state);
      } else {
        emit(PaymentError(e.toString()));
      }
    }
  }

  // Imperative method for direct calls (used by PaymentFlowScreen)
  Future<List<PaymentMethod>> getPaymentMethods() async {
    return await _paymentRepository.getPaymentMethods();
  }

  // Imperative method for direct calls (used by PaymentFlowScreen)
  Future<String> processPayment({
    required String paymentMethodId,
    required String serviceId,
    String? therapistId,
    String? timeSlotId,
    required double amount,
  }) async {
    return await _paymentRepository.processPayment(
      paymentMethodId: paymentMethodId,
      serviceId: serviceId,
      therapistId: therapistId,
      timeSlotId: timeSlotId,
      amount: amount,
    );
  }
}
