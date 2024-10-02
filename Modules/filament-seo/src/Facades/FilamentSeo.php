<?php

namespace TomatoPHP\FilamentSeo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \TomatoPHP\FilamentSeo\Services\FilamentSeoServices google()
 */
class FilamentSeo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'filament-seo';
    }
}
