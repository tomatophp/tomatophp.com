<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Laravelcm\Subscriptions\Models\Plan;
use Laravelcm\Subscriptions\Services\Period;

/**
 * Demo plans with features for tomatophp/filament-subscriptions, and the demo user on the Pro plan.
 * Needs the laravel-subscriptions migrations (php artisan vendor:publish --provider="Laravelcm\Subscriptions\SubscriptionServiceProvider").
 */
class DemoSubscriptionsSeeder extends Seeder
{
    /**
     * @var array<int, array{slug: string, name: array<string, string>, description: array<string, string>, price: int, features: array<string, string>}>
     */
    protected array $plans = [
        [
            'slug' => 'starter',
            'name' => ['en' => 'Starter', 'ar' => 'المبتدئ'],
            'description' => ['en' => 'For side projects and small teams.', 'ar' => 'للمشاريع الصغيرة والفرق الصغيرة.'],
            'price' => 9,
            'features' => ['Projects' => '3', 'Team members' => '2', 'Storage (GB)' => '5'],
        ],
        [
            'slug' => 'pro',
            'name' => ['en' => 'Pro', 'ar' => 'المحترف'],
            'description' => ['en' => 'For growing businesses.', 'ar' => 'للأعمال النامية.'],
            'price' => 29,
            'features' => ['Projects' => '25', 'Team members' => '10', 'Storage (GB)' => '100'],
        ],
        [
            'slug' => 'business',
            'name' => ['en' => 'Business', 'ar' => 'الأعمال'],
            'description' => ['en' => 'Unlimited projects and priority support.', 'ar' => 'مشاريع غير محدودة ودعم أولوية.'],
            'price' => 99,
            'features' => ['Projects' => 'unlimited', 'Team members' => 'unlimited', 'Storage (GB)' => '1000'],
        ],
    ];

    public function run(): void
    {
        foreach ($this->plans as $order => $data) {
            $plan = Plan::query()->firstOrNew(['slug' => $data['slug']]);
            $plan->fill([
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'signup_fee' => 0,
                'currency' => 'USD',
                'invoice_period' => 1,
                'invoice_interval' => 'month',
                'trial_period' => 14,
                'trial_interval' => 'day',
                'is_active' => true,
                'sort_order' => $order + 1,
            ])->save();

            foreach ($data['features'] as $name => $value) {
                $plan->features()->updateOrCreate(
                    ['slug' => $data['slug'].'-'.str($name)->slug()],
                    ['name' => ['en' => $name], 'value' => $value, 'resettable_period' => 1, 'resettable_interval' => 'month'],
                );
            }
        }

        $demo = User::query()->where('email', config('demo.email', 'demo@tomatophp.com'))->first();
        $pro = Plan::query()->where('slug', 'pro')->first();

        if ($demo && $pro && method_exists($demo, 'planSubscriptions') && ! $demo->planSubscriptions()->exists()) {
            // DatabaseSeeder runs without model events, so the slug the package generates on
            // creating is set here. Dates follow newPlanSubscription(): the trial, then one period.
            $trial = new Period($pro->trial_interval, $pro->trial_period, now());
            $period = new Period($pro->invoice_interval, $pro->invoice_period, $trial->getEndDate());

            $demo->planSubscriptions()->create([
                'name' => 'main',
                'slug' => 'main',
                'plan_id' => $pro->getKey(),
                'trial_ends_at' => $trial->getEndDate(),
                'starts_at' => $period->getStartDate(),
                'ends_at' => $period->getEndDate(),
            ]);
        }
    }
}
