<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Http\Middleware\LockDemoPages;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use LaraZeus\SpatieTranslatable\SpatieTranslatablePlugin;
use TomatoPHP\FilamentAlerts\FilamentAlertsPlugin;
use TomatoPHP\FilamentCms\FilamentCMSPlugin;
use TomatoPHP\FilamentDeveloperGate\FilamentDeveloperGatePlugin;
use TomatoPHP\FilamentDiscordDriver\FilamentDiscordDriverPlugin;
use TomatoPHP\FilamentLanguageSwitcher\FilamentLanguageSwitcherPlugin;
use TomatoPHP\FilamentMediaManager\FilamentMediaManagerPlugin;
use TomatoPHP\FilamentMenus\FilamentMenusPlugin;
use TomatoPHP\FilamentSettingsHub\FilamentSettingsHubPlugin;
use TomatoPHP\FilamentTomatoPHPTheme\FilamentTomatoPHPThemePlugin;
use TomatoPHP\FilamentTranslations\FilamentTranslationsPlugin;
use TomatoPHP\FilamentTranslationsGoogle\FilamentTranslationsGooglePlugin;
use TomatoPHP\FilamentTranslationsGpt\FilamentTranslationsGptPlugin;
use TomatoPHP\FilamentUsers\FilamentUsersPlugin;
use TomatoPHP\FilamentWallet\FilamentWalletPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->brandName('TomatoPHP')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                LockDemoPages::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentTomatoPHPThemePlugin::make(),
                FilamentUsersPlugin::make(),
                FilamentSettingsHubPlugin::make()->allowColorSettings(),
                FilamentDeveloperGatePlugin::make(),
                FilamentAlertsPlugin::make()->useSettingsHub(),
                FilamentDiscordDriverPlugin::make(),
                FilamentTranslationsPlugin::make()->allowCreate(),
                FilamentTranslationsGooglePlugin::make(),
                FilamentTranslationsGptPlugin::make(),
                FilamentLanguageSwitcherPlugin::make(),
                FilamentWalletPlugin::make(),
                SpatieTranslatablePlugin::make()->defaultLocales(['en', 'ar']),
                // Import, export and content import stay off on the public demo.
                FilamentCMSPlugin::make()->useCategory()->usePost(),
                FilamentMenusPlugin::make(),
                FilamentMediaManagerPlugin::make(),
            ]);
    }
}
