<?php

use TomatoPHP\FilamentSeo\FilamentSeoServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\Filament\AppPanelProvider::class,
    App\Providers\Filament\AppsPanelProvider::class,
    \App\Providers\TwitterServiceProvider::class,
    FilamentSeoServiceProvider::class
];
