<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;
use TomatoPHP\FilamentTypes\Models\Type;

/**
 * Demo rows for tomatophp/filament-accounts (and its account types from filament-types).
 * Emails use the reserved example.com domain; no account can log in (is_login false, no password).
 */
class DemoAccountsSeeder extends Seeder
{
    /**
     * @var array<int, array{0: string, 1: string}>
     */
    protected array $accounts = [
        ['Amira Hassan', 'customer'],
        ['Omar Khaled', 'account'],
        ['Lina Farouk', 'customer'],
        ['Youssef Nabil', 'customer'],
        ['Sara Mahmoud', 'account'],
        ['Karim Adel', 'customer'],
        ['Nour El-Din', 'account'],
        ['Mariam Samir', 'customer'],
    ];

    public function run(): void
    {
        $types = [
            'customer' => [['en' => 'Customer', 'ar' => 'عميل'], 'heroicon-c-user-group', '#d91919'],
            'account' => [['en' => 'Account', 'ar' => 'حساب'], 'heroicon-c-user-circle', '#0a56d9'],
        ];

        foreach ($types as $key => [$name, $icon, $color]) {
            Type::query()->firstOrCreate(
                ['for' => 'accounts', 'type' => 'type', 'key' => $key],
                ['name' => $name, 'icon' => $icon, 'color' => $color],
            );
        }

        foreach ($this->accounts as $index => [$name, $type]) {
            $email = str($name)->slug('.').'@example.com';

            Account::query()->firstOrCreate(['email' => $email], [
                'name' => $name,
                'username' => $email,
                'phone' => '+2010000000'.str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                'type' => $type,
                'loginBy' => 'email',
                'address' => 'Cairo, Egypt',
                'is_active' => $index % 3 !== 2,
                'is_login' => false,
            ]);
        }
    }
}
