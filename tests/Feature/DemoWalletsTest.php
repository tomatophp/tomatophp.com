<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * tomatophp/filament-wallet in the demo: every seeded user has a funded wallet.
 */
class DemoWalletsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_seeders_give_every_user_a_wallet_with_transactions(): void
    {
        config()->set('demo.admin.email', 'admin@tomatophp.test');
        config()->set('demo.admin.password', 'Admin-Secret-123');

        $this->seed(DatabaseSeeder::class);

        $users = User::all();

        $this->assertNotEmpty($users);

        foreach ($users as $user) {
            $this->assertGreaterThan(0, $user->balanceFloatNum, "{$user->email} has no balance");
            $this->assertSame(3, $user->wallet->transactions()->count(), "{$user->email} should have 3 transactions");
        }
    }
}
