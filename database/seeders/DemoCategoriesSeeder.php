<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class DemoCategoriesSeeder extends Seeder
{
    /**
     * @var array<int, array{name: array<string, string>, description: array<string, string>, icon: string}>
     */
    protected array $categories = [
        [
            'name' => ['en' => 'Electronics', 'ar' => 'إلكترونيات'],
            'description' => ['en' => 'Phones, laptops and smart home devices.', 'ar' => 'هواتف وحواسيب وأجهزة المنزل الذكي.'],
            'icon' => 'heroicon-o-cpu-chip',
        ],
        [
            'name' => ['en' => 'Books', 'ar' => 'كتب'],
            'description' => ['en' => 'Novels, technical books and magazines.', 'ar' => 'روايات وكتب تقنية ومجلات.'],
            'icon' => 'heroicon-o-book-open',
        ],
        [
            'name' => ['en' => 'Travel', 'ar' => 'سفر'],
            'description' => ['en' => 'Flights, hotels and weekend trips.', 'ar' => 'رحلات طيران وفنادق ورحلات نهاية الأسبوع.'],
            'icon' => 'heroicon-o-globe-europe-africa',
        ],
        [
            'name' => ['en' => 'Food & Drinks', 'ar' => 'طعام ومشروبات'],
            'description' => ['en' => 'Restaurants, groceries and coffee.', 'ar' => 'مطاعم وبقالة وقهوة.'],
            'icon' => 'heroicon-o-cake',
        ],
        [
            'name' => ['en' => 'Health', 'ar' => 'صحة'],
            'description' => ['en' => 'Clinics, pharmacies and fitness.', 'ar' => 'عيادات وصيدليات ولياقة بدنية.'],
            'icon' => 'heroicon-o-heart',
        ],
        [
            'name' => ['en' => 'Finance', 'ar' => 'مالية'],
            'description' => ['en' => 'Banking, payments and invoices.', 'ar' => 'بنوك ومدفوعات وفواتير.'],
            'icon' => 'heroicon-o-banknotes',
        ],
    ];

    public function run(): void
    {
        foreach ($this->categories as $category) {
            Category::query()->updateOrCreate(['icon' => $category['icon']], $category);
        }
    }
}
