<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Filament Panels
    |--------------------------------------------------------------------------
    |
    | Here you may define the panels that will be used in your application.
    |
    */

    'panels' => [
        'admin' => [
            'id' => 'admin',
            'path' => 'admin',
            'login' => true,
            'registration' => false,
            'password_reset' => true,
            'email_verification' => false,
            'profile' => true,
            'colors' => [
                'primary' => '#6366f1',
                'secondary' => '#ec4899',
                'accent' => '#10b981',
                'danger' => '#ef4444',
                'warning' => '#f59e0b',
                'info' => '#3b82f6',
                'success' => '#10b981',
                'gray' => '#6b7280',
            ],
            'brand' => [
                'name' => env('APP_NAME', 'Ngalula Wellness Center'),
                'logo' => env('FILAMENT_LOGO', '/images/logo.png'),
                'logoHeight' => '3rem',
            ],
            'font' => 'Poppins',
            'theme' => [
                'light' => true,
                'dark' => true,
                'default' => 'light',
            ],
            'middleware' => [
                'web',
                'auth',
                'verified',
                'role:admin,super_admin,manager,inventory_manager',
            ],
            'auth' => [
                'guard' => 'web',
                'pages' => [
                    'login' => \App\Filament\Pages\Auth\Login::class,
                    'register' => \App\Filament\Pages\Auth\Register::class,
                    'requestPasswordReset' => \App\Filament\Pages\Auth\RequestPasswordReset::class,
                    'resetPassword' => \App\Filament\Pages\Auth\ResetPassword::class,
                    'editProfile' => \App\Filament\Pages\Auth\EditProfile::class,
                ],
            ],
            'resources' => [
                // User Management
                \App\Filament\Resources\UserResource::class,
                \App\Filament\Resources\RoleResource::class,
                \App\Filament\Resources\PermissionResource::class,
                
                // Core Business Resources
                \App\Filament\Resources\ServiceResource::class,
                \App\Filament\Resources\CategoryResource::class,
                \App\Filament\Resources\BookingResource::class,
                \App\Filament\Resources\PaymentResource::class,
                \App\Filament\Resources\CustomerResource::class,
                \App\Filament\Resources\TherapistResource::class,
                
                // Inventory Management
                \App\Filament\Resources\ProductResource::class,
                \App\Filament\Resources\OilResource::class,
                \App\Filament\Resources\CreamResource::class,
                \App\Filament\Resources\EquipmentResource::class,
                \App\Filament\Resources\BarcodeResource::class,
                \App\Filament\Resources\SupplierResource::class,
                \App\Filament\Resources\PurchaseRequestResource::class,
                \App\Filament\Resources\InventoryTransactionResource::class,
                \App\Filament\Resources\InventoryAlertResource::class,
                
                // Content Management
                \App\Filament\Resources\BlogResource::class,
                \App\Filament\Resources\PageResource::class,
                \App\Filament\Resources\TestimonialResource::class,
                \App\Filament\Resources\FaqResource::class,
                
                // Marketing
                \App\Filament\Resources\CouponResource::class,
                \App\Filament\Resources\GiftCardResource::class,
                \App\Filament\Resources\PromotionResource::class,
                \App\Filament\Resources\NewsletterResource::class,
                
                // System
                \App\Filament\Resources\SettingResource::class,
                \App\Filament\Resources\NotificationResource::class,
                \App\Filament\Resources\AuditLogResource::class,
            ],
            'pages' => [
                \App\Filament\Pages\Dashboard::class,
                \App\Filament\Pages\Reports\Analytics::class,
                \App\Filament\Pages\Reports\Sales::class,
                \App\Filament\Pages\Reports\Inventory::class,
                \App\Filament\Pages\Reports\Performance::class,
                \App\Filament\Pages\Settings\General::class,
                \App\Filament\Pages\Settings\Email::class,
                \App\Filament\Pages\Settings\Payment::class,
                \App\Filament\Pages\Settings\Notification::class,
                \App\Filament\Pages\Settings\Security::class,
                \App\Filament\Pages\System\Health::class,
                \App\Filament\Pages\System\Cache::class,
                \App\Filament\Pages\System\Backup::class,
                \App\Filament\Pages\System\Queue::class,
                \App\Filament\Pages\System\Schedule::class,
            ],
            'widgets' => [
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\RecentBookings::class,
                \App\Filament\Widgets\UpcomingAppointments::class,
                \App\Filament\Widgets\RevenueChart::class,
                \App\Filament\Widgets\InventoryStatus::class,
                \App\Filament\Widgets\AlertsWidget::class,
                \App\Filament\Widgets\SystemStatus::class,
            ],
            'sidebar' => [
                'collapsible' => true,
                'width' => '16rem',
                'collapsedWidth' => '4rem',
                'groups' => [
                    'dashboard' => [
                        'label' => 'Dashboard',
                        'icon' => 'heroicon-o-home',
                        'items' => [
                            'dashboard' => [
                                'label' => 'Dashboard',
                                'icon' => 'heroicon-o-chart-bar',
                                'url' => '/admin',
                            ],
                        ],
                    ],
                    'users' => [
                        'label' => 'User Management',
                        'icon' => 'heroicon-o-users',
                        'items' => [
                            'users' => [
                                'label' => 'Users',
                                'icon' => 'heroicon-o-user',
                                'url' => '/admin/users',
                            ],
                            'roles' => [
                                'label' => 'Roles',
                                'icon' => 'heroicon-o-shield-check',
                                'url' => '/admin/roles',
                            ],
                            'permissions' => [
                                'label' => 'Permissions',
                                'icon' => 'heroicon-o-key',
                                'url' => '/admin/permissions',
                            ],
                        ],
                    ],
                    'business' => [
                        'label' => 'Business',
                        'icon' => 'heroicon-o-briefcase',
                        'items' => [
                            'services' => [
                                'label' => 'Services',
                                'icon' => 'heroicon-o-sparkles',
                                'url' => '/admin/services',
                            ],
                            'categories' => [
                                'label' => 'Categories',
                                'icon' => 'heroicon-o-tag',
                                'url' => '/admin/categories',
                            ],
                            'bookings' => [
                                'label' => 'Bookings',
                                'icon' => 'heroicon-o-calendar',
                                'url' => '/admin/bookings',
                            ],
                            'payments' => [
                                'label' => 'Payments',
                                'icon' => 'heroicon-o-credit-card',
                                'url' => '/admin/payments',
                            ],
                            'customers' => [
                                'label' => 'Customers',
                                'icon' => 'heroicon-o-user-group',
                                'url' => '/admin/customers',
                            ],
                            'therapists' => [
                                'label' => 'Therapists',
                                'icon' => 'heroicon-o-academic-cap',
                                'url' => '/admin/therapists',
                            ],
                        ],
                    ],
                    'inventory' => [
                        'label' => 'Inventory',
                        'icon' => 'heroicon-o-cube',
                        'items' => [
                            'products' => [
                                'label' => 'Products',
                                'icon' => 'heroicon-o-box',
                                'url' => '/admin/products',
                            ],
                            'oils' => [
                                'label' => 'Oils',
                                'icon' => 'heroicon-o-beaker',
                                'url' => '/admin/oils',
                            ],
                            'creams' => [
                                'label' => 'Creams',
                                'icon' => 'heroicon-o-rectangle-stack',
                                'url' => '/admin/creams',
                            ],
                            'equipment' => [
                                'label' => 'Equipment',
                                'icon' => 'heroicon-o-wrench',
                                'url' => '/admin/equipment',
                            ],
                            'barcodes' => [
                                'label' => 'Barcodes',
                                'icon' => 'heroicon-o-qrcode',
                                'url' => '/admin/barcodes',
                            ],
                            'suppliers' => [
                                'label' => 'Suppliers',
                                'icon' => 'heroicon-o-truck',
                                'url' => '/admin/suppliers',
                            ],
                            'purchase-requests' => [
                                'label' => 'Purchase Requests',
                                'icon' => 'heroicon-o-shopping-cart',
                                'url' => '/admin/purchase-requests',
                            ],
                            'transactions' => [
                                'label' => 'Transactions',
                                'icon' => 'heroicon-o-arrow-path',
                                'url' => '/admin/transactions',
                            ],
                            'alerts' => [
                                'label' => 'Alerts',
                                'icon' => 'heroicon-o-bell',
                                'url' => '/admin/alerts',
                            ],
                        ],
                    ],
                    'content' => [
                        'label' => 'Content',
                        'icon' => 'heroicon-o-document-text',
                        'items' => [
                            'blog' => [
                                'label' => 'Blog',
                                'icon' => 'heroicon-o-newspaper',
                                'url' => '/admin/blog',
                            ],
                            'pages' => [
                                'label' => 'Pages',
                                'icon' => 'heroicon-o-document',
                                'url' => '/admin/pages',
                            ],
                            'testimonials' => [
                                'label' => 'Testimonials',
                                'icon' => 'heroicon-o-chat-bubble-left-right',
                                'url' => '/admin/testimonials',
                            ],
                            'faqs' => [
                                'label' => 'FAQs',
                                'icon' => 'heroicon-o-question-mark-circle',
                                'url' => '/admin/faqs',
                            ],
                        ],
                    ],
                    'marketing' => [
                        'label' => 'Marketing',
                        'icon' => 'heroicon-o-megaphone',
                        'items' => [
                            'coupons' => [
                                'label' => 'Coupons',
                                'icon' => 'heroicon-o-ticket',
                                'url' => '/admin/coupons',
                            ],
                            'gift-cards' => [
                                'label' => 'Gift Cards',
                                'icon' => 'heroicon-o-gift',
                                'url' => '/admin/gift-cards',
                            ],
                            'promotions' => [
                                'label' => 'Promotions',
                                'icon' => 'heroicon-o-sparkles',
                                'url' => '/admin/promotions',
                            ],
                            'newsletter' => [
                                'label' => 'Newsletter',
                                'icon' => 'heroicono-envelope',
                                'url' => '/admin/newsletter',
                            ],
                        ],
                    ],
                    'reports' => [
                        'label' => 'Reports',
                        'icon' => 'heroicon-o-chart-bar',
                        'items' => [
                            'analytics' => [
                                'label' => 'Analytics',
                                'icon' => 'heroicon-o-chart-line',
                                'url' => '/admin/reports/analytics',
                            ],
                            'sales' => [
                                'label' => 'Sales',
                                'icon' => 'heroicon-o-currency-dollar',
                                'url' => '/admin/reports/sales',
                            ],
                            'inventory' => [
                                'label' => 'Inventory',
                                'icon' => 'heroicon-o-cube',
                                'url' => '/admin/reports/inventory',
                            ],
                            'performance' => [
                                'label' => 'Performance',
                                'icon' => 'heroicon-o-trending-up',
                                'url' => '/admin/reports/performance',
                            ],
                        ],
                    ],
                    'settings' => [
                        'label' => 'Settings',
                        'icon' => 'heroicon-o-cog',
                        'items' => [
                            'general' => [
                                'label' => 'General',
                                'icon' => 'heroicon-o-cog',
                                'url' => '/admin/settings/general',
                            ],
                            'email' => [
                                'label' => 'Email',
                                'icon' => 'heroicon-o-envelope',
                                'url' => '/admin/settings/email',
                            ],
                            'payment' => [
                                'label' => 'Payment',
                                'icon' => 'heroicon-o-credit-card',
                                'url' => '/admin/settings/payment',
                            ],
                            'notification' => [
                                'label' => 'Notification',
                                'icon' => 'heroicon-o-bell',
                                'url' => '/admin/settings/notification',
                            ],
                            'security' => [
                                'label' => 'Security',
                                'icon' => 'heroicon-o-shield-check',
                                'url' => '/admin/settings/security',
                            ],
                        ],
                    ],
                    'system' => [
                        'label' => 'System',
                        'icon' => 'heroicon-o-server',
                        'items' => [
                            'health' => [
                                'label' => 'Health',
                                'icon' => 'heroicon-o-heart',
                                'url' => '/admin/system/health',
                            ],
                            'cache' => [
                                'label' => 'Cache',
                                'icon' => 'heroicono-arrow-path',
                                'url' => '/admin/system/cache',
                            ],
                            'backup' => [
                                'label' => 'Backup',
                                'icon' => 'heroicono-archive-box-arrow-down',
                                'url' => '/admin/system/backup',
                            ],
                            'queue' => [
                                'label' => 'Queue',
                                'icon' => 'heroicono-queue-list',
                                'url' => '/admin/system/queue',
                            ],
                            'schedule' => [
                                'label' => 'Schedule',
                                'icon' => 'heroicono-clock',
                                'url' => '/admin/system/schedule',
                            ],
                        ],
                    ],
                ],
            ],
            'navigation' => [
                'group' => 'sidebar',
                'items' => [
                    // Navigation items are defined in sidebar groups above
                ],
            ],
            'tenant' => [
                'enabled' => false,
                'attribute' => 'team_id',
                'model' => \App\Models\Team::class,
            ],
            'database_notifications' => [
                'enabled' => true,
                'polling_interval' => '30s',
                'database_connection' => null,
                'table' => 'notifications',
            ],
            'broadcasting' => [
                'enabled' => true,
                'echo' => [
                    'apps' => [
                        'main' => [
                            'key' => env('PUSHER_APP_KEY'),
                            'cluster' => env('PUSHER_APP_CLUSTER'),
                            'host' => env('PUSHER_HOST'),
                            'port' => env('PUSHER_PORT'),
                            'scheme' => env('PUSHER_SCHEME'),
                            'encrypted' => env('PUSHER_SCHEME') === 'https',
                            'useTLS' => env('PUSHER_SCHEME') === 'https',
                        ],
                    ],
                ],
            ],
            'google' => [
                'fonts' => [
                    'family' => 'Poppins',
                    'weights' => [
                        300, 400, 500, 600, 700, 800, 900,
                    ],
                ],
            ],
            'spa' => [
                'enabled' => false,
                'middleware' => [
                    'web',
                    'auth',
                    'verified',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Assets
    |--------------------------------------------------------------------------
    |
    | Here you may configure the assets that will be used in your application.
    |
    */

    'assets' => [
        'css' => [
            'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap',
        ],
        'js' => [
            // Add custom JavaScript files here
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Hooks
    |--------------------------------------------------------------------------
    |
    | Here you may configure the hooks that will be used in your application.
    |
    */

    'hooks' => [
        // Add custom hooks here
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Commands
    |--------------------------------------------------------------------------
    |
    | Here you may configure the commands that will be used in your application.
    |
    */

    'commands' => [
        // Add custom commands here
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Testing
    |--------------------------------------------------------------------------
    |
    | Here you may configure the testing settings for your application.
    |
    */

    'testing' => [
        'enabled' => env('FILAMENT_TESTING', false),
        'enable_browser_tests' => env('FILAMENT_BROWSER_TESTS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Livewire
    |--------------------------------------------------------------------------
    |
    | Here you may configure the Livewire settings for your application.
    |
    */

    'livewire' => [
        'lazy_loading' => true,
        'notification_polling' => '30s',
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Icons
    |--------------------------------------------------------------------------
    |
    | Here you may configure the icons that will be used in your application.
    |
    */

    'icons' => [
        'provider' => 'heroicons',
        'aliases' => [
            // Add custom icon aliases here
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Plugins
    |--------------------------------------------------------------------------
    |
    | Here you may configure the plugins that will be used in your application.
    |
    */

    'plugins' => [
        // Add custom plugins here
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Strict Mode
    |--------------------------------------------------------------------------
    |
    | Here you may configure the strict mode for your application.
    |
    */

    'strict' => true,

    /*
    |--------------------------------------------------------------------------
    | Filament Discover
    |--------------------------------------------------------------------------
    |
    | Here you may configure the discover settings for your application.
    |
    */

    'discover' => [
        'in' => [
            app_path('Filament'),
        ],
    ],
];
