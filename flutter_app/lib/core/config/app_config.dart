class AppConfig {
  // App Information
  static const String appName = 'Ngalula Wellness Center';
  static const String appVersion = '1.0.0';
  static const String appBuildNumber = '1';
  static const String appDescription = 'Your Wellness Journey Starts Here';
  
  // Environment
  static const String environment = String.fromEnvironment(
    'ENVIRONMENT',
    defaultValue: 'development',
  );
  
  // API Configuration
  static const String baseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://localhost:8000/api',
  );
  static const String wsUrl = String.fromEnvironment(
    'WS_URL',
    defaultValue: 'ws://localhost:6001',
  );
  
  // Debug Mode
  static const bool isDebugMode = environment == 'development';
  
  // App Configuration
  static const int apiTimeout = 30; // seconds
  static const int wsTimeout = 10; // seconds
  static const int cacheTimeout = 3600; // seconds
  
  // Feature Flags
  static const bool enableNotifications = true;
  static const bool enableWebSocket = true;
  static const bool enableBiometric = true;
  static const bool enableSocialLogin = true;
  static const bool enableAnalytics = true;
  static const bool enableCrashReporting = true;
  
  // UI Configuration
  static const double defaultPadding = 16.0;
  static const double defaultRadius = 12.0;
  static const double defaultElevation = 4.0;
  
  // Animation Configuration
  static const Duration defaultAnimationDuration = Duration(milliseconds: 300);
  static const Duration fastAnimationDuration = Duration(milliseconds: 150);
  static const Duration slowAnimationDuration = Duration(milliseconds: 500);
  
  // Pagination
  static const int defaultPageSize = 20;
  static const int maxPageSize = 100;
  
  // File Upload
  static const int maxFileSize = 10 * 1024 * 1024; // 10MB
  static const List<String> supportedImageFormats = [
    'jpg',
    'jpeg',
    'png',
    'gif',
    'webp',
  ];
  
  // Cache Keys
  static const String userCacheKey = 'user_data';
  static const String tokenCacheKey = 'auth_token';
  static const String settingsCacheKey = 'app_settings';
  static const String themeCacheKey = 'theme_mode';
  static const String localeCacheKey = 'app_locale';
  
  // Storage Keys
  static const String isFirstLaunchKey = 'is_first_launch';
  static const String onboardingCompletedKey = 'onboarding_completed';
  static const String biometricEnabledKey = 'biometric_enabled';
  static const String notificationEnabledKey = 'notification_enabled';
  
  // API Endpoints
  static const String authEndpoint = '/auth';
  static const String usersEndpoint = '/users';
  static const String servicesEndpoint = '/services';
  static const String bookingsEndpoint = '/bookings';
  static const String paymentsEndpoint = '/payments';
  static const String notificationsEndpoint = '/notifications';
  static const String inventoryEndpoint = '/inventory';
  static const String therapistsEndpoint = '/therapists';
  
  // WebSocket Channels
  static const String notificationsChannel = 'notifications';
  static const String bookingsChannel = 'bookings';
  static const String inventoryChannel = 'inventory';
  static const String chatChannel = 'chat';
  
  // Error Messages
  static const String networkErrorMessage = 'Please check your internet connection';
  static const String serverErrorMessage = 'Server is temporarily unavailable';
  static const String timeoutErrorMessage = 'Request timed out';
  static const String unauthorizedErrorMessage = 'Please login to continue';
  static const String forbiddenErrorMessage = 'You don\'t have permission to access this';
  static const String notFoundErrorMessage = 'Resource not found';
  static const String validationErrorMessage = 'Please check your input';
  
  // Success Messages
  static const String loginSuccessMessage = 'Login successful';
  static const String logoutSuccessMessage = 'Logout successful';
  static const String bookingSuccessMessage = 'Booking confirmed';
  static const String paymentSuccessMessage = 'Payment successful';
  static const String profileUpdateSuccessMessage = 'Profile updated successfully';
  static const String passwordChangeSuccessMessage = 'Password changed successfully';
  
  // Validation Rules
  static const int minPasswordLength = 8;
  static const int maxPasswordLength = 128;
  static const int minNameLength = 2;
  static const int maxNameLength = 100;
  static const int minPhoneLength = 10;
  static const int maxPhoneLength = 20;
  
  // Rate Limiting
  static const int maxLoginAttempts = 5;
  static const Duration loginLockoutDuration = Duration(minutes: 15);
  static const int maxOtpRequests = 3;
  static const Duration otpLockoutDuration = Duration(minutes: 5);
  
  // Session Management
  static const Duration sessionTimeout = Duration(hours: 24);
  static const Duration tokenRefreshInterval = Duration(minutes: 55);
  static const Duration inactivityTimeout = Duration(minutes: 30);
  
  // App Store Links
  static const String appStoreUrl = 'https://apps.apple.com/app/ngalula-wellness';
  static const String playStoreUrl = 'https://play.google.com/store/apps/details?id=com.ngalula.wellness';
  
  // Support
  static const String supportEmail = 'support@ngalula.com';
  static const String supportPhone = '+1234567890';
  static const String supportWebsite = 'https://ngalula.com/support';
  
  // Social Media
  static const String facebookUrl = 'https://facebook.com/ngalulawellness';
  static const String instagramUrl = 'https://instagram.com/ngalulawellness';
  static const String twitterUrl = 'https://twitter.com/ngalulawellness';
  static const String linkedinUrl = 'https://linkedin.com/company/ngalulawellness';
  
  // Legal
  static const String privacyPolicyUrl = 'https://ngalula.com/privacy';
  static const String termsOfServiceUrl = 'https://ngalula.com/terms';
  static const String cookiePolicyUrl = 'https://ngalula.com/cookies';
  
  // Development Configuration
  static const bool enableLogging = isDebugMode;
  static const bool enableNetworkLogging = isDebugMode;
  static const bool enablePerformanceMonitoring = !isDebugMode;
  static const bool enableCrashAnalytics = !isDebugMode;
  
  // Feature Configuration
  static const Map<String, bool> features = {
    'appointments': true,
    'payments': true,
    'notifications': true,
    'chat': true,
    'inventory': true,
    'reports': true,
    'analytics': true,
    'multi_language': true,
    'dark_mode': true,
    'biometric_auth': true,
    'social_login': true,
    'qr_scanning': true,
    'location_services': true,
    'camera_access': true,
    'gallery_access': true,
    'file_upload': true,
    'push_notifications': true,
    'email_notifications': true,
    'sms_notifications': true,
    'whatsapp_notifications': true,
  };
  
  // API Version
  static const String apiVersion = 'v1';
  static const String apiHeader = 'Accept';
  static const String apiHeaderValue = 'application/json';
  
  // Security
  static const bool enableSSL = !isDebugMode;
  static const bool enableCertificatePinning = !isDebugMode;
  static const bool enableRequestSigning = !isDebugMode;
  
  // Performance
  static const int maxConcurrentRequests = 10;
  static const int maxCacheSize = 100 * 1024 * 1024; // 100MB
  static const int maxImageCacheSize = 50 * 1024 * 1024; // 50MB
  
  // Localization
  static const List<String> supportedLocales = [
    'en',
    'es',
    'fr',
    'de',
    'it',
    'pt',
  ];
  
  static const String defaultLocale = 'en';
  
  // Theme Configuration
  static const String defaultTheme = 'light';
  static const List<String> supportedThemes = [
    'light',
    'dark',
    'system',
  ];
  
  // Font Configuration
  static const String defaultFontFamily = 'Poppins';
  static const double defaultFontSize = 14.0;
  static const double titleFontSize = 24.0;
  static const double subtitleFontSize = 18.0;
  static const double captionFontSize = 12.0;
  
  // Color Configuration
  static const String primaryColor = '#6366F1';
  static const String secondaryColor = '#EC4899';
  static const String accentColor = '#10B981';
  static const String errorColor = '#EF4444';
  static const String warningColor = '#F59E0B';
  static const String infoColor = '#3B82F6';
  static const String successColor = '#10B981';
  
  // Animation Configuration
  static const Curve defaultAnimationCurve = Curves.easeInOut;
  static const Curve fastAnimationCurve = Curves.easeOut;
  static const Curve slowAnimationCurve = Curves.elasticOut;
  
  // Gesture Configuration
  static const double swipeThreshold = 50.0;
  static const double longPressThreshold = 500.0;
  static const double doubleTapThreshold = 300.0;
  
  // Accessibility
  static const bool enableAccessibility = true;
  static const bool enableHighContrast = false;
  static const bool enableLargeText = false;
  static const bool enableVoiceOver = true;
  
  // Testing Configuration
  static const bool enableTestMode = isDebugMode;
  static const String testBaseUrl = 'http://localhost:8001/api';
  static const bool enableMockData = isDebugMode;
  
  // Monitoring Configuration
  static const bool enablePerformanceMonitoring = !isDebugMode;
  static const bool enableUserBehaviorTracking = !isDebugMode;
  static const bool enableCrashReporting = !isDebugMode;
  static const bool enableNetworkMonitoring = isDebugMode;
  
  // Backup Configuration
  static const bool enableAutoBackup = true;
  static const Duration backupInterval = Duration(hours: 24);
  static const int maxBackupFiles = 7;
  
  // Update Configuration
  static const bool enableAutoUpdate = true;
  static const Duration updateCheckInterval = Duration(hours: 6);
  static const bool enableForceUpdate = false;
  
  // Analytics Configuration
  static const bool enableAnalytics = !isDebugMode;
  static const String analyticsApiKey = 'your-analytics-api-key';
  static const Duration analyticsFlushInterval = Duration(minutes: 5);
  
  // Advertisement Configuration
  static const bool enableAds = false;
  static const String adMobAppId = 'your-admob-app-id';
  static const String adMobBannerId = 'your-admob-banner-id';
  static const String adMobInterstitialId = 'your-admob-interstitial-id';
  static const String adMobRewardedId = 'your-admob-rewarded-id';
  
  // In-App Purchase Configuration
  static const bool enableInAppPurchases = true;
  static const String inAppPurchaseApiKey = 'your-in-app-purchase-api-key';
  
  // Cloud Configuration
  static const bool enableCloudSync = true;
  static const String cloudProvider = 'firebase';
  static const Duration cloudSyncInterval = Duration(minutes: 15);
  
  // Offline Configuration
  static const bool enableOfflineMode = true;
  static const Duration offlineDataExpiration = Duration(days: 7);
  static const int maxOfflineDataSize = 50 * 1024 * 1024; // 50MB
  
  // Security Configuration
  static const bool enableEncryption = true;
  static const bool enableBiometricAuth = true;
  static const bool enableTwoFactorAuth = true;
  static const Duration sessionTimeoutDuration = Duration(hours: 24);
  
  // Notification Configuration
  static const bool enablePushNotifications = true;
  static const bool enableEmailNotifications = true;
  static const bool enableSmsNotifications = true;
  static const bool enableInAppNotifications = true;
  
  // Chat Configuration
  static const bool enableChat = true;
  static const int maxMessageLength = 1000;
  static const List<String> supportedMessageTypes = [
    'text',
    'image',
    'video',
    'audio',
    'file',
    'location',
  ];
  
  // File Configuration
  static const bool enableFileUpload = true;
  static const bool enableFileDownload = true;
  static const int maxFileSizeBytes = 10 * 1024 * 1024; // 10MB
  static const List<String> supportedFileTypes = [
    'pdf',
    'doc',
    'docx',
    'xls',
    'xlsx',
    'ppt',
    'pptx',
    'txt',
    'jpg',
    'jpeg',
    'png',
    'gif',
    'mp4',
    'avi',
    'mov',
    'mp3',
    'wav',
  ];
}
