<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use TomatoPHP\FilamentCms\Models\Category;
use TomatoPHP\FilamentCms\Models\Post;

/**
 * Demo content for tomatophp/filament-cms: post categories, tags and published posts with feature images.
 */
class DemoCmsSeeder extends Seeder
{
    protected array $categories = [
        ['en' => 'Laravel', 'ar' => 'لارافيل', 'icon' => 'heroicon-o-fire', 'color' => '#ef4444'],
        ['en' => 'FilamentPHP', 'ar' => 'فيلامنت', 'icon' => 'heroicon-o-bolt', 'color' => '#f59e0b'],
        ['en' => 'Tutorials', 'ar' => 'دروس', 'icon' => 'heroicon-o-academic-cap', 'color' => '#10b981'],
    ];

    protected array $tags = ['php', 'open-source', 'packages'];

    protected array $posts = [
        ['en' => 'Getting started with TomatoPHP', 'ar' => 'البداية مع TomatoPHP', 'type' => 'post', 'color' => [239, 68, 68]],
        ['en' => 'Build a CMS in minutes with Filament', 'ar' => 'ابني نظام محتوى في دقائق', 'type' => 'post', 'color' => [245, 158, 11]],
        ['en' => 'Translatable content made simple', 'ar' => 'محتوى متعدد اللغات ببساطة', 'type' => 'post', 'color' => [16, 185, 129]],
        ['en' => 'Custom post types for every project', 'ar' => 'أنواع محتوى مخصصة لكل مشروع', 'type' => 'service', 'color' => [59, 130, 246]],
        ['en' => 'Our open source packages', 'ar' => 'حزمنا مفتوحة المصدر', 'type' => 'open-source', 'color' => [139, 92, 246]],
        ['en' => 'Release notes: Filament v5', 'ar' => 'ملاحظات الإصدار: فيلامنت 5', 'type' => 'post', 'color' => [236, 72, 153]],
    ];

    public function run(): void
    {
        $categories = collect($this->categories)->map(fn (array $category) => Category::query()->updateOrCreate(
            ['slug' => Str::slug($category['en'])],
            [
                'name' => ['en' => $category['en'], 'ar' => $category['ar']],
                'description' => ['en' => "Articles about {$category['en']}", 'ar' => $category['ar']],
                'for' => 'post',
                'type' => 'category',
                'icon' => $category['icon'],
                'color' => $category['color'],
                'is_active' => true,
                'show_in_menu' => true,
            ],
        ));

        $tags = collect($this->tags)->map(fn (string $tag) => Category::query()->updateOrCreate(
            ['slug' => $tag],
            ['name' => ['en' => $tag, 'ar' => $tag], 'for' => 'post', 'type' => 'tags', 'is_active' => true],
        ));

        foreach ($this->posts as $index => $data) {
            $post = Post::query()->updateOrCreate(
                ['slug' => Str::slug($data['en'])],
                [
                    'type' => $data['type'],
                    'title' => ['en' => $data['en'], 'ar' => $data['ar']],
                    'short_description' => ['en' => "A short introduction to {$data['en']}.", 'ar' => $data['ar']],
                    'keywords' => ['en' => 'tomatophp, filament, laravel', 'ar' => 'tomatophp'],
                    'body' => ['en' => "## {$data['en']}\n\nWritten with the TomatoPHP CMS on Filament v5.", 'ar' => "## {$data['ar']}"],
                    'is_published' => true,
                    'is_trend' => $index % 2 === 0,
                    'published_at' => now()->subDays($index),
                    'views' => 120 * ($index + 1),
                    'likes' => 12 * ($index + 1),
                ],
            );

            $post->categories()->sync([$categories[$index % $categories->count()]->id]);
            $post->tags()->sync($tags->pluck('id')->all());

            if (! $post->hasMedia('feature_image')) {
                $post->addMedia($this->image($data['color'], $data['en']))->toMediaCollection('feature_image');
            }
        }
    }

    /**
     * A generated cover so the demo needs no network access.
     */
    protected function image(array $rgb, string $text): string
    {
        $image = imagecreatetruecolor(800, 450);
        imagefill($image, 0, 0, imagecolorallocate($image, ...$rgb));
        imagestring($image, 5, 32, 400, $text, imagecolorallocate($image, 255, 255, 255));

        $path = storage_path('app/'.Str::slug($text).'.png');
        imagepng($image, $path);
        imagedestroy($image);

        return $path;
    }
}
