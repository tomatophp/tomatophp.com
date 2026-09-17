<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use TomatoPHP\FilamentNotes\Models\Note;
use TomatoPHP\FilamentTypes\Models\Type;

/**
 * Demo sticky notes for tomatophp/filament-notes: the note groups and statuses (filament-types rows)
 * and a colourful board for the demo user, with pinned notes for the dashboard widget.
 */
class DemoNotesSeeder extends Seeder
{
    /**
     * @var array<string, array<string, array{0: string, 1: string, 2: string, 3: string}>>
     */
    protected array $types = [
        'groups' => [
            'todo' => ['TODO', 'مهام', '#D64524', 'heroicon-o-check-circle'],
            'ideas' => ['Ideas', 'أفكار', '#4E9A3E', 'heroicon-o-light-bulb'],
            'saved' => ['Saved', 'محفوظ', '#2563EB', 'heroicon-o-bookmark'],
        ],
        'status' => [
            'pending' => ['Pending', 'معلق', '#F59E0B', 'heroicon-o-clock'],
            'processing' => ['Processing', 'قيد التنفيذ', '#2563EB', 'heroicon-o-arrow-path'],
            'done' => ['Done', 'منتهي', '#16A34A', 'heroicon-o-check-badge'],
        ],
    ];

    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $notes = [
        ['title' => 'Launch checklist', 'group' => 'todo', 'status' => 'processing', 'background' => '#F4F39E', 'border' => '#DEE184', 'color' => '#47576B', 'font_size' => '1em', 'icon' => 'heroicon-o-rocket-launch', 'is_pined' => true, 'is_public' => true, 'body' => "Ship the **Filament v5** release.\n\n- Tests green\n- Covers refreshed", 'checklist' => ['Run the test suite' => true, 'Refresh screenshots' => true, 'Tag the release' => false]],
        ['title' => 'Plugin ideas', 'group' => 'ideas', 'status' => 'pending', 'background' => '#C7F0D8', 'border' => '#8FD9AE', 'color' => '#1F4D35', 'font_size' => '1.25em', 'icon' => 'heroicon-o-light-bulb', 'is_pined' => true, 'is_public' => true, 'body' => 'A kanban view for notes, grouped by status.'],
        ['title' => 'Customer call', 'group' => 'todo', 'status' => 'pending', 'background' => '#FFD6CC', 'border' => '#F5A898', 'color' => '#7A2614', 'font_size' => '1em', 'icon' => 'heroicon-o-phone', 'is_pined' => true, 'is_public' => false, 'body' => 'Follow up on the invoice questions every Monday at 10:00.', 'date' => '+2 days', 'time' => '10:00'],
        ['title' => 'Reading list', 'group' => 'saved', 'status' => 'done', 'background' => '#D6E4FF', 'border' => '#A8C3F5', 'color' => '#1E3A6E', 'font_size' => '1em', 'icon' => 'heroicon-o-book-open', 'is_pined' => false, 'is_public' => true, 'body' => "- Livewire 4 upgrade notes\n- Tailwind v4 theming\n- Filament schemas"],
        ['title' => 'Design review', 'group' => 'ideas', 'status' => 'processing', 'background' => '#EADCFB', 'border' => '#CDB3F2', 'color' => '#4A2A78', 'font_size' => '1.25em', 'icon' => 'heroicon-o-swatch', 'is_pined' => true, 'is_public' => true, 'body' => 'Grid lines, brand red **#D64524** and accent green **#4E9A3E**.'],
        ['title' => 'Groceries', 'group' => 'todo', 'status' => 'done', 'background' => '#F4F39E', 'border' => '#DEE184', 'color' => '#47576B', 'font_size' => '1em', 'icon' => 'heroicon-o-shopping-cart', 'is_pined' => false, 'is_public' => false, 'body' => 'Weekend list.', 'checklist' => ['Tomatoes' => true, 'Olive oil' => false, 'Bread' => false]],
        ['title' => 'Release notes draft', 'group' => 'saved', 'status' => 'pending', 'background' => '#FFE8B3', 'border' => '#F5CF7A', 'color' => '#6B4A0E', 'font_size' => '1em', 'icon' => 'heroicon-o-document-text', 'is_pined' => false, 'is_public' => true, 'body' => 'Filament v5, Laravel 12 / 13, new test suite and fresh screenshots.'],
        ['title' => 'Team standup', 'group' => 'todo', 'status' => 'processing', 'background' => '#CFF4F7', 'border' => '#95E0E6', 'color' => '#13545A', 'font_size' => '1em', 'icon' => 'heroicon-o-user-group', 'is_pined' => false, 'is_public' => true, 'body' => 'Daily at 09:30. Blockers first.', 'time' => '09:30'],
    ];

    public function run(): void
    {
        foreach ($this->types as $type => $keys) {
            foreach ($keys as $key => [$english, $arabic, $color, $icon]) {
                Type::query()->updateOrCreate(
                    ['for' => 'notes', 'type' => $type, 'key' => $key],
                    ['name' => ['en' => $english, 'ar' => $arabic], 'color' => $color, 'icon' => $icon],
                );
            }
        }

        $owner = User::query()->where('email', config('demo.email'))->first() ?? User::query()->first();

        if (! $owner || Note::query()->exists()) {
            return;
        }

        foreach ($this->notes as $order => $note) {
            Note::query()->create([
                ...$note,
                'user_id' => $owner->getKey(),
                'user_type' => $owner::class,
                'order' => $order,
                'date' => isset($note['date']) ? now()->modify($note['date'])->toDateString() : null,
            ]);
        }
    }
}
