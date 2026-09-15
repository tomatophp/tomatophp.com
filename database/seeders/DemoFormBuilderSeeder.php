<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TomatoPHP\FilamentFormBuilder\Models\Form;

/**
 * Demo data for tomatophp/filament-form-builder: a contact form with fields and one stored request.
 */
class DemoFormBuilderSeeder extends Seeder
{
    public function run(): void
    {
        $form = Form::query()->updateOrCreate(['key' => 'contact-us'], [
            'type' => 'page',
            'title' => ['en' => 'Contact us', 'ar' => 'تواصل معنا'],
            'description' => ['en' => 'Send a message to the TomatoPHP team.', 'ar' => 'أرسل رسالة إلى فريق TomatoPHP.'],
            'endpoint' => '/contact',
            'method' => 'POST',
            'is_active' => true,
        ]);

        if ($form->fields()->exists()) {
            return;
        }

        $form->fields()->createMany([
            ['type' => 'text', 'name' => 'name', 'label' => ['en' => 'Name', 'ar' => 'الاسم'], 'is_required' => true, 'order' => 1],
            ['type' => 'email', 'name' => 'email', 'label' => ['en' => 'Email', 'ar' => 'البريد الإلكتروني'], 'is_required' => true, 'order' => 2],
            ['type' => 'select', 'name' => 'topic', 'label' => ['en' => 'Topic', 'ar' => 'الموضوع'], 'has_options' => true, 'order' => 3, 'options' => [
                ['label' => ['en' => 'Support', 'ar' => 'الدعم'], 'value' => 'support'],
                ['label' => ['en' => 'Sales', 'ar' => 'المبيعات'], 'value' => 'sales'],
            ]],
            ['type' => 'textarea', 'name' => 'message', 'label' => ['en' => 'Message', 'ar' => 'الرسالة'], 'order' => 4],
        ]);

        $form->requests()->create([
            'status' => 'pending',
            'description' => 'Demo submission',
            'date' => now()->toDateString(),
            'time' => now()->toTimeString(),
            'payload' => ['name' => 'Demo User', 'email' => 'demo@tomatophp.com', 'topic' => 'support', 'message' => 'Hello from the demo!'],
        ]);
    }
}
