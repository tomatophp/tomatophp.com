<?php

namespace App\Providers\Filament;

use App\Filament\Apps\Pages\Login;
use App\Filament\Apps\Pages\Register;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
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
use TomatoPHP\FilamentAccounts\Filament\Pages\EditProfile;
use TomatoPHP\FilamentAccounts\FilamentAccountsSaaSPlugin;
use TomatoPHP\FilamentFcm\FilamentFcmPlugin;
use TomatoPHP\FilamentNotes\Filament\Widgets\NotesWidget;
use TomatoPHP\FilamentNotes\FilamentNotesPlugin;
use TomatoPHP\FilamentTranslations\FilamentTranslationsSwitcherPlugin;
use TomatoPHP\FilamentTranslations\Http\Middleware\LanguageMiddleware;

class AppsPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('user')
            ->path('user')
            ->authGuard('accounts')
            ->databaseNotifications()
            ->login(Login::class)
            ->registration(Register::class)
            ->profile()
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'primary' => Color::Rose,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->topNavigation()
            ->domain(config('filament-tenancy.central_domain'))
            ->favicon(asset('favicon.ico'))
            ->brandName('TomatoPHP')
            ->brandLogo(asset('tomato.png'))
            ->brandLogoHeight('80px')
            ->homeUrl(url('/'))
            ->font(
                'IBM Plex Sans Arabic',
                provider: GoogleFontProvider::class,
            )
            ->userMenuItems([
                "profile" => MenuItem::make()
                    ->label('Profile')
                    ->icon('heroicon-o-user')
                    ->url(fn() => \App\Filament\Apps\Pages\EditProfile::getUrl()),
                "public" => MenuItem::make()
                    ->visible(fn()=>(bool)auth('accounts')->user()->meta('is_public'))
                    ->label('Public Profile')
                    ->icon('heroicon-o-globe-alt')
                    ->url(fn() => url( '/@' . auth('accounts')->user()->username)),
            ])
            ->discoverResources(in: app_path('Filament/Apps/Resources'), for: 'App\\Filament\\Apps\\Resources')
            ->discoverPages(in: app_path('Filament/Apps/Pages'), for: 'App\\Filament\\Apps\\Pages')
            ->pages([
                Pages\Dashboard::class
            ])
            ->discoverWidgets(in: app_path('Filament/Apps/Widgets'), for: 'App\\Filament\\Apps\\Widgets')
            ->plugin(
                FilamentNotesPlugin::make()
            )
            ->widgets([
                NotesWidget::class
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
                LanguageMiddleware::class

            ])
            ->plugin(
                FilamentFcmPlugin::make()
            )
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
