<?php

namespace Tests\Feature;

use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\DemoCategoriesSeeder;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * tomatophp/filament-translation-component and tomatophp/filament-icons inside a real resource.
 */
class ShowcaseCategoriesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    #[Test]
    public function it_lists_the_seeded_categories_with_their_icons(): void
    {
        $this->seed(DemoCategoriesSeeder::class);

        Livewire::test(ManageCategories::class)
            ->loadTable()
            ->assertCanSeeTableRecords(Category::all())
            ->assertSee('Electronics')
            ->assertTableColumnStateSet('icon', 'heroicon-o-cpu-chip', Category::query()->where('icon', 'heroicon-o-cpu-chip')->firstOrFail());
    }

    #[Test]
    public function it_creates_a_category_with_translations_and_an_icon(): void
    {
        Livewire::test(ManageCategories::class)
            ->callAction(TestAction::make('create'), data: [
                'name' => ['en' => 'Music', 'ar' => 'موسيقى'],
                'description' => ['en' => 'Instruments and concerts.', 'ar' => 'آلات وحفلات.'],
                'icon' => 'heroicon-o-musical-note',
            ])
            ->assertHasNoErrors();

        $category = Category::query()->where('icon', 'heroicon-o-musical-note')->firstOrFail();

        $this->assertSame('Music', $category->name['en']);
        $this->assertSame('موسيقى', $category->name['ar']);
        $this->assertSame('Instruments and concerts.', $category->translated('description'));
    }
}
