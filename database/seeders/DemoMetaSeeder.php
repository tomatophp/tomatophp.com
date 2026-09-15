<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo rows for tomatophp/filament-meta: a small profile on one of the demo people.
 * Requires App\Models\User to use TomatoPHP\FilamentMeta\Traits\HasMeta.
 */
class DemoMetaSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('email', 'amira.hassan@example.com')->first();

        if (! $user) {
            return;
        }

        $user->meta('job_title', 'Product Designer');
        $user->meta('department', 'Design');
        $user->meta('timezone', 'Africa/Cairo');
        $user->meta('preferences', ['theme' => 'dark', 'newsletter' => true]);
    }
}
