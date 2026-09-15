<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use TomatoPHP\FilamentBookmarksMenu\Models\Bookmark;
use TomatoPHP\FilamentBookmarksMenu\Models\BookmarkLink;

/**
 * Demo folders and links for tomatophp/filament-bookmarks-menu: three shared folders and
 * one private folder that only the demo user sees.
 */
class DemoBookmarksSeeder extends Seeder
{
    /**
     * @var array<int, array{name: string, icon: string, color: string, private: bool, links: array<int, array{0: string, 1: string, 2: string}>}>
     */
    protected array $folders = [
        [
            'name' => 'Daily work',
            'icon' => 'heroicon-s-briefcase',
            'color' => '#2563eb',
            'private' => false,
            'links' => [
                ['Invoices', '/admin/invoices', 'heroicon-o-document-text'],
                ['Users', '/admin/users', 'heroicon-o-users'],
                ['Issues', '/admin/issues', 'heroicon-o-bug-ant'],
            ],
        ],
        [
            'name' => 'Content',
            'icon' => 'heroicon-s-newspaper',
            'color' => '#db2777',
            'private' => false,
            'links' => [
                ['Posts', '/admin/cms/posts', 'heroicon-o-newspaper'],
                ['Menus', '/admin/menus', 'heroicon-o-bars-3'],
                ['Documents', '/admin/documents', 'heroicon-o-document'],
            ],
        ],
        [
            'name' => 'Developer',
            'icon' => 'heroicon-s-code-bracket',
            'color' => '#16a34a',
            'private' => false,
            'links' => [
                ['TomatoPHP on GitHub', 'https://github.com/tomatophp', 'heroicon-o-code-bracket'],
                ['Filament docs', 'https://filamentphp.com/docs', 'heroicon-o-book-open'],
                ['Laravel docs', 'https://laravel.com/docs', 'heroicon-o-academic-cap'],
            ],
        ],
        [
            'name' => 'My private links',
            'icon' => 'heroicon-s-lock-closed',
            'color' => '#f59e0b',
            'private' => true,
            'links' => [
                ['TomatoPHP', 'https://tomatophp.com', 'heroicon-o-globe-alt'],
            ],
        ],
    ];

    public function run(): void
    {
        $demo = User::query()->where('email', config('demo.email'))->first();

        foreach ($this->folders as $data) {
            $folder = Bookmark::query()->updateOrCreate(['name' => $data['name']], [
                'type' => 'folder',
                'icon' => $data['icon'],
                'color' => $data['color'],
                'is_private' => $data['private'],
                'user_type' => $data['private'] ? $demo?->getMorphClass() : null,
                'user_id' => $data['private'] ? $demo?->id : null,
            ]);

            $links = collect($data['links'])->map(fn (array $link): int => BookmarkLink::query()->updateOrCreate(
                ['url' => $link[1]],
                ['name' => $link[0], 'icon' => $link[2]],
            )->id);

            $folder->links()->sync($links->all());
        }
    }
}
