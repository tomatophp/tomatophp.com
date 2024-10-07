<?php

use TomatoPHP\FilamentSeo\FilamentSeoServiceProvider;
use TomatoPHP\FilamentIssues\FilamentIssuesServiceProvider;
use TomatoPHP\FilamentSocial\FilamentSocialServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\Filament\AppPanelProvider::class,
    App\Providers\Filament\AppsPanelProvider::class,
    \App\Providers\TwitterServiceProvider::class,
    FilamentSeoServiceProvider::class,
    FilamentIssuesServiceProvider::class,
    FilamentSocialServiceProvider::class
];
