<?php

namespace Modules\Cms\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Cms\Livewire\CommentPost;
use Modules\Cms\Livewire\LikePost;
use Modules\Cms\View\Components\BlogCard;
use Modules\Cms\View\Components\CategoryToolbar;
use Modules\Cms\View\Components\CommentLog;
use Modules\Cms\View\Components\EmptyState;
use Modules\Cms\View\Components\FilterToolbar;
use Modules\Cms\View\Components\LikeLog;
use Modules\Cms\View\Components\MainButton;
use Modules\Cms\View\Components\MenuItem;
use Modules\Cms\View\Components\OpenSourceCard;
use Modules\Cms\View\Components\PortfolioCard;
use Modules\Cms\View\Components\ProfileCard;
use Modules\Cms\View\Components\ServiceCard;
use Modules\Cms\View\Components\SocailIcon;
use Modules\Cms\View\Components\SocialShare;
use Modules\Cms\View\Components\SubButton;
use Modules\Cms\View\Components\UserMenu;
use TomatoPHP\FilamentCms\Facades\FilamentCMS;
use TomatoPHP\FilamentCms\Services\Contracts\Section;

class CmsServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Cms';

    protected string $moduleNameLower = 'cms';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
        $this->loadViewComponentsAs('cms', [
            MainButton::class,
            SubButton::class,
            SocailIcon::class,
            MenuItem::class,
            OpenSourceCard::class,
            BlogCard::class,
            PortfolioCard::class,
            SocialShare::class,
            ServiceCard::class,
            FilterToolbar::class,
            EmptyState::class,
            UserMenu::class,
            ProfileCard::class,
            CommentLog::class,
            LikeLog::class
        ]);

        Livewire::component('like', LikePost::class);
        Livewire::component('comment', CommentPost::class);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([module_path($this->moduleName, 'config/config.php') => config_path($this->moduleNameLower.'.php')], 'config');
        $this->mergeConfigFrom(module_path($this->moduleName, 'config/config.php'), $this->moduleNameLower);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->moduleNameLower);
        $sourcePath = module_path($this->moduleName, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->moduleNameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);

        $componentNamespace = str_replace('/', '\\', config('modules.namespace').'\\'.$this->moduleName.'\\'.ltrim(config('modules.paths.generator.component-class.path'), config('modules.paths.app_folder', '')));
        Blade::componentNamespace($componentNamespace, $this->moduleNameLower);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->moduleNameLower)) {
                $paths[] = $path.'/modules/'.$this->moduleNameLower;
            }
        }

        return $paths;
    }
}
