<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoUsersSeeder extends Seeder
{
    /**
     * Sample people shown in the users resource. Emails use the reserved example.com domain.
     *
     * @var array<int, array{0: string, 1: string}>
     */
    protected array $people = [
        ['Amira Hassan', 'amira.hassan'],
        ['Omar Khaled', 'omar.khaled'],
        ['Lina Farouk', 'lina.farouk'],
        ['Youssef Nabil', 'youssef.nabil'],
        ['Sara Mahmoud', 'sara.mahmoud'],
        ['Karim Adel', 'karim.adel'],
        ['Nour El-Din', 'nour.eldin'],
        ['Mariam Samir', 'mariam.samir'],
        ['Hany Fawzy', 'hany.fawzy'],
        ['Dina Wagdy', 'dina.wagdy'],
        ['Tarek Mansour', 'tarek.mansour'],
        ['Farida Ashraf', 'farida.ashraf'],
    ];

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => config('demo.email')],
            ['name' => config('demo.name'), 'password' => config('demo.password'), 'email_verified_at' => now()],
        );

        if (filled(config('demo.admin.email')) && filled(config('demo.admin.password'))) {
            User::query()->updateOrCreate(
                ['email' => config('demo.admin.email')],
                ['name' => config('demo.admin.name'), 'password' => config('demo.admin.password'), 'email_verified_at' => now()],
            );
        }

        foreach ($this->people as [$name, $handle]) {
            User::query()->firstOrCreate(
                ['email' => "{$handle}@example.com"],
                ['name' => $name, 'password' => Str::password(20), 'email_verified_at' => now()],
            );
        }
    }
}
