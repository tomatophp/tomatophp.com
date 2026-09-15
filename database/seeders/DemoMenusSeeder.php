<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TomatoPHP\FilamentMenus\Models\Menu;

/**
 * Demo menus for tomatophp/filament-menus: a header menu and a footer menu with url, route and badge items.
 */
class DemoMenusSeeder extends Seeder
{
    public function run(): void
    {
        $header = Menu::query()->updateOrCreate(
            ['key' => 'header'],
            ['title' => 'Header Menu', 'location' => 'header', 'activated' => true],
        );

        $header->menuItems()->delete();

        $header->menuItems()->createMany([
            ['title' => ['en' => 'Home', 'ar' => 'الرئيسية'], 'icon' => 'heroicon-o-home', 'is_route' => false, 'url' => '/', 'order' => 1],
            ['title' => ['en' => 'Blog', 'ar' => 'المدونة'], 'icon' => 'heroicon-o-newspaper', 'is_route' => true, 'route' => 'filament.admin.resources.posts.index', 'order' => 2],
            ['title' => ['en' => 'Packages', 'ar' => 'الحزم'], 'icon' => 'heroicon-o-cube', 'is_route' => false, 'url' => 'https://github.com/tomatophp', 'new_tab' => true, 'has_badge' => true, 'badge' => ['en' => 'New', 'ar' => 'جديد'], 'badge_color' => 'success', 'order' => 3],
            ['title' => ['en' => 'Contact', 'ar' => 'اتصل بنا'], 'icon' => 'heroicon-o-envelope', 'is_route' => false, 'url' => '/contact', 'order' => 4],
        ]);

        $footer = Menu::query()->updateOrCreate(
            ['key' => 'footer'],
            ['title' => 'Footer Menu', 'location' => 'footer', 'activated' => true],
        );

        $footer->menuItems()->delete();

        $footer->menuItems()->createMany([
            ['title' => ['en' => 'Privacy', 'ar' => 'الخصوصية'], 'icon' => 'heroicon-o-shield-check', 'is_route' => false, 'url' => '/privacy', 'order' => 1],
            ['title' => ['en' => 'Terms', 'ar' => 'الشروط'], 'icon' => 'heroicon-o-document-text', 'is_route' => false, 'url' => '/terms', 'order' => 2],
        ]);
    }
}
