<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for Firebase services.
    |
    */

    'project_id' => env('FIREBASE_PROJECT_ID'),
    'client_email' => env('FIREBASE_CLIENT_EMAIL'),
    'private_key' => env('FIREBASE_PRIVATE_KEY'),
    'database_url' => env('FIREBASE_DATABASE_URL'),
    'credentials_file' => env('FIREBASE_CREDENTIALS_FILE'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Cloud Messaging (FCM)
    |
    */

    'messaging' => [
        'server_key' => env('FIREBASE_SERVER_KEY'),
        'sender_id' => env('FIREBASE_SENDER_ID'),
        'api_key' => env('FIREBASE_API_KEY'),
        'legacy_server_key' => env('FIREBASE_LEGACY_SERVER_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Authentication Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Authentication
    |
    */

    'auth' => [
        'enabled' => env('FIREBASE_AUTH_ENABLED', false),
        'api_key' => env('FIREBASE_AUTH_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Storage
    |
    */

    'storage' => [
        'enabled' => env('FIREBASE_STORAGE_ENABLED', false),
        'bucket' => env('FIREBASE_STORAGE_BUCKET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Firestore Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Cloud Firestore
    |
    */

    'firestore' => [
        'enabled' => env('FIREBASE_FIRESTORE_ENABLED', false),
        'database' => env('FIREBASE_FIRESTORE_DATABASE', '(default)'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Realtime Database Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Realtime Database
    |
    */

    'realtime_database' => [
        'enabled' => env('FIREBASE_REALTIME_DATABASE_ENABLED', true),
        'url' => env('FIREBASE_DATABASE_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Dynamic Links Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Dynamic Links
    |
    */

    'dynamic_links' => [
        'enabled' => env('FIREBASE_DYNAMIC_LINKS_ENABLED', false),
        'domain' => env('FIREBASE_DYNAMIC_LINKS_DOMAIN'),
        'api_key' => env('FIREBASE_DYNAMIC_LINKS_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Functions Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Cloud Functions
    |
    */

    'functions' => [
        'enabled' => env('FIREBASE_FUNCTIONS_ENABLED', false),
        'region' => env('FIREBASE_FUNCTIONS_REGION', 'us-central1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Analytics Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Analytics
    |
    */

    'analytics' => [
        'enabled' => env('FIREBASE_ANALYTICS_ENABLED', false),
        'measurement_id' => env('FIREBASE_MEASUREMENT_ID'),
        'api_secret' => env('FIREBASE_ANALYTICS_API_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Performance Monitoring Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Performance Monitoring
    |
    */

    'performance' => [
        'enabled' => env('FIREBASE_PERFORMANCE_ENABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Crashlytics Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Crashlytics
    |
    */

    'crashlytics' => [
        'enabled' => env('FIREBASE_CRASHLYTICS_ENABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Remote Config Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Remote Config
    |
    */

    'remote_config' => [
        'enabled' => env('FIREBASE_REMOTE_CONFIG_ENABLED', false),
        'default_config' => [
            'maintenance_mode' => false,
            'feature_flags' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase App Distribution Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase App Distribution
    |
    */

    'app_distribution' => [
        'enabled' => env('FIREBASE_APP_DISTRIBUTION_ENABLED', false),
        'api_key' => env('FIREBASE_APP_DISTRIBUTION_API_KEY'),
        'app_id' => env('FIREBASE_APP_DISTRIBUTION_APP_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase In-App Messaging Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase In-App Messaging
    |
    */

    'in_app_messaging' => [
        'enabled' => env('FIREBASE_IN_APP_MESSAGING_ENABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase A/B Testing Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase A/B Testing
    |
    */

    'ab_testing' => [
        'enabled' => env('FIREBASE_AB_TESTING_ENABLED', false),
        'api_key' => env('FIREBASE_AB_TESTING_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Predictions Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Predictions
    |
    */

    'predictions' => [
        'enabled' => env('FIREBASE_PREDICTIONS_ENABLED', false),
        'api_key' => env('FIREBASE_PREDICTIONS_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase ML Kit Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase ML Kit
    |
    */

    'ml_kit' => [
        'enabled' => env('FIREBASE_ML_KIT_ENABLED', false),
        'api_key' => env('FIREBASE_ML_KIT_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Vision Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Vision
    |
    */

    'vision' => [
        'enabled' => env('FIREBASE_VISION_ENABLED', false),
        'api_key' => env('FIREBASE_VISION_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Natural Language Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Natural Language
    |
    */

    'natural_language' => [
        'enabled' => env('FIREBASE_NATURAL_LANGUAGE_ENABLED', false),
        'api_key' => env('FIREBASE_NATURAL_LANGUAGE_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Security Rules Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Security Rules
    |
    */

    'security_rules' => [
        'enabled' => env('FIREBASE_SECURITY_RULES_ENABLED', false),
        'api_key' => env('FIREBASE_SECURITY_RULES_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Hosting Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Hosting
    |
    */

    'hosting' => [
        'enabled' => env('FIREBASE_HOSTING_ENABLED', false),
        'site' => env('FIREBASE_HOSTING_SITE'),
        'api_key' => env('FIREBASE_HOSTING_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Extensions Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Extensions
    |
    */

    'extensions' => [
        'enabled' => env('FIREBASE_EXTENSIONS_ENABLED', false),
        'api_key' => env('FIREBASE_EXTENSIONS_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase App Check Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase App Check
    |
    */

    'app_check' => [
        'enabled' => env('FIREBASE_APP_CHECK_ENABLED', false),
        'debug_token' => env('FIREBASE_APP_CHECK_DEBUG_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Custom Authentication Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Custom Authentication
    |
    */

    'custom_auth' => [
        'enabled' => env('FIREBASE_CUSTOM_AUTH_ENABLED', false),
        'secret' => env('FIREBASE_CUSTOM_AUTH_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Multi-Factor Authentication Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Multi-Factor Authentication
    |
    */

    'mfa' => [
        'enabled' => env('FIREBASE_MFA_ENABLED', false),
        'api_key' => env('FIREBASE_MFA_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Tenant Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Tenant Management
    |
    */

    'tenant' => [
        'enabled' => env('FIREBASE_TENANT_ENABLED', false),
        'tenant_id' => env('FIREBASE_TENANT_ID'),
        'api_key' => env('FIREBASE_TENANT_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Test Lab Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Test Lab
    |
    */

    'test_lab' => [
        'enabled' => env('FIREBASE_TEST_LAB_ENABLED', false),
        'api_key' => env('FIREBASE_TEST_LAB_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase App Configuration
    |--------------------------------------------------------------------------
    |
    | General app configuration
    |
    */

    'app' => [
        'name' => env('FIREBASE_APP_NAME', env('APP_NAME')),
        'version' => env('FIREBASE_APP_VERSION', '1.0.0'),
        'environment' => env('FIREBASE_APP_ENVIRONMENT', env('APP_ENV')),
    ],
];
