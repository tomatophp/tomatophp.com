<?php

namespace App\Providers\Filament;

use App\Providers\TenancyServiceProvider;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Facades\Filament;
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
use Modules\Core\Plugins\CorePlugin;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use TomatoPHP\FilamentAccounts\FilamentAccountsPlugin;
use TomatoPHP\FilamentAlerts\FilamentAlertsPlugin;
use TomatoPHP\FilamentApi\FilamentAPIPlugin;
use TomatoPHP\FilamentCms\FilamentCMSPlugin;
use TomatoPHP\FilamentEcommerce\FilamentEcommercePlugin;
use TomatoPHP\FilamentEmployees\FilamentEmployeesPlugin;
use TomatoPHP\FilamentFcm\FilamentFcmPlugin;
use TomatoPHP\FilamentInvoices\FilamentInvoicesPlugin;
use TomatoPHP\FilamentLocations\FilamentLocationsPlugin;
use TomatoPHP\FilamentMediaManager\FilamentMediaManagerPlugin;
use TomatoPHP\FilamentMenus\FilamentMenusPlugin;
use TomatoPHP\FilamentNotes\FilamentNotesPlugin;
use TomatoPHP\FilamentPayments\FilamentPaymentsPlugin;
use TomatoPHP\FilamentPos\FilamentPOSPlugin;
use TomatoPHP\FilamentPWA\FilamentPWAPlugin;
use TomatoPHP\FilamentSettingsHub\FilamentSettingsHubPlugin;
use TomatoPHP\FilamentSimpleTheme\FilamentSimpleThemePlugin;
use TomatoPHP\FilamentSubscriptions\Filament\Pages\Billing;
use TomatoPHP\FilamentSubscriptions\FilamentSubscriptionsPlugin;
use TomatoPHP\FilamentSubscriptions\FilamentSubscriptionsProvider;
use TomatoPHP\FilamentTenancy\FilamentTenancyAppPlugin;
use TomatoPHP\FilamentTranslations\FilamentTranslationsPlugin;
use TomatoPHP\FilamentTranslations\FilamentTranslationsSwitcherPlugin;
use TomatoPHP\FilamentTypes\FilamentTypesPlugin;
use TomatoPHP\FilamentUsers\FilamentUsersPlugin;
use TomatoPHP\FilamentWallet\FilamentWalletPlugin;
use TomatoPHP\FilamentWithdrawals\FilamentWithdrawalsPlugin;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel
            ->default()
            ->id('app')
            ->path('app')
            ->profile()
            ->databaseNotifications()
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
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                Pages\Dashboard::class,
                Billing::class
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                PreventAccessFromCentralDomains::class,
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
            ->plugins([
                FilamentAPIPlugin::make(),
                FilamentTypesPlugin::make(),
                FilamentMenusPlugin::make(),
                FilamentTranslationsSwitcherPlugin::make(),
                FilamentEcommercePlugin::make()
                    ->useWidgets(),
                FilamentLocationsPlugin::make(),
                FilamentUsersPlugin::make(),
                FilamentShieldPlugin::make(),
                FilamentWalletPlugin::make()
                    ->useAccounts(),
                FilamentFcmPlugin::make(),
                FilamentPWAPlugin::make(),
                FilamentWithdrawalsPlugin::make(),
                FilamentPOSPlugin::make(),
                FilamentInvoicesPlugin::make(),
                FilamentPaymentsPlugin::make(),
                FilamentSubscriptionsPlugin::make(),
                FilamentEmployeesPlugin::make()
            ])
            ->plugin(
                FilamentSettingsHubPlugin::make()
                    ->allowShield(),
            )
            ->plugin(
                FilamentMediaManagerPlugin::make()
                    ->allowUserAccess()
                    ->allowSubFolders(),
            )
            ->plugin(
                FilamentTranslationsPlugin::make()
                    ->allowGPTScan()
                    ->allowGoogleTranslateScan()
                    ->allowCreate()
                    ->allowCreate(),
            )
            ->plugin(
                FilamentAlertsPlugin::make()
                    ->useDiscord()
                    ->useDatabase()
                    ->useSettingsHub(),
            )
            ->plugin(
                FilamentCMSPlugin::make()
                    ->useThemeManager()
                    ->usePageBuilder()
                    ->useFormBuilder(),
            )
            ->plugin(
                FilamentNotesPlugin::make()
                    ->useStatus()
                    ->useGroups()
                    ->useUserAccess()
                    ->useCheckList()
                    ->useShareLink()
                    ->useNotification(),
            )
            ->plugin(
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
            )
            ->plugin(
                FilamentTenancyAppPlugin::make()
            )
            ->plugin(FilamentSimpleThemePlugin::make())
            ->authMiddleware([
                Authenticate::class,
            ]);

        $domains = tenant()?->domains()->pluck('domain') ?? [];
        $panel->domains($domains);


        return $panel;
    }

}
