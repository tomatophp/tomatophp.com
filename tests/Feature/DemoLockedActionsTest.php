<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DemoInvoicesSeeder;
use Database\Seeders\DemoUsersSeeder;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Toggle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use TomatoPHP\FilamentInvoices\Filament\Resources\InvoiceResource\Pages\ListInvoices;
use TomatoPHP\FilamentInvoices\Filament\Resources\InvoiceResource\Pages\ViewInvoice;
use TomatoPHP\FilamentInvoices\Models\Invoice;
use TomatoPHP\FilamentIssues\Filament\Resources\IssueResource\Pages\ListIssues;

/**
 * Package actions that send mail or call GitHub are hidden in the public demo.
 */
class DemoLockedActionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs(User::factory()->create());
    }

    /**
     * @return array<string, array{0: bool}>
     */
    public static function demoModes(): array
    {
        return [
            'demo mode' => [true],
            'outside demo mode' => [false],
        ];
    }

    #[Test]
    #[DataProvider('demoModes')]
    public function invoice_email_actions_are_hidden_only_in_demo_mode(bool $demo): void
    {
        config()->set('demo.enabled', $demo);
        $this->seed([DemoUsersSeeder::class, DemoInvoicesSeeder::class]);
        $invoice = Invoice::query()->firstOrFail();

        $assertion = $demo ? 'assertActionHidden' : 'assertActionVisible';

        Livewire::test(ListInvoices::class)
            ->{$assertion}(TestAction::make('send_email')->table($invoice))
            ->{$assertion}(TestAction::make('bulk_send_email')->table()->bulk());

        Livewire::test(ViewInvoice::class, ['record' => $invoice->getRouteKey()])
            ->{$assertion}('send_email');
    }

    #[Test]
    #[DataProvider('demoModes')]
    public function issue_refresh_and_clean_actions_are_hidden_only_in_demo_mode(bool $demo): void
    {
        config()->set('demo.enabled', $demo);

        $assertion = $demo ? 'assertActionHidden' : 'assertActionVisible';

        Livewire::test(ListIssues::class)
            ->{$assertion}('refresh')
            ->{$assertion}('clean');
    }

    #[Test]
    #[DataProvider('demoModes')]
    public function the_form_builder_relation_toggle_is_hidden_only_in_demo_mode(bool $demo): void
    {
        config()->set('demo.enabled', $demo);

        $this->assertSame($demo, Toggle::make('is_relation')->isHidden());
        $this->assertFalse(Toggle::make('is_required')->isHidden());
    }
}
