<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Public Demo
    |--------------------------------------------------------------------------
    |
    | When enabled, the login page is prefilled with the demo account, the demo
    | and admin accounts cannot be changed, deleted or impersonated from the
    | panel, and the database is reset from the seeders every hour.
    |
    */

    'enabled' => (bool) env('DEMO_MODE', false),

    'name' => env('DEMO_NAME', 'Demo User'),

    'email' => env('DEMO_EMAIL', 'demo@tomatophp.com'),

    'password' => env('DEMO_PASSWORD', 'demo1234'),

    /*
    | Pages that store credentials (SMTP, webhooks, API keys). In demo mode they redirect
    | to the dashboard with a notice. Class strings, so a plugin that is not installed is fine.
    */
    'locked_pages' => [
        'TomatoPHP\FilamentAlerts\Filament\Pages\EmailSettingsPage',
        'TomatoPHP\FilamentDiscordDriver\Filament\Pages\DiscordSettingsPage',
        'TomatoPHP\FilamentFcmDriver\Filament\Pages\FcmSettingsPage',
        // Invoice mail sender and templates.
        'TomatoPHP\FilamentInvoices\Pages\InvoiceSettingsPage',
    ],

    'admin' => [
        'name' => env('DEMO_ADMIN_NAME', 'TomatoPHP Admin'),
        'email' => env('DEMO_ADMIN_EMAIL'),
        'password' => env('DEMO_ADMIN_PASSWORD'),
    ],

];
