<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TomatoPHP\FilamentTranslations\Services\SaveScan;

/**
 * Re-imports the app's translation strings for tomatophp/filament-translations after each demo reset.
 */
class DemoTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        (new SaveScan)->save();
    }
}
