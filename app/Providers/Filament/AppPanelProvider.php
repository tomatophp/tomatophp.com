<?php

namespace App\Providers\Filament;

use App\Providers\TenancyServiceProvider;
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
use TomatoPHP\FilamentNotes\FilamentNotesPlugin;
use TomatoPHP\FilamentUsers\FilamentUsersPlugin;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel
            ->id('app')
            ->path('app')
            ->login()
            ->registration()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                'universal',
                TenancyServiceProvider::TENANCY_IDENTIFICATION,
                PreventAccessFromCentralDomains::class,
            ], isPersistent: true)
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
                FilamentUsersPlugin::make(),
                FilamentNotesPlugin::make()
            ])
            ->plugin(CorePlugin::make()
                ->registerResources(false)
                ->registerPages(false)
                ->registerWidgets(false)
            )
            ->authMiddleware([
                Authenticate::class,
            ]);

        $domains = tenant()?->domains()->pluck('domain') ?? [];
        $panel->domains($domains);

        return $panel;
    }
}
