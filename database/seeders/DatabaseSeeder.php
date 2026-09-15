<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the demo application. Every installed tomatophp package adds its demo seeder here.
     */
    public function run(): void
    {
        $this->call([
            DemoUsersSeeder::class,
            DemoCategoriesSeeder::class,
            DemoAlertsSeeder::class,
            DemoTranslationsSeeder::class,
            DemoWalletsSeeder::class,
        ]);
    }
}
