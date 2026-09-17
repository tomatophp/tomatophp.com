<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use TomatoPHP\FilamentAccounts\Models\Account;
use TomatoPHP\FilamentEcommerce\Models\Branch;
use TomatoPHP\FilamentEcommerce\Models\Company;
use TomatoPHP\FilamentEcommerce\Models\Coupon;
use TomatoPHP\FilamentEcommerce\Models\Delivery;
use TomatoPHP\FilamentEcommerce\Models\GiftCard;
use TomatoPHP\FilamentEcommerce\Models\Order;
use TomatoPHP\FilamentEcommerce\Models\OrderLog;
use TomatoPHP\FilamentEcommerce\Models\OrdersItem;
use TomatoPHP\FilamentEcommerce\Models\Product;
use TomatoPHP\FilamentEcommerce\Models\ReferralCode;
use TomatoPHP\FilamentEcommerce\Models\ShippingPrice;
use TomatoPHP\FilamentEcommerce\Models\ShippingVendor;
use TomatoPHP\FilamentTypes\Models\Type;

/**
 * Demo store for tomatophp/filament-ecommerce: order statuses, payment methods and sources,
 * one company with branches (company and branch 1 are the order defaults), customer accounts
 * on the reserved example.com domain, products, orders with items and logs, coupons, gift cards,
 * referral codes and a shipping vendor with a delivery boy and prices.
 */
class DemoEcommerceSeeder extends Seeder
{
    /**
     * @var array<string, array<string, array{0: string, 1: string, 2: string, 3: string}>>
     */
    protected array $types = [
        'status' => [
            'pending' => ['Pending', 'قيد الانتظار', 'warning', 'heroicon-o-clock'],
            'prepared' => ['Prepared', 'تم التحضير', 'info', 'heroicon-o-archive-box'],
            'withdrew' => ['Withdrew', 'تم السحب', 'gray', 'heroicon-o-arrow-uturn-left'],
            'shipped' => ['Shipped', 'تم الشحن', 'info', 'heroicon-o-truck'],
            'delivered' => ['Delivered', 'تم التوصيل', 'success', 'heroicon-o-check-circle'],
            'part-delivered' => ['Part Delivered', 'تم التوصيل جزئياً', 'warning', 'heroicon-o-check'],
            'cancelled' => ['Cancelled', 'تم الإلغاء', 'danger', 'heroicon-o-x-circle'],
            'refunded' => ['Refunded', 'تم الاسترجاع', 'danger', 'heroicon-o-receipt-refund'],
            'done' => ['Done', 'تم الانتهاء', 'success', 'heroicon-o-check-badge'],
            'paid' => ['Paid', 'تم الدفع', 'success', 'heroicon-o-banknotes'],
        ],
        'payment_methods' => [
            'cash' => ['Cash', 'نقدي', 'success', 'heroicon-o-banknotes'],
            'credit' => ['Credit Card', 'بطاقة ائتمان', 'info', 'heroicon-o-credit-card'],
            'wallet' => ['Wallet', 'المحفظة', 'primary', 'heroicon-o-wallet'],
        ],
        'source' => [
            'system' => ['System', 'النظام', 'gray', 'heroicon-o-computer-desktop'],
            'website' => ['Website', 'الموقع', 'info', 'heroicon-o-globe-alt'],
            'phone' => ['Phone', 'الهاتف', 'warning', 'heroicon-o-phone'],
        ],
    ];

    /**
     * @var array<int, array{0: string, 1: string, 2: string, 3: float, 4: float}>
     */
    protected array $products = [
        ['Tomato Hoodie', 'هودي طماطم', 'TMT-HOOD', 45.00, 5.00],
        ['Filament Mug', 'كوب فيلامنت', 'FIL-MUG', 12.50, 0.00],
        ['Laravel Sticker Pack', 'ملصقات لارافيل', 'LRV-STK', 6.00, 1.00],
        ['Developer Notebook', 'دفتر المطور', 'DEV-NTB', 18.00, 2.00],
        ['Mechanical Keyboard', 'لوحة مفاتيح ميكانيكية', 'MEC-KBD', 129.00, 10.00],
        ['USB-C Dock', 'موزع USB-C', 'USB-DOCK', 79.00, 0.00],
    ];

