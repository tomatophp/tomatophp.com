<?php

namespace Modules\Core\Plugins;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\Support\Assets\Theme;
use Filament\Support\Facades\FilamentAsset;
use Modules\Core\Http\Middleware\ApplyPanelColorsMiddleware;
use Modules\Core\Http\Middleware\RedirectIfInertiaMiddleware;

class CorePlugin implements Plugin
{
    private bool $registerResources = true;
    private bool $registerPages = true;
    private bool $registerWidgets = true;

    public function getId(): string
    {
        return 'core-plugin';
    }

    public static function getNavigationGroupLabel()
    {
        return 'System Setup';
    }

    public function register(Panel $panel): void
    {
        $panel->navigationGroups([
            NavigationGroup::make(static::getNavigationGroupLabel()),
            NavigationGroup::make('Accounts')->collapsible()->collapsed(),
            NavigationGroup::make('Content')->collapsible()->collapsed(),
            NavigationGroup::make('Ordering')->collapsible()->collapsed(),
            NavigationGroup::make('Wallet')->collapsible()->collapsed(),
            NavigationGroup::make('Locations')->collapsible()->collapsed(),
            NavigationGroup::make('Translations')->collapsible()->collapsed(),
            NavigationGroup::make('Notifications')->collapsible()->collapsed(),
            NavigationGroup::make('ALC')->collapsible()->collapsed(),
            NavigationGroup::make('Settings')->collapsible()->collapsed(),
        ])
            ->navigationItems([

            ])
            ->middleware([
                RedirectIfInertiaMiddleware::class,
                ApplyPanelColorsMiddleware::class,
            ])
            ->maxContentWidth('screen-2xl');

        if ($this->shouldRegisterPages()) {
            $panel = $panel->discoverPages(in: __DIR__ . '/../Filament/Pages', for: 'Modules\\Core\\Filament\\Pages');
        }
        if ($this->shouldRegisterResources()) {
            $panel = $panel->discoverResources(in: __DIR__ . '/../Filament/Resources', for: 'Modules\\Core\\Filament\\Resources');
        }
        if ($this->shouldRegisterWidgets()) {
            $panel = $panel->discoverResources(in: __DIR__ . '/../Filament/Widgets', for: 'Modules\\Core\\Filament\\Widgets');
        }
    }

    public function boot(Panel $panel): void
    {
        /*FilamentColor::register(fn() => [
            'primary' => tenant()?->primary_color ?: Color::Indigo,
            'info' => tenant()?->secondary_color ?: Color::Amber,
        ]);*/
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function registerResources(bool $registerResources = true): static
    {
        $this->registerResources = $registerResources;
        return $this;
    }

    public function shouldRegisterResources(): bool
    {
        return $this->registerResources;
    }

    public function registerPages(bool $registerPages = true): static
    {
        $this->registerPages = $registerPages;
        return $this;
    }

    public function shouldRegisterPages(): bool
    {
        return $this->registerPages;
    }

    public function registerWidgets(bool $registerWidgets = true): static
    {
        $this->registerWidgets = $registerWidgets;
        return $this;
    }

    public function shouldRegisterWidgets(): bool
    {
        return $this->registerWidgets;
    }


}
