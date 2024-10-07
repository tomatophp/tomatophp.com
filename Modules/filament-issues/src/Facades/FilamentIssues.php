<?php

namespace Modules\FilamentIssues\Facades;

use Illuminate\Support\Facades\Facade;

class FilamentIssues extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'filament-issues';
    }
}
