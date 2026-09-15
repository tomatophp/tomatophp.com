<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use TomatoPHP\FilamentMediaManager\Models\Folder;

/**
 * Demo folders and images for tomatophp/filament-media-manager. Images are generated locally (no network).
 */
class DemoMediaSeeder extends Seeder
{
    protected array $folders = [
        ['name' => 'Brand Assets', 'icon' => 'heroicon-o-swatch', 'color' => '#ef4444', 'images' => [[239, 68, 68], [245, 158, 11], [16, 185, 129]]],
        ['name' => 'Blog Covers', 'icon' => 'heroicon-o-photo', 'color' => '#3b82f6', 'images' => [[59, 130, 246], [139, 92, 246]]],
        ['name' => 'Screenshots', 'icon' => 'heroicon-o-computer-desktop', 'color' => '#10b981', 'images' => [[16, 185, 129], [236, 72, 153], [15, 23, 42]]],
        ['name' => 'Private', 'icon' => 'heroicon-o-lock-closed', 'color' => '#64748b', 'images' => [], 'protected' => true],
    ];

    public function run(): void
    {
        foreach ($this->folders as $data) {
            $collection = Str::slug($data['name']);

            $folder = Folder::query()->updateOrCreate(
                ['collection' => $collection],
                [
                    'name' => $data['name'],
                    'description' => "Demo {$data['name']} folder",
                    'icon' => $data['icon'],
                    'color' => $data['color'],
                    'is_protected' => $data['protected'] ?? false,
                    'password' => ($data['protected'] ?? false) ? 'demo1234' : null,
                ],
            );

            if ($folder->getMedia($collection)->isNotEmpty()) {
                continue;
            }

            foreach ($data['images'] as $index => $rgb) {
                $folder->addMedia($this->image($rgb, "{$data['name']} ".($index + 1)))
                    ->toMediaCollection($collection);
            }
        }
    }

    /**
     * A generated image so the demo needs no network access.
     */
    protected function image(array $rgb, string $text): string
    {
        $image = imagecreatetruecolor(800, 600);
        imagefill($image, 0, 0, imagecolorallocate($image, ...$rgb));
        imagestring($image, 5, 32, 560, $text, imagecolorallocate($image, 255, 255, 255));

        $path = storage_path('app/'.Str::slug($text).'.png');
        imagepng($image, $path);
        imagedestroy($image);

        return $path;
    }
}
