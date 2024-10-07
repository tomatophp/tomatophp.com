<?php

namespace TomatoPHP\FilamentIssues;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentIssuesPlugin implements Plugin
{

    public function getId(): string
    {
        return 'filament-issues';
    }


    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
       //
    }

    public static function make(): static
    {
        return new static();
    }
}
