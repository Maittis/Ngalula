<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for WhatsApp API integration.
    |
    */

    'default' => 'twilio',

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the WhatsApp connections setup for your application.
    |
    */

    'connections' => [
        'twilio' => [
            'driver' => 'twilio',
            'sid' => env('TWILIO_SID'),
            'token' => env('TWILIO_TOKEN'),
            'from' => env('TWILIO_WHATSAPP_FROM'),
            'webhook_url' => env('TWILIO_WEBHOOK_URL'),
            'webhook_secret' => env('TWILIO_WEBHOOK_SECRET'),
        ],

        'meta' => [
            'driver' => 'meta',
            'app_id' => env('META_WHATSAPP_APP_ID'),
            'app_secret' => env('META_WHATSAPP_APP_SECRET'),
            'phone_number_id' => env('META_WHATSAPP_PHONE_NUMBER_ID'),
            'webhook_verify_token' => env('META_WHATSAPP_WEBHOOK_VERIFY_TOKEN'),
            'access_token' => env('META_WHATSAPP_ACCESS_TOKEN'),
            'webhook_url' => env('META_WHATSAPP_WEBHOOK_URL'),
        ],

        '360dialog' => [
            'driver' => '360dialog',
            'api_key' => env('WHATSAPP_360DIALOG_API_KEY'),
            'phone_number' => env('WHATSAPP_360DIALOG_PHONE_NUMBER'),
            'webhook_url' => env('WHATSAPP_360DIALOG_WEBHOOK_URL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Templates Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp message templates
    |
    */

    'templates' => [
        'appointment_reminder' => [
            'name' => 'appointment_reminder',
            'language' => 'en',
            'components' => [
                [
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => '{{customer_name}}'],
                        ['type' => 'text', 'text' => '{{appointment_time}}'],
                        ['type' => 'text', 'text' => '{{therapist_name}}'],
                    ],
                ],
            ],
        ],

        'appointment_confirmation' => [
            'name' => 'appointment_confirmation',
            'language' => 'en',
            'components' => [
                [
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => '{{customer_name}}'],
                        ['type' => 'text', 'text' => '{{appointment_date}}'],
                        ['type' => 'text', 'text' => '{{appointment_time}}'],
                        ['type' => 'text', 'text' => '{{service_name}}'],
                    ],
                ],
            ],
        ],

        'appointment_cancellation' => [
            'name' => 'appointment_cancellation',
            'language' => 'en',
            'components' => [
                [
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => '{{customer_name}}'],
                        ['type' => 'text', 'text' => '{{appointment_date}}'],
                        ['type' => 'text', 'text' => '{{reason}}'],
                    ],
                ],
            ],
        ],

        'payment_reminder' => [
            'name' => 'payment_reminder',
            'language' => 'en',
            'components' => [
                [
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => '{{customer_name}}'],
                        ['type' => 'text', 'text' => '{{amount}}'],
                        ['type' => 'text', 'text' => '{{due_date}}'],
                    ],
                ],
            ],
        ],

        'payment_confirmation' => [
            'name' => 'payment_confirmation',
            'language' => 'en',
            'components' => [
                [
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => '{{customer_name}}'],
                        ['type' => 'text', 'text' => '{{amount}}'],
                        ['type' => 'text', 'text' => '{{transaction_id}}'],
                    ],
                ],
            ],
        ],

        'welcome_message' => [
            'name' => 'welcome_message',
            'language' => 'en',
            'components' => [
                [
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => '{{customer_name}}'],
                    ],
                ],
            ],
        ],

        'promotion_message' => [
            'name' => 'promotion_message',
            'language' => 'en',
            'components' => [
                [
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => '{{customer_name}}'],
                        ['type' => 'text', 'text' => '{{promotion_name}}'],
                        ['type' => 'text', 'text' => '{{discount}}'],
                    ],
                ],
            ],
        ],

        'inventory_alert' => [
            'name' => 'inventory_alert',
            'language' => 'en',
            'components' => [
                [
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => '{{item_name}}'],
                        ['type' => 'text', 'text' => '{{stock_level}}'],
                        ['type' => 'text', 'text' => '{{action_required}}'],
                    ],
                ],
            ],
        ],

        'therapist_availability' => [
            'name' => 'therapist_availability',
            'language' => 'en',
            'components' => [
                [
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => '{{therapist_name}}'],
                        ['type' => 'text', 'text' => '{{availability_status}}'],
                        ['type' => 'text', 'text' => '{{next_available}}'],
                    ],
                ],
            ],
        ],

        'service_update' => [
            'name' => 'service_update',
            'language' => 'en',
            'components' => [
                [
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => '{{service_name}}'],
                        ['type' => 'text', 'text' => '{{update_type}}'],
                        ['type' => 'text', 'text' => '{{details}}'],
                    ],
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Business Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp Business API
    |
    */

    'business' => [
        'profile' => [
            'name' => env('WHATSAPP_BUSINESS_NAME', env('APP_NAME')),
            'description' => env('WHATSAPP_BUSINESS_DESCRIPTION', 'Wellness Center Services'),
            'email' => env('WHATSAPP_BUSINESS_EMAIL', env('MAIL_FROM_ADDRESS')),
            'website' => env('WHATSAPP_BUSINESS_WEBSITE', env('APP_URL')),
            'phone' => env('WHATSAPP_BUSINESS_PHONE'),
            'address' => env('WHATSAPP_BUSINESS_ADDRESS'),
            'categories' => env('WHATSAPP_BUSINESS_CATEGORIES', 'health_beauty'),
        ],

        'hours' => [
            'monday' => env('WHATSAPP_BUSINESS_HOURS_MONDAY', '09:00-18:00'),
            'tuesday' => env('WHATSAPP_BUSINESS_HOURS_TUESDAY', '09:00-18:00'),
            'wednesday' => env('WHATSAPP_BUSINESS_HOURS_WEDNESDAY', '09:00-18:00'),
            'thursday' => env('WHATSAPP_BUSINESS_HOURS_THURSDAY', '09:00-18:00'),
            'friday' => env('WHATSAPP_BUSINESS_HOURS_FRIDAY', '09:00-18:00'),
            'saturday' => env('WHATSAPP_BUSINESS_HOURS_SATURDAY', '09:00-17:00'),
            'sunday' => env('WHATSAPP_BUSINESS_HOURS_SUNDAY', 'closed'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Message Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp messages
    |
    */

    'messages' => [
        'max_length' => env('WHATSAPP_MAX_MESSAGE_LENGTH', 4096),
        'max_media_size' => env('WHATSAPP_MAX_MEDIA_SIZE', 16777216), // 16MB
        'supported_media_types' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'audio/mpeg',
            'audio/mp4',
            'audio/amr',
            'video/mp4',
            'video/3gpp',
            'application/pdf',
        ],
        'rate_limit' => env('WHATSAPP_RATE_LIMIT', 50), // messages per second
        'retry_attempts' => env('WHATSAPP_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('WHATSAPP_RETRY_DELAY', 1000), // milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp webhooks
    |
    */

    'webhooks' => [
        'enabled' => env('WHATSAPP_WEBHOOKS_ENABLED', true),
        'secret' => env('WHATSAPP_WEBHOOK_SECRET'),
        'events' => [
            'message_received',
            'message_sent',
            'message_delivered',
            'message_read',
            'message_failed',
            'conversation_started',
            'conversation_ended',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Analytics Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp analytics
    |
    */

    'analytics' => [
        'enabled' => env('WHATSAPP_ANALYTICS_ENABLED', true),
        'track_conversations' => env('WHATSAPP_TRACK_CONVERSATIONS', true),
        'track_messages' => env('WHATSAPP_TRACK_MESSAGES', true),
        'track_deliverability' => env('WHATSAPP_TRACK_DELIVERABILITY', true),
        'track_engagement' => env('WHATSAPP_TRACK_ENGAGEMENT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Compliance Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp compliance
    |
    */

    'compliance' => [
        'opt_in_required' => env('WHATSAPP_OPT_IN_REQUIRED', true),
        'opt_in_message' => env('WHATSAPP_OPT_IN_MESSAGE', 'Reply YES to receive messages from us'),
        'opt_out_message' => env('WHATSAPP_OPT_OUT_MESSAGE', 'Reply STOP to stop receiving messages'),
        'help_message' => env('WHATSAPP_HELP_MESSAGE', 'Reply HELP for assistance'),
        'privacy_policy_url' => env('WHATSAPP_PRIVACY_POLICY_URL'),
        'terms_of_service_url' => env('WHATSAPP_TERMS_OF_SERVICE_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp message queue
    |
    */

    'queue' => [
        'connection' => env('WHATSAPP_QUEUE_CONNECTION', 'redis'),
        'queue' => env('WHATSAPP_QUEUE_NAME', 'whatsapp'),
        'after_commit' => env('WHATSAPP_QUEUE_AFTER_COMMIT', true),
        'max_tries' => env('WHATSAPP_QUEUE_MAX_TRIES', 3),
        'retry_after' => env('WHATSAPP_QUEUE_RETRY_AFTER', 60),
        'backoff' => env('WHATSAPP_QUEUE_BACKOFF', [30, 60, 120]),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp cache
    |
    */

    'cache' => [
        'driver' => env('WHATSAPP_CACHE_DRIVER', 'redis'),
        'prefix' => env('WHATSAPP_CACHE_PREFIX', 'whatsapp'),
        'ttl' => env('WHATSAPP_CACHE_TTL', 3600), // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp logging
    |
    */

    'logging' => [
        'enabled' => env('WHATSAPP_LOGGING_ENABLED', true),
        'channel' => env('WHATSAPP_LOG_CHANNEL', 'whatsapp'),
        'level' => env('WHATSAPP_LOG_LEVEL', 'info'),
        'log_requests' => env('WHATSAPP_LOG_REQUESTS', true),
        'log_responses' => env('WHATSAPP_LOG_RESPONSES', true),
        'log_errors' => env('WHATSAPP_LOG_ERRORS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Testing Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp testing
    |
    */

    'testing' => [
        'enabled' => env('WHATSAPP_TESTING_ENABLED', false),
        'test_numbers' => explode(',', env('WHATSAPP_TEST_NUMBERS', '')),
        'mock_responses' => env('WHATSAPP_MOCK_RESPONSES', false),
        'skip_rate_limits' => env('WHATSAPP_SKIP_RATE_LIMITS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Security Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp security
    |
    */

    'security' => [
        'verify_webhooks' => env('WHATSAPP_VERIFY_WEBHOOKS', true),
        'encrypt_payloads' => env('WHATSAPP_ENCRYPT_PAYLOADS', true),
        'sign_requests' => env('WHATSAPP_SIGN_REQUESTS', true),
        'rate_limiting' => env('WHATSAPP_RATE_LIMITING', true),
        'ip_whitelist' => explode(',', env('WHATSAPP_IP_WHITELIST', '')),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Localization Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp localization
    |
    */

    'localization' => [
        'default_language' => env('WHATSAPP_DEFAULT_LANGUAGE', 'en'),
        'supported_languages' => explode(',', env('WHATSAPP_SUPPORTED_LANGUAGES', 'en,es,fr,de,it,pt')),
        'auto_translate' => env('WHATSAPP_AUTO_TRANSLATE', false),
        'translation_service' => env('WHATSAPP_TRANSLATION_SERVICE', 'google'),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Integration Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp integrations
    |
    */

    'integrations' => [
        'crm' => [
            'enabled' => env('WHATSAPP_CRM_INTEGRATION_ENABLED', false),
            'provider' => env('WHATSAPP_CRM_PROVIDER'),
            'api_key' => env('WHATSAPP_CRM_API_KEY'),
            'webhook_url' => env('WHATSAPP_CRM_WEBHOOK_URL'),
        ],

        'analytics' => [
            'enabled' => env('WHATSAPP_ANALYTICS_INTEGRATION_ENABLED', false),
            'provider' => env('WHATSAPP_ANALYTICS_PROVIDER'),
            'tracking_id' => env('WHATSAPP_ANALYTICS_TRACKING_ID'),
        ],

        'ecommerce' => [
            'enabled' => env('WHATSAPP_ECOMMERCE_INTEGRATION_ENABLED', false),
            'provider' => env('WHATSAPP_ECOMMERCE_PROVIDER'),
            'api_key' => env('WHATSAPP_ECOMMERCE_API_KEY'),
            'webhook_url' => env('WHATSAPP_ECOMMERCE_WEBHOOK_URL'),
        ],
    ],
];
