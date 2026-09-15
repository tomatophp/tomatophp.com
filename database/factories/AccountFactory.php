<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * Factory for the account model published from tomatophp/filament-accounts.
 *
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        $email = fake()->unique()->safeEmail();

        return [
            'name' => fake()->name(),
            'type' => 'account',
            'address' => fake()->address(),
            'phone' => fake()->unique()->e164PhoneNumber(),
            'email' => $email,
            'username' => $email,
            'loginBy' => 'email',
            'password' => Hash::make(fake()->password(12)),
            'is_active' => true,
        ];
    }

    public function employee(): static
    {
        return $this->state(['type' => 'employee']);
    }
}
