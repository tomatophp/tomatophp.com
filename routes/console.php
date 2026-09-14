<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Public demo: rebuild the database from the seeders every hour.
\Illuminate\Support\Facades\Schedule::command('migrate:fresh', ['--seed' => true, '--force' => true])
    ->hourly()
    ->when(fn (): bool => (bool) config('demo.enabled'))
    ->withoutOverlapping()
    // migrate:fresh empties the cache table that holds the icon list used by the icon picker.
    ->after(fn () => \Illuminate\Support\Facades\Artisan::call("filament-icons:install"));
