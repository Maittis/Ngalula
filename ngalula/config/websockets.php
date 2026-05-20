<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default WebSocket Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default WebSocket driver that will be used.
    | You can switch between 'pusher' and 'laravel-websockets' drivers.
    |
    */

    'default' => env('WEBSOCKET_DRIVER', 'laravel-websockets'),

    /*
    |--------------------------------------------------------------------------
    | Laravel WebSockets Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for Laravel WebSockets.
    |
    */

    'apps' => [
        [
            'id' => env('PUSHER_APP_ID'),
            'name' => env('APP_NAME'),
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'path' => env('PUSHER_APP_PATH', 'app'),
            'capacity' => null,
            'enable_client_messages' => false,
            'enable_statistics' => true,
            'allowed_origins' => [
                env('APP_URL'),
                'http://localhost:3000',
                'http://localhost:8000',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel WebSockets Server Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for the WebSocket server.
    |
    */

    'port' => env('WEBSOCKET_PORT', 6001),
    'host' => env('WEBSOCKET_HOST', '0.0.0.0'),
    'ssl' => [
        'local_cert' => env('WEBSOCKET_SSL_LOCAL_CERT', null),
        'local_pk' => env('WEBSOCKET_SSL_LOCAL_PK', null),
        'passphrase' => env('WEBSOCKET_SSL_PASSPHRASE', null),
    ],
    'max_request_size' => env('WEBSOCKET_MAX_REQUEST_SIZE', 10000),
    'max_payload_size' => env('WEBSOCKET_MAX_PAYLOAD_SIZE', 1000000),

    /*
    |--------------------------------------------------------------------------
    | Laravel WebSockets Statistics Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for the WebSocket statistics.
    |
    */

    'statistics' => [
        'enabled' => env('WEBSOCKET_STATISTICS_ENABLED', true),
        'interval' => env('WEBSOCKET_STATISTICS_INTERVAL', 60),
        'store' => env('WEBSOCKET_STATISTICS_STORE', 'database'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel WebSockets Debugging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for debugging.
    |
    */

    'debug' => env('WEBSOCKET_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Laravel WebSockets Route Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for the WebSocket routes.
    |
    */

    'route' => [
        'middleware' => ['web', 'auth'],
        'prefix' => 'laravel-websockets',
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel WebSockets Replication Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for replication.
    |
    */

    'replication' => [
        'enabled' => env('WEBSOCKET_REPLICATION_ENABLED', false),
        'driver' => env('WEBSOCKET_REPLICATION_DRIVER', 'redis'),
        'redis' => [
            'connection' => env('WEBSOCKET_REPLICATION_REDIS_CONNECTION', 'default'),
            'prefix' => env('WEBSOCKET_REPLICATION_REDIS_PREFIX', 'websockets'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel WebSockets API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for the WebSocket API.
    |
    */

    'api' => [
        'middleware' => ['web', 'auth'],
        'prefix' => 'api/websockets',
    ],
];
