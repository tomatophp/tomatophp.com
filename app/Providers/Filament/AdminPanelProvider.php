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
use TomatoPHP\FilamentAccounts\FilamentAccountsPlugin;
use TomatoPHP\FilamentAlerts\FilamentAlertsPlugin;
use TomatoPHP\FilamentBookmarksMenu\FilamentBookmarksMenuPlugin;
use TomatoPHP\FilamentCms\FilamentCMSPlugin;
use TomatoPHP\FilamentDeveloperGate\FilamentDeveloperGatePlugin;
use TomatoPHP\FilamentDiscordDriver\FilamentDiscordDriverPlugin;
use TomatoPHP\FilamentDocs\FilamentDocsPlugin;
use TomatoPHP\FilamentEmployees\FilamentEmployeesPlugin;
use TomatoPHP\FilamentFormBuilder\FilamentFormBuilderPlugin;
use TomatoPHP\FilamentInvoices\FilamentInvoicesPlugin;
use TomatoPHP\FilamentIssues\FilamentIssuesPlugin;
use TomatoPHP\FilamentLanguageSwitcher\FilamentLanguageSwitcherPlugin;
use TomatoPHP\FilamentLocations\FilamentLocationsPlugin;
use TomatoPHP\FilamentMediaManager\FilamentMediaManagerPlugin;
use TomatoPHP\FilamentMenus\FilamentMenusPlugin;
use TomatoPHP\FilamentSettingsHub\FilamentSettingsHubPlugin;
use TomatoPHP\FilamentSubscriptions\FilamentSubscriptionsPlugin;
use TomatoPHP\FilamentTomatoPHPTheme\FilamentTomatoPHPThemePlugin;
use TomatoPHP\FilamentTranslations\FilamentTranslationsPlugin;
use TomatoPHP\FilamentTranslationsGoogle\FilamentTranslationsGooglePlugin;
use TomatoPHP\FilamentTranslationsGpt\FilamentTranslationsGptPlugin;
use TomatoPHP\FilamentTypes\FilamentTypesPlugin;
use TomatoPHP\FilamentTypes\Services\Contracts\Type;
use TomatoPHP\FilamentTypes\Services\Contracts\TypeFor;
use TomatoPHP\FilamentTypes\Services\Contracts\TypeOf;
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
                FilamentTypesPlugin::make()->types($this->showcaseTypes()),
                // Account import and export stay off on the public demo, like the CMS ones.
                FilamentAccountsPlugin::make()
                    ->useTypes()
                    ->useAvatar()
                    ->canLogin()
                    ->canBlocked()
                    ->showAddressField(),
                FilamentEmployeesPlugin::make(),
                FilamentLocationsPlugin::make(),
                FilamentInvoicesPlugin::make(),
                FilamentSubscriptionsPlugin::make(),
                FilamentFormBuilderPlugin::make(),
                FilamentIssuesPlugin::make(),
                FilamentDocsPlugin::make(),
                FilamentBookmarksMenuPlugin::make(),
            ]);
    }

    /**
     * Sample type groups managed by tomatophp/filament-types.
     *
     * @return array<int, TypeFor>
     */
    protected function showcaseTypes(): array
    {
        return [
            TypeFor::make('posts')
                ->label('Posts')
                ->types([
                    TypeOf::make('categories')
                        ->label('Categories')
                        ->register([
                            Type::make('news')->name('News')->icon('heroicon-o-newspaper')->color('#2563eb'),
                            Type::make('tutorials')->name('Tutorials')->icon('heroicon-o-academic-cap')->color('#7c3aed'),
                            Type::make('releases')->name('Releases')->icon('heroicon-o-rocket-launch')->color('#db2777'),
                        ]),
                ]),
            TypeFor::make('orders')
                ->label('Orders')
                ->types([
                    TypeOf::make('status')
                        ->label('Status')
                        ->register([
                            Type::make('pending')->name('Pending')->icon('heroicon-o-clock')->color('#f59e0b'),
                            Type::make('processing')->name('Processing')->icon('heroicon-o-arrow-path')->color('#0ea5e9'),
                            Type::make('shipped')->name('Shipped')->icon('heroicon-o-truck')->color('#6366f1'),
                            Type::make('delivered')->name('Delivered')->icon('heroicon-o-check-badge')->color('#16a34a'),
                            Type::make('cancelled')->name('Cancelled')->icon('heroicon-o-x-circle')->color('#dc2626'),
                        ]),
                ]),
        ];
    }
}
