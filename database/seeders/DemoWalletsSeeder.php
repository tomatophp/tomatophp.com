<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo balances for tomatophp/filament-wallet: every user gets a wallet with a few transactions.
 */
class DemoWalletsSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->each(function (User $user, int $index): void {
            if ($user->wallet->exists && $user->wallet->transactions()->exists()) {
                return;
            }

            $user->depositFloat(250 + ($index * 75), ['description' => 'Welcome credit']);
            $user->depositFloat(120.50, ['description' => 'Top up']);
            $user->withdrawFloat(40 + ($index * 5), ['description' => 'Subscription']);
        });
    }
}
