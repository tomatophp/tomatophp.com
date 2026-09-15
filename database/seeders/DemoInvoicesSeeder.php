<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use TomatoPHP\FilamentInvoices\Facades\FilamentInvoices;
use TomatoPHP\FilamentInvoices\Models\Invoice;
use TomatoPHP\FilamentLocations\Models\Currency;

/**
 * Demo invoices for tomatophp/filament-invoices, billed from the admin to the demo users.
 * Run after DemoUsersSeeder and `php artisan filament-invoices:install`.
 */
class DemoInvoicesSeeder extends Seeder
{
    /**
     * @var array<int, array{uuid: string, type: string, status: string, paid: bool, days: int, items: array<int, array{0: string, 1: string, 2: int, 3: int}>}>
     */
    protected array $invoices = [
        ['uuid' => 'INV-2026-001', 'type' => 'sale', 'status' => 'paid', 'paid' => true, 'days' => -30, 'items' => [['Website redesign', 'Landing page and dashboard', 1, 1200], ['Hosting', '12 months', 12, 15]]],
        ['uuid' => 'INV-2026-002', 'type' => 'sale', 'status' => 'sent', 'paid' => false, 'days' => -12, 'items' => [['Mobile app', 'iOS and Android build', 1, 2400]]],
        ['uuid' => 'INV-2026-003', 'type' => 'sale', 'status' => 'overdue', 'paid' => false, 'days' => -45, 'items' => [['Support plan', 'Monthly retainer', 3, 300]]],
        ['uuid' => 'INV-2026-004', 'type' => 'estimate', 'status' => 'draft', 'paid' => false, 'days' => -2, 'items' => [['SEO audit', 'Technical and content audit', 1, 450], ['Copywriting', '10 articles', 10, 40]]],
        ['uuid' => 'INV-2026-005', 'type' => 'push', 'status' => 'paid', 'paid' => true, 'days' => -5, 'items' => [['Laptops', 'Developer laptops', 2, 1800]]],
    ];

    public function run(): void
    {
        FilamentInvoices::loadTypes();

        $from = User::query()->orderBy('id')->first();
        $customers = User::query()->orderBy('id')->get();
        $currency = Currency::query()->where('iso', 'USD')->value('id');

        if (! $from) {
            return;
        }

        foreach ($this->invoices as $index => $data) {
            $customer = $customers[($index + 1) % $customers->count()];
            $total = collect($data['items'])->sum(fn (array $item): int => $item[2] * $item[3]);

            $invoice = Invoice::query()->updateOrCreate(['uuid' => $data['uuid']], [
                'user_id' => $from->id,
                'from_type' => User::class,
                'from_id' => $from->id,
                'for_type' => User::class,
                'for_id' => $customer->id,
                'name' => $customer->name,
                'phone' => '+20 100 000 000'.$index,
                'address' => 'Cairo, Egypt',
                'type' => $data['type'],
                'status' => $data['status'],
                'currency_id' => $currency,
                'total' => $total,
                'paid' => $data['paid'] ? $total : 0,
                'discount' => 0,
                'vat' => 0,
                'shipping' => 0,
                'date' => now()->addDays($data['days']),
                'due_date' => now()->addDays($data['days'] + 30),
                'is_activated' => true,
            ]);

            $invoice->invoicesItems()->delete();

            foreach ($data['items'] as [$item, $description, $qty, $price]) {
                $invoice->invoicesItems()->create([
                    'item' => $item,
                    'description' => $description,
                    'qty' => $qty,
                    'price' => $price,
                    'discount' => 0,
                    'vat' => 0,
                    'total' => $qty * $price,
                ]);
            }
        }
    }
}
