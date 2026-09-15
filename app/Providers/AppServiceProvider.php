<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stichoza\GoogleTranslate\GoogleTranslate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Public demo: the Google translate plugin rewrites text through the translator; keep it offline.
        if (config('demo.enabled')) {
            $this->app->bind(GoogleTranslate::class, fn () => new class extends GoogleTranslate
            {
                public function translate(string $string): ?string
                {
                    return $string;
                }
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
