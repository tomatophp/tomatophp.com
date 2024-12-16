<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],


    'domain' => env('CENTRAL_DOMAIN', 'localhost'),

    'packages' => [
        'filament-users' => [
            'description' => 'User Table Resource with a lot of packages integrations',
            'url' => 'https://docs.tomatophp.com/filament/filament-users',
            'key' => 'filament-users',
            'label' => 'User Manager',
            'icon' => 'heroicon-o-user',
            'permissions' => [
                'user'
            ],
        ],
        'filament-translations' => [
            'description' => 'Manage your translation with DB and cache, you can scan your language tags like trans(), __(), and get the string inside and translate them using UI.',
            'url' => 'https://docs.tomatophp.com/filament/filament-translations',
            'key' => 'filament-translations',
            'label' => 'Translation Manager',
            'icon' => 'heroicon-o-globe-alt',
            'permissions' => [
                'translation'
            ]
        ],
        'filament-notes' => [
            'description' => 'Add Sticky Notes to your FilamentPHP dashboard with tons of options and style',
            'url' => 'https://docs.tomatophp.com/filament/filament-notes',
            'key' => 'filament-notes',
            'label' => 'Sticky Notes',
            'icon' => 'heroicon-o-bookmark',
            'permissions' => [
                'note',
                'page_NotesGroups',
                'page_NotesStatus'
            ]
        ],
        'filament-types' => [
            'description' => 'Manage any type on your app in the Database with easy Resources for FilamentPHP',
            'url' => 'https://docs.tomatophp.com/filament/filament-types',
            'key' => 'filament-types',
            'label' => 'Types Manager',
            'icon' => 'heroicon-o-puzzle-piece',
            'permissions' => [
                'type'
            ]
        ],
        'filament-accounts' => [
            'description' => 'full accounts manager with API/Notifications/Contacts to manage your contacts and accounts',
            'url' => 'https://docs.tomatophp.com/filament/filament-accounts',
            'key' => 'filament-accounts',
            'label' => 'Accounts Builder',
            'icon' => 'heroicon-o-user-group',
            'permissions' => [
                'account',
                'account::request',
                'team',
                'page_AccountTypes'
            ]
        ],
        'filament-wallet' => [
            'description' => 'Account Balance / Wallets Manager For FilamentPHP and Filament Account Builder',
            'url' => 'https://docs.tomatophp.com/filament/filament-wallet',
            'key' => 'filament-wallet',
            'label' => 'Wallets Manager',
            'icon' => 'heroicon-o-arrows-up-down',
            'permissions' => [
                'wallet',
                'transaction',
            ]
        ],
        'filament-api' => [
            'description' => 'Generate APIs from your filament resource using a single line of code',
            'url' => 'https://docs.tomatophp.com/filament/filament-api',
            'key' => 'filament-api',
            'label' => 'API Generator',
            'icon' => 'heroicon-o-rectangle-stack',
            'permissions' => [
                'api'
            ]
        ],
        'filament-locations' => [
            'description' => 'Database Seeds for Locations for FilamentPHP',
            'url' => 'https://docs.tomatophp.com/filament/filament-locations',
            'key' => 'filament-locations',
            'label' => 'Locations Seeder',
            'icon' => 'heroicon-o-map',
            'permissions' => [
                'country',
                'city',
                'area',
                'location',
                'language',
                'currency',
                'page_LocationSettings'
            ]
        ],
        'filament-cms' => [
            'description' => 'Full CMS System with easy-to-use page builder & theme manager for FilamentPHP',
            'url' => 'https://docs.tomatophp.com/filament/filament-cms',
            'key' => 'filament-cms',
            'label' => 'CMS Builder',
            'icon' => 'heroicon-o-pencil',
            'permissions' => [
                'post',
                'category',
                'form',
                'page_Themes'
            ]
        ],
        'filament-ecommerce' => [
            'description' => 'Build your e-commerce store with FilamentPHP with the Power of Tomato CMS Builder',
            'url' => 'https://docs.tomatophp.com/filament/filament-ecommerce',
            'key' => 'filament-ecommerce',
            'label' => 'E-commerce Builder',
            'icon' => 'heroicon-o-shopping-bag',
            'permissions' => [
                'product',
                'order',
                'coupon',
                'company',
                'gift::card',
                'referral::code',
                'shipping::vendor',
                'page_OrderSettingsPage',
                'page_OrderStatusSettingsPage',
                'page_OrderReceiptSettingsPage',
                'widget_OrdersStateWidget',
                'widget_OrderPaymentMethodChart',
                'widget_OrderSourceChart',
                'widget_OrderStateChart'
            ]
        ],
        'filament-alerts' => [
            'description' => 'Send notifications to users using notification templates and multi-notification channels',
            'url' => 'https://docs.tomatophp.com/filament/filament-alerts',
            'key' => 'filament-alerts',
            'label' => 'Alerts Sender',
            'icon' => 'heroicon-o-fire',
            'permissions' => [
                'user::notification',
                'notifications::logs',
                'notifications::template',
                'page_NotificationsSettingsPage',
                'page_EmailSettingsPage',
            ]
        ],
        'filament-media-manager' => [
            'description' => 'Manage your media files using spatie media library with easy-to-use GUI for FilamentPHP',
            'url' => 'https://docs.tomatophp.com/filament/filament-media-manager',
            'key' => 'filament-manager',
            'label' => 'Media Manager',
            'icon' => 'heroicon-o-photo',
            'permissions' => [
                'folder',
                'media',
            ]
        ],
        'filament-menus' => [
            'description' => 'Menu Database builder to use it as a navigation on Filament Panel or as a Livewire Component',
            'url' => 'https://docs.tomatophp.com/filament/filament-menus',
            'key' => 'filament-menus',
            'label' => 'Menu Builder',
            'icon' => 'heroicon-o-bars-3',
            'permissions' => [
                'menu'
            ]
        ],
        'filament-withdrawals' => [
            'description' => 'Manage your withdrawals in Filament',
            'url' => 'https://github.com/tomatophp/filament-withdrawals',
            'key' => 'filament-withdrawals',
            'label' => 'Wallet Withdrawals',
            'icon' => 'heroicon-o-check-badge',
            'permissions' => [
                'withdrawal::request',
                'withdrawal::method'
            ]
        ],
        'filament-payments' => [
            'description' => 'Manage your payments inside FilamentPHP app with multi payment gateway integration',
            'url' => 'https://github.com/tomatophp/filament-payments',
            'key' => 'filament-payments',
            'label' => 'Payment Manager',
            'icon' => 'heroicon-o-credit-card',
            'permissions' => [
                'payment',
                'page_PaymentGateway'
            ]
        ],
        'filament-pos' => [
            'description' => 'POS System for FilamentPHP with a lot of features and integration with Ecommerce Builder',
            'url' => 'https://github.com/tomatophp/filament-pos',
            'key' => 'filament-pos',
            'label' => 'POS',
            'icon' => 'heroicon-o-receipt-percent',
            'permissions' => [
                'page_Pos',
                'widget_POSStateWidget'
            ]
        ],
        'filament-invoices' => [
            'description' => 'Generate and manage your invoices / payments using multi currencies and multi types in FilamentPHP',
            'url' => 'https://github.com/tomatophp/filament-invoices',
            'key' => 'filament-invoices',
            'label' => 'Invoice Manager',
            'icon' => 'heroicon-o-document-text',
            'permissions' => [
                'invoice',
                'page_InvoiceStatus',
            ]
        ],
        'filament-subscriptions' => [
            'description' => 'Manage subscriptions and feature access with customizable plans in FilamentPHP',
            'url' => 'https://github.com/tomatophp/filament-subscriptions',
            'key' => 'filament-subscriptions',
            'label' => 'Subscriptions Manager',
            'icon' => 'heroicon-o-currency-dollar',
            'permissions' => [
                'subscription',
                'plan',
            ]
        ],
        'filament-employees' => [
            'description' => 'Manage your employees with easy using Account builder and FilamentPHP',
            'url' => 'https://github.com/tomatophp/filament-employees',
            'key' => 'filament-employees',
            'label' => 'Employees Manager',
            'icon' => 'heroicon-o-user-circle',
            'permissions' => [
                'page_EmployeeApplyStatus',
                'page_Departments',
                'page_EmployeePaymentsType',
                'page_EmployeePaymentsStatus',
                'page_EmployeeRequestsStatus',
                'employee::apply',
                'attendance::shift',
            ]
        ],
        'filament-docs' => [
            'description' => 'Manage your documents and contracts all in one place with template builder',
            'url' => 'https://github.com/tomatophp/filament-docs',
            'key' => 'filament-docs',
            'label' => 'Documents Editor',
            'icon' => 'heroicon-o-document',
            'permissions' => [
                'document',
                'document::template',
            ]
        ],
    ]
];
