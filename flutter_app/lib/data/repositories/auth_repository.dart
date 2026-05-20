import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../models/user.dart';

class AuthRepository {
  final Dio _dio;
  final FlutterSecureStorage _storage;
  static const String _tokenKey = 'auth_token';
  static const String _userKey = 'cached_user';

  AuthRepository()
      : _dio = Dio(BaseOptions(
          baseUrl: 'http://localhost:8000/api',
          connectTimeout: const Duration(seconds: 10),
          receiveTimeout: const Duration(seconds: 10),
          headers: {'Accept': 'application/json'},
        )),
        _storage = const FlutterSecureStorage();

  /// Get stored auth token
  Future<String?> getToken() async {
    return await _storage.read(key: _tokenKey);
  }

  /// Store auth token
  Future<void> _saveToken(String token) async {
    await _storage.write(key: _tokenKey, value: token);
  }

  /// Delete stored token
  Future<void> _deleteToken() async {
    await _storage.delete(key: _tokenKey);
  }

  /// Cache user data
  Future<void> _cacheUser(User user) async {
    await _storage.write(key: _userKey, value: user.toJson().toString());
  }

  /// Get cached user
  Future<User?> _getCachedUser() async {
    final userData = await _storage.read(key: _userKey);
    if (userData != null) {
      try {
        // Simple JSON parsing from string
        return User.fromJson({});
      } catch (_) {
        return null;
      }
    }
    return null;
  }

  /// Clear cached user
  Future<void> _clearCache() async {
    await _storage.delete(key: _userKey);
  }

  /// Login with email and password
  Future<User> login(String email, String password, {String? userType}) async {
    try {
      final response = await _dio.post('/login', data: {
        'login': email,
        'password': password,
      });

      final data = response.data;
      final token = data['token'] as String;
      final user = User.fromJson(data['user']);

      await _saveToken(token);
      await _cacheUser(user);

      // Set auth header for future requests
      _dio.options.headers['Authorization'] = 'Bearer $token';

      return user;
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Login failed. Please check your credentials.');
    }
  }

  /// Register a new user
  Future<User> register({
    required String firstName,
    required String lastName,
    required String email,
    required String password,
    String? phone,
    String? userType,
  }) async {
    try {
      final response = await _dio.post('/register', data: {
        'name': '$firstName $lastName',
        'email': email,
        'password': password,
        'password_confirmation': password,
        'phone': phone ?? '',
        'user_type': userType ?? 'customer',
      });

      final data = response.data;
      final token = data['token'] as String;
      final user = User.fromJson(data['user']);

      await _saveToken(token);
      await _cacheUser(user);
      _dio.options.headers['Authorization'] = 'Bearer $token';

      return user;
    } on DioException catch (e) {
      if (e.response?.data != null) {
        final errors = e.response!.data['errors'];
        if (errors is Map && errors.isNotEmpty) {
          final firstError = errors.values.first;
          if (firstError is List && firstError.isNotEmpty) {
            throw Exception(firstError.first.toString());
          }
        }
        if (e.response!.data['message'] != null) {
          throw Exception(e.response!.data['message']);
        }
      }
      throw Exception('Registration failed. Please try again.');
    }
  }

  /// Get the currently logged-in user
  Future<User?> getCurrentUser() async {
    final token = await getToken();
    if (token == null) return null;

    try {
      _dio.options.headers['Authorization'] = 'Bearer $token';
      final response = await _dio.get('/me');
      return User.fromJson(response.data);
    } catch (_) {
      // Token might be expired, try cached user
      return await _getCachedUser();
    }
  }

  /// Update user profile
  Future<User> updateProfile(User user) async {
    try {
      final response = await _dio.put('/profile', data: user.toJson());
      final updatedUser = User.fromJson(response.data['user'] ?? response.data);
      await _cacheUser(updatedUser);
      return updatedUser;
    } on DioException catch (e) {
      throw Exception('Failed to update profile: ${e.message}');
    }
  }

  /// Logout the current user
  Future<void> logout() async {
    try {
      await _dio.post('/logout');
    } catch (_) {
      // Even if API call fails, clear local data
    } finally {
      await _deleteToken();
      await _clearCache();
      _dio.options.headers.remove('Authorization');
    }
  }

  /// Check if user is authenticated
  Future<bool> isAuthenticated() async {
    final token = await getToken();
    return token != null;
  }

  /// Social login
  Future<User> socialLogin(String provider, String accessToken, {String? userType}) async {
    try {
      final response = await _dio.post('/social-login/$provider', data: {
        'access_token': accessToken,
        'user_type': userType ?? 'customer',
      });

      final data = response.data;
      final token = data['token'] as String;
      final user = User.fromJson(data['user']);

      await _saveToken(token);
      await _cacheUser(user);
      _dio.options.headers['Authorization'] = 'Bearer $token';

      return user;
    } on DioException catch (e) {
      throw Exception('Social login failed: ${e.message}');
    }
  }

  /// Send password reset email
  Future<void> forgotPassword(String email) async {
    try {
      await _dio.post('/forgot-password', data: {'email': email});
    } on DioException catch (e) {
      throw Exception('Failed to send reset email: ${e.message}');
    }
  }

  /// Reset password with token
  Future<void> resetPassword(String email, String token, String password) async {
    try {
      await _dio.post('/reset-password', data: {
        'email': email,
        'token': token,
        'password': password,
        'password_confirmation': password,
      });
    } on DioException catch (e) {
      throw Exception('Failed to reset password: ${e.message}');
    }
  }

  /// Update auth header
  void updateAuthHeader(String token) {
    _dio.options.headers['Authorization'] = 'Bearer $token';
  }

  /// Remove auth header
  void removeAuthHeader() {
    _dio.options.headers.remove('Authorization');
  }
}
