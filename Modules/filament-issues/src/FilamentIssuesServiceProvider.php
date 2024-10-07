<?php

namespace TomatoPHP\FilamentIssues;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;


class FilamentIssuesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //Register generate command
        $this->commands([
           \TomatoPHP\FilamentIssues\Console\FilamentIssuesInstall::class,
        ]);

        //Register Config file
        $this->mergeConfigFrom(__DIR__.'/../config/filament-issues.php', 'filament-issues');

        //Publish Config
        $this->publishes([
           __DIR__.'/../config/filament-issues.php' => config_path('filament-issues.php'),
        ], 'filament-issues-config');

        //Register Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        //Publish Migrations
        $this->publishes([
           __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'filament-issues-migrations');
        //Register views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament-issues');

        //Publish Views
        $this->publishes([
           __DIR__.'/../resources/views' => resource_path('views/vendor/filament-issues'),
        ], 'filament-issues-views');

        //Register Langs
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'filament-issues');

        //Publish Lang
        $this->publishes([
           __DIR__.'/../resources/lang' => base_path('lang/vendor/filament-issues'),
        ], 'filament-issues-lang');

        //Register Routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

    }

    public function boot(): void
    {
        RateLimiter::for('twitter', function ($job) {
            return Limit::perHour(1);
        });
    }
}