    /**
     * @var array<int, array{0: string, 1: string, 2: string, 3: string}>
     */
    protected array $orders = [
        ['delivered', 'cash', 'website', 'Leave at the reception desk.'],
        ['pending', 'credit', 'website', 'Gift wrap, please.'],
        ['prepared', 'wallet', 'system', 'Call before delivery.'],
        ['shipped', 'cash', 'phone', 'Second floor, flat 4.'],
        ['done', 'credit', 'website', 'Invoice to the company.'],
        ['cancelled', 'cash', 'phone', 'Customer asked to cancel.'],
        ['paid', 'wallet', 'website', 'Paid from the wallet balance.'],
        ['pending', 'cash', 'system', 'Deliver in the morning.'],
    ];

    public function run(): void
    {
        foreach ($this->types as $type => $keys) {
            foreach ($keys as $key => [$english, $arabic, $color, $icon]) {
                Type::query()->updateOrCreate(
                    ['for' => 'orders', 'type' => $type, 'key' => $key],
                    ['name' => ['en' => $english, 'ar' => $arabic], 'color' => $color, 'icon' => $icon],
                );
            }
        }

        if (Order::query()->exists()) {
            return;
        }

        $company = Company::query()->firstOrCreate(['name' => 'Tomato Store'], [
            'ceo' => 'Amira Hassan',
            'address' => '12 Nile Street, Zamalek',
            'city' => 'Cairo',
            'zip' => '11511',
            'registration_number' => 'CR-104233',
            'tax_number' => 'TX-558812',
            'email' => 'store@example.com',
            'phone' => '+20 100 000 0000',
            'website' => 'https://example.com',
            'notes' => 'Demo company for the ecommerce plugin.',
        ]);

        $branches = collect([
            ['Downtown Branch', 'BR-001', '+20 100 000 0001', '5 Tahrir Square, Cairo'],
            ['Alexandria Branch', 'BR-002', '+20 100 000 0002', '21 Corniche Road, Alexandria'],
        ])->map(fn (array $branch) => Branch::query()->firstOrCreate(['name' => $branch[0]], [
            'company_id' => $company->id,
            'branch_number' => $branch[1],
            'phone' => $branch[2],
            'address' => $branch[3],
        ]));

        $accounts = collect(['Omar Khaled', 'Lina Farouk', 'Youssef Nabil', 'Sara Mahmoud', 'Karim Adel'])
            ->map(function (string $name, int $index) {
                $handle = Str::slug($name, '.');

                return Account::query()->firstOrCreate(['email' => "{$handle}.shop@example.com"], [
                    'name' => $name,
                    'username' => "{$handle}.shop",
                    'phone' => '+20 111 000 00'.str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                    'loginBy' => 'email',
                    'type' => 'account',
                    'address' => ($index + 3).' Garden City, Cairo',
                    'password' => Hash::make(Str::password(20)),
                    'is_active' => true,
                ]);
            });

        $products = collect($this->products)->map(fn (array $product) => Product::query()->firstOrCreate(['sku' => $product[2]], [
            'name' => ['en' => $product[0], 'ar' => $product[1]],
            'slug' => Str::slug($product[0]),
            'type' => 'product',
            'about' => ['en' => "The {$product[0]} from the TomatoPHP demo store.", 'ar' => $product[1]],
            'description' => ['en' => "<p>{$product[0]} made for developers who ship with Filament.</p>", 'ar' => "<p>{$product[1]}</p>"],
            'price' => $product[3],
            'discount' => $product[4],
            'vat' => 0,
            'is_in_stock' => true,
            'is_activated' => true,
            'is_shipped' => true,
            'is_trend' => $product[4] > 0,
            'has_unlimited_stock' => true,
        ]));

        $vendor = ShippingVendor::query()->firstOrCreate(['name' => 'Tomato Express'], [
            'price' => 5,
            'delivery_estimation' => '2-3 days',
            'contact_person' => 'Hany Fawzy',
            'phone' => '+20 122 000 0000',
            'address' => 'Nasr City, Cairo',
            'is_activated' => true,
        ]);

        $delivery = Delivery::query()->firstOrCreate(['phone' => '+20 122 000 0001'], [
            'shipping_vendor_id' => $vendor->id,
            'name' => 'Tarek Mansour',
            'address' => 'Nasr City, Cairo',
            'is_activated' => true,
        ]);

        ShippingPrice::query()->firstOrCreate(['shipping_vendor_id' => $vendor->id, 'type' => 'all'], [
            'delivery_id' => $delivery->id,
            'price' => 5,
        ]);

        $coupon = Coupon::query()->firstOrCreate(['code' => 'TOMATO10'], [
            'type' => 'percentage_coupon',
            'amount' => 10,
            'is_activated' => true,
            'is_limited' => false,
            'end_at' => now()->addMonths(3),
        ]);

        Coupon::query()->firstOrCreate(['code' => 'WELCOME5'], [
            'type' => 'discount_coupon',
            'amount' => 5,
            'is_activated' => true,
            'is_limited' => true,
            'use_limit' => 100,
            'use_limit_by_user' => 1,
            'end_at' => now()->addMonth(),
        ]);

        foreach ($accounts->take(3) as $index => $account) {
            GiftCard::query()->firstOrCreate(['code' => 'GIFT-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT)], [
                'account_id' => $account->id,
                'name' => "{$account->name} birthday card",
                'balance' => 25 * ($index + 1),
                'currency' => 'USD',
                'is_activated' => true,
                'is_expired' => false,
            ]);

            ReferralCode::query()->firstOrCreate(['code' => 'REF-'.strtoupper(Str::before($account->username, '.'))], [
                'account_id' => $account->id,
                'name' => "{$account->name} referral",
                'counter' => 3 * ($index + 1),
                'is_activated' => true,
                'is_public' => true,
            ]);
        }

        $cashier = User::query()->where('email', config('demo.email'))->first() ?? User::query()->first();

        foreach ($this->orders as $index => [$status, $paymentMethod, $source, $notes]) {
            $account = $accounts[$index % $accounts->count()];
            $branch = $branches[$index % $branches->count()];
            $items = $products->shuffle()->take(($index % 3) + 1)->values();
            $subtotal = 0;
            $discountTotal = 0;

            $order = Order::query()->create([
                'uuid' => 'ORD-'.now()->format('ym').'-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'company_id' => $company->id,
                'branch_id' => $branch->id,
                'account_id' => $account->id,
                'user_id' => $cashier?->id,
                'cashier_id' => $cashier?->id,
                'shipping_vendor_id' => $vendor->id,
                'shipper_id' => $delivery->id,
                'coupon_id' => $index === 1 ? $coupon->id : null,
                'type' => 'order',
                'name' => $account->name,
                'phone' => $account->phone,
                'flat' => (string) ($index + 1),
                'address' => $account->address,
                'source' => $source,
                'status' => $status,
                'payment_method' => $paymentMethod,
                'is_approved' => ! in_array($status, ['pending', 'cancelled'], true),
                'is_closed' => in_array($status, ['done', 'cancelled'], true),
                'is_payed' => in_array($status, ['paid', 'done', 'delivered'], true),
                'notes' => $notes,
                'total' => 0,
                'discount' => 0,
                'shipping' => 5,
                'vat' => 0,
                'created_at' => now()->subDays(count($this->orders) - $index)->setTime(10 + $index, 15),
                'updated_at' => now()->subDays(count($this->orders) - $index)->setTime(10 + $index, 15),
            ]);

            foreach ($items as $position => $product) {
                $quantity = $position + 1;
                $lineTotal = ($product->price - $product->discount) * $quantity;
                $subtotal += $lineTotal;
                $discountTotal += $product->discount * $quantity;

                OrdersItem::query()->create([
                    'order_id' => $order->id,
                    'account_id' => $account->id,
                    'product_id' => $product->id,
                    'item' => $product->getTranslation('name', 'en'),
                    'price' => $product->price,
                    'discount' => $product->discount,
                    'vat' => 0,
                    'qty' => $quantity,
                    'total' => $lineTotal,
                ]);
            }

            $order->update(['total' => $subtotal + 5, 'discount' => $discountTotal]);

            OrderLog::query()->create([
                'user_id' => $cashier?->id,
                'order_id' => $order->id,
                'status' => 'pending',
                'note' => 'Order created from '.$source,
            ]);

            if ($status !== 'pending') {
                OrderLog::query()->create([
                    'user_id' => $cashier?->id,
                    'order_id' => $order->id,
                    'status' => $status,
                    'note' => 'Order status changed to '.$status,
                    'is_closed' => in_array($status, ['done', 'cancelled'], true),
                ]);
            }
        }
    }
}
