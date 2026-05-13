import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../data/models/service.dart';
import '../../../core/repositories/service_repository.dart';

// States
abstract class ServiceState {}

class ServiceInitial extends ServiceState {}

class ServiceLoading extends ServiceState {}

class FeaturedServicesLoaded extends ServiceState {
  final List<Service> featuredServices;
  FeaturedServicesLoaded(this.featuredServices);
}

class ServicesLoaded extends ServiceState {
  final List<Service> services;
  ServicesLoaded(this.services);
}

class ServiceError extends ServiceState {
  final String message;
  ServiceError(this.message);
}

// Events
abstract class ServiceEvent {}

class LoadFeaturedServices extends ServiceEvent {}

class LoadServices extends ServiceEvent {}

class LoadServicesByCategory extends ServiceEvent {
  final String categoryId;
  LoadServicesByCategory(this.categoryId);
}

class SearchServices extends ServiceEvent {
  final String query;
  SearchServices(this.query);
}

// Bloc
class ServiceBloc extends Bloc<ServiceEvent, ServiceState> {
  final ServiceRepository _serviceRepository;

  ServiceBloc(this._serviceRepository) : super(ServiceInitial()) {
    on<LoadFeaturedServices>(_onLoadFeaturedServices);
    on<LoadServices>(_onLoadServices);
    on<LoadServicesByCategory>(_onLoadServicesByCategory);
    on<SearchServices>(_onSearchServices);
  }

  Future<void> _onLoadFeaturedServices(
      LoadFeaturedServices event, Emitter<ServiceState> emit) async {
    emit(ServiceLoading());
    try {
      final services = await _serviceRepository.getFeaturedServices();
      emit(FeaturedServicesLoaded(services));
    } catch (e) {
      emit(ServiceError(e.toString()));
    }
  }

  Future<void> _onLoadServices(
      LoadServices event, Emitter<ServiceState> emit) async {
    emit(ServiceLoading());
    try {
      final services = await _serviceRepository.getAllServices();
      emit(ServicesLoaded(services));
    } catch (e) {
      emit(ServiceError(e.toString()));
    }
  }

  Future<void> _onLoadServicesByCategory(
      LoadServicesByCategory event, Emitter<ServiceState> emit) async {
    emit(ServiceLoading());
    try {
      final services =
          await _serviceRepository.getServicesByCategory(event.categoryId);
      emit(ServicesLoaded(services));
    } catch (e) {
      emit(ServiceError(e.toString()));
    }
  }

  Future<void> _onSearchServices(
      SearchServices event, Emitter<ServiceState> emit) async {
    emit(ServiceLoading());
    try {
      final services = await _serviceRepository.searchServices(event.query);
      emit(ServicesLoaded(services));
    } catch (e) {
      emit(ServiceError(e.toString()));
    }
  }
}
