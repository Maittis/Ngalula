import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../data/models/user.dart';
import '../../../data/repositories/auth_repository.dart';

// States
abstract class AuthState {}

class AuthInitial extends AuthState {}

class AuthLoading extends AuthState {}

class AuthAuthenticated extends AuthState {
  final User user;
  AuthAuthenticated(this.user);
}

class AuthUnauthenticated extends AuthState {}

class AuthError extends AuthState {
  final String message;
  AuthError(this.message);
}

// Events
abstract class AuthEvent {}

class CheckAuth extends AuthEvent {}

class Login extends AuthEvent {
  final String email;
  final String password;
  Login(this.email, this.password);
}

class Register extends AuthEvent {
  final String firstName;
  final String lastName;
  final String email;
  final String password;
  final String? phone;
  Register({
    required this.firstName,
    required this.lastName,
    required this.email,
    required this.password,
    this.phone,
  });
}

class Logout extends AuthEvent {}

class UpdateUser extends AuthEvent {
  final User user;
  UpdateUser(this.user);
}

// Bloc
class AuthBloc extends Bloc<AuthEvent, AuthState> {
  final AuthRepository _authRepository;

  AuthBloc(this._authRepository) : super(AuthInitial()) {
    on<CheckAuth>(_onCheckAuth);
    on<Login>(_onLogin);
    on<Register>(_onRegister);
    on<Logout>(_onLogout);
    on<UpdateUser>(_onUpdateUser);
  }

  Future<void> _onCheckAuth(CheckAuth event, Emitter<AuthState> emit) async {
    emit(AuthLoading());
    try {
      final user = await _authRepository.getCurrentUser();
      if (user != null) {
        emit(AuthAuthenticated(user));
      } else {
        emit(AuthUnauthenticated());
      }
    } catch (e) {
      emit(AuthUnauthenticated());
    }
  }

  Future<void> _onLogin(Login event, Emitter<AuthState> emit) async {
    emit(AuthLoading());
    try {
      final user = await _authRepository.login(event.email, event.password);
      emit(AuthAuthenticated(user));
    } catch (e) {
      emit(AuthError(e.toString()));
    }
  }

  Future<void> _onRegister(Register event, Emitter<AuthState> emit) async {
    emit(AuthLoading());
    try {
      final user = await _authRepository.register(
        firstName: event.firstName,
        lastName: event.lastName,
        email: event.email,
        password: event.password,
        phone: event.phone,
      );
      emit(AuthAuthenticated(user));
    } catch (e) {
      emit(AuthError(e.toString()));
    }
  }

  Future<void> _onLogout(Logout event, Emitter<AuthState> emit) async {
    emit(AuthLoading());
    try {
      await _authRepository.logout();
      emit(AuthUnauthenticated());
    } catch (e) {
      emit(AuthUnauthenticated());
    }
  }

  Future<void> _onUpdateUser(UpdateUser event, Emitter<AuthState> emit) async {
    emit(AuthLoading());
    try {
      await _authRepository.updateProfile(event.user);
      emit(AuthAuthenticated(event.user));
    } catch (e) {
      // Return to previous authenticated state
      if (state is AuthAuthenticated) {
        emit(state);
      } else {
        emit(AuthError(e.toString()));
      }
    }
  }
}
