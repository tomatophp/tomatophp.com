<?php

namespace App\Providers\Filament;

use App\Filament\Pages\AppDashboard;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use TomatoPHP\FilamentAccounts\FilamentAccountsPlugin;
use TomatoPHP\FilamentAlerts\FilamentAlertsPlugin;
use TomatoPHP\FilamentCms\FilamentCMSPlugin;
use TomatoPHP\FilamentFcm\FilamentFcmPlugin;
use TomatoPHP\FilamentLogger\FilamentLoggerPlugin;
use TomatoPHP\FilamentMediaManager\FilamentMediaManagerPlugin;
use TomatoPHP\FilamentMenus\FilamentMenusPlugin;
use TomatoPHP\FilamentMenus\Services\FilamentMenuLoader;
use TomatoPHP\FilamentNotes\FilamentNotesPlugin;
use TomatoPHP\FilamentPWA\FilamentPWAPlugin;
use TomatoPHP\FilamentSeo\FilamentSeoPlugin;
use TomatoPHP\FilamentSettingsHub\FilamentSettingsHubPlugin;
use TomatoPHP\FilamentSubscriptions\FilamentSubscriptionsProvider;
use TomatoPHP\FilamentTenancy\FilamentTenancyPlugin;
use TomatoPHP\FilamentTranslations\FilamentTranslationsPlugin;
use TomatoPHP\FilamentTranslations\FilamentTranslationsSwitcherPlugin;
use TomatoPHP\FilamentTypes\FilamentTypesPlugin;
use TomatoPHP\FilamentUsers\FilamentUsersPlugin;

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
                'Inter',
                provider: GoogleFontProvider::class,
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                AppDashboard::class,
            ])
            ->globalSearchFieldKeyBindingSuffix()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\PageViewsWidget::class,
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\VisitorsWidget::class,
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\ActiveUsersOneDayWidget::class,
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\ActiveUsersSevenDayWidget::class,
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\ActiveUsersTwentyEightDayWidget::class,
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsWidget::class,
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsByCountryWidget::class,
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsByDeviceWidget::class,
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\MostVisitedPagesWidget::class,
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\TopReferrersListWidget::class,
            ])
            ->plugin(
                FilamentTenancyPlugin::make()->allowImpersonate()->panel('app')
            )
            ->plugins([
                FilamentTypesPlugin::make(),
                FilamentMenusPlugin::make(),
                FilamentTranslationsSwitcherPlugin::make(),
                FilamentUsersPlugin::make(),
                FilamentShieldPlugin::make(),
                FilamentFcmPlugin::make(),
                FilamentPWAPlugin::make(),
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
                    ->models([
                        \App\Models\User::class => 'Admins',
                        \App\Models\Account::class => 'Accounts',
                    ])
                    ->useDiscord()
                    ->useDatabase()
                    ->useSettingsHub(),
            )
            ->plugin(
                FilamentCMSPlugin::make()
                    ->defaultLocales(['ar', 'en'])
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
                    ->useImpersonate()
                    ->impersonateRedirect('user')
                    ->useContactUs()
                    ->useTypes()
                    ->showTypeField()
                    ->useAvatar()
                    ->useNotifications()
                    ->canLogin()
                    ->canBlocked(),
            )
            ->plugin(
                FilamentLoggerPlugin::make()
            )
            ->plugin(
                FilamentSeoPlugin::make()
            )
            ->navigation(function (NavigationBuilder $builder){
                return $builder->items(FilamentMenuLoader::make('dashboard'));
            })
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
            ->databaseNotifications()
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
