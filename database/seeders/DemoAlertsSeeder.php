<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TomatoPHP\FilamentAlerts\Models\NotificationsTemplate;

/**
 * Demo templates for tomatophp/filament-alerts. Database provider only: nothing leaves the server.
 */
class DemoAlertsSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Welcome',
                'key' => 'welcome',
                'title' => ['en' => 'Welcome to TomatoPHP', 'ar' => 'مرحبًا بك في TomatoPHP'],
                'body' => ['en' => 'Your account is ready. Explore the plugins from the sidebar.', 'ar' => 'حسابك جاهز. استكشف الإضافات من القائمة الجانبية.'],
                'icon' => 'heroicon-o-sparkles',
                'type' => 'success',
            ],
            [
                'name' => 'Order shipped',
                'key' => 'order-shipped',
                'title' => ['en' => 'Your order is on its way', 'ar' => 'طلبك في الطريق'],
                'body' => ['en' => 'Order #1042 left the warehouse and arrives in 2 days.', 'ar' => 'غادر الطلب رقم 1042 المستودع ويصل خلال يومين.'],
                'icon' => 'heroicon-o-truck',
                'type' => 'info',
            ],
            [
                'name' => 'Password reset',
                'key' => 'password-reset',
                'title' => ['en' => 'Password changed', 'ar' => 'تم تغيير كلمة المرور'],
                'body' => ['en' => 'If this was not you, contact support right away.', 'ar' => 'إذا لم تكن أنت، تواصل مع الدعم فورًا.'],
                'icon' => 'heroicon-o-key',
                'type' => 'warning',
            ],
        ];

        foreach ($templates as $template) {
            NotificationsTemplate::query()->updateOrCreate(
                ['key' => $template['key']],
                [...$template, 'url' => 'https://demo.tomatophp.com/admin', 'providers' => ['database'], 'action' => 'system'],
            );
        }
    }
}
