<?php

namespace App\Providers\Filament;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use TomatoPHP\FilamentAccounts\FilamentAccountsPlugin;
use TomatoPHP\FilamentAlerts\FilamentAlertsPlugin;
use TomatoPHP\FilamentApi\FilamentAPIPlugin;
use TomatoPHP\FilamentCms\FilamentCMSPlugin;
use TomatoPHP\FilamentEcommerce\FilamentEcommercePlugin;
use TomatoPHP\FilamentLocations\FilamentLocationsPlugin;
use TomatoPHP\FilamentMediaManager\FilamentMediaManagerPlugin;
use TomatoPHP\FilamentMenus\FilamentMenusPlugin;
use TomatoPHP\FilamentNotes\FilamentNotesPlugin;
use TomatoPHP\FilamentSettingsHub\FilamentSettingsHubPlugin;
use TomatoPHP\FilamentTranslations\FilamentTranslationsPlugin;
use TomatoPHP\FilamentTranslations\FilamentTranslationsSwitcherPlugin;
use TomatoPHP\FilamentTypes\FilamentTypesPlugin;
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
            ->login()
            ->profile()
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'primary' => Color::Rose,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->favicon(asset('favicon.ico'))
            ->brandName('TomatoPHP')
            ->brandLogo(asset('tomato.png'))
            ->brandLogoHeight('80px')
            ->font(
                'IBM Plex Sans Arabic',
                provider: GoogleFontProvider::class,
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->persistentMiddleware(['universal'])
            ->domains(config('tenancy.central_domains'))
            ->databaseNotifications()
            ->plugins([
                FilamentAPIPlugin::make(),
                FilamentSettingsHubPlugin::make(),
                FilamentTypesPlugin::make(),
                FilamentMenusPlugin::make(),
                FilamentNotesPlugin::make()
                    ->useStatus()
                    ->useGroups()
                    ->useUserAccess()
                    ->useCheckList()
                    ->useShareLink()
                    ->useNotification(),
                FilamentMediaManagerPlugin::make()
                    ->allowUserAccess()
                    ->allowSubFolders(),
                FilamentCMSPlugin::make()
                    ->useThemeManager()
                    ->usePageBuilder()
                    ->useFormBuilder(),
                FilamentTranslationsPlugin::make()
                    ->allowGPTScan()
                    ->allowGoogleTranslateScan()
                    ->allowCreate()
                    ->allowCreate(),
                FilamentEcommercePlugin::make()
                    ->useWidgets(),
                FilamentAccountsPlugin::make()
                    ->useTeams()
                    ->useTypes()
                    ->useRequests()
                    ->useAvatar()
                    ->useNotifications()
                    ->useLocations()
                    ->useLoginBy()
                    ->canLogin()
                    ->canBlocked(),
                FilamentLocationsPlugin::make(),
                FilamentAlertsPlugin::make()
                    ->useDiscord()
                    ->useDatabase()
                    ->useSettingsHub(),
                FilamentUsersPlugin::make(),
                FilamentShieldPlugin::make(),
                FilamentWalletPlugin::make()
                    ->useAccounts(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
