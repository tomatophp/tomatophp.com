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


    'domain' => env('APP_BASE_DOMAIN', 'localhost'),

    'packages' => [
        'filament-users' => [
            'key' => 'filament-users',
            'label' => 'User Manager',
            'icon' => 'heroicon-o-user',
            'permissions' => [
                'user'
            ],
        ],
        'filament-translations' => [
            'key' => 'filament-translations',
            'label' => 'Translation Manager',
            'icon' => 'heroicon-o-language',
            'permissions' => [
                'translation'
            ]
        ],
        'filament-notes' => [
            'key' => 'filament-notes',
            'label' => 'Sticky Notes',
            'icon' => 'heroicon-o-document-text',
            'permissions' => [
                'note',
                'page_NotesGroups',
                'page_NotesStatus'
            ]
        ],
        'filament-types' => [
            'key' => 'filament-types',
            'label' => 'Types Manager',
            'icon' => 'heroicon-o-rectangle-stack',
            'permissions' => [
                'type'
            ]
        ],
        'filament-accounts' => [
            'key' => 'filament-accounts',
            'label' => 'Accounts Builder',
            'icon' => 'heroicon-o-user-group',
            'permissions' => [
                'account',
                'account::request',
                'team',
            ]
        ],
        'filament-api' => [
            'key' => 'filament-api',
            'label' => 'API Generator',
            'icon' => 'heroicon-o-share',
            'permissions' => [
                'api'
            ]
        ],
        'filament-locations' => [
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
            'key' => 'filament-cms',
            'label' => 'CMS Builder',
            'icon' => 'heroicon-o-book-open',
            'permissions' => [
                'post',
                'category',
                'form',
                'page_Themes'
            ]
        ],
        'filament-ecommerce' => [
            'key' => 'filament-ecommerce',
            'label' => 'E-commerce Builder',
            'icon' => 'heroicon-o-shopping-cart',
            'permissions' => [
                'product',
                'order',
                'coupon',
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
            'key' => 'filament-alerts',
            'label' => 'Alerts Sender',
            'icon' => 'heroicon-o-bell',
            'permissions' => [
                'user::notification',
                'notifications::logs',
                'notifications::template',
                'page_NotificationsSettingsPage',
                'page_EmailSettingsPage',
            ]
        ],
        'filament-wallet' => [
            'key' => 'filament-wallet',
            'label' => 'Wallets Manager',
            'icon' => 'heroicon-o-wallet',
            'permissions' => [
                'wallet',
                'transaction',
            ]
        ],
        'filament-media-manager' => [
            'key' => 'filament-manager',
            'label' => 'Media Manager',
            'icon' => 'heroicon-o-photo',
            'permissions' => [
                'folder',
                'media',
            ]
        ],
        'filament-menus' => [
            'key' => 'filament-menus',
            'label' => 'Menu Builder',
            'icon' => 'heroicon-o-bars-3',
            'permissions' => [
                'menu'
            ]
        ],
    ]
];
