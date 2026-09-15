<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use TomatoPHP\FilamentAlerts\Filament\Pages\EmailSettingsPage;
use TomatoPHP\FilamentDiscordDriver\Filament\Pages\DiscordSettingsPage;
use TomatoPHP\FilamentInvoices\Pages\InvoiceSettingsPage;

/**
 * Pages that store credentials are unreachable in the public demo.
 */
class DemoLockedPagesTest extends TestCase
{
    use RefreshDatabase;

    public static function lockedPages(): array
    {
        return [
            'alerts email settings' => [EmailSettingsPage::class],
            'discord settings' => [DiscordSettingsPage::class],
            'invoices settings' => [InvoiceSettingsPage::class],
        ];
    }

    #[Test]
    #[DataProvider('lockedPages')]
    public function a_locked_page_redirects_to_the_dashboard_in_demo_mode(string $page): void
    {
        config()->set('demo.enabled', true);

        $this->actingAs(User::factory()->create())
            ->get($page::getUrl(panel: 'admin'))
            ->assertRedirect('/admin');
    }

    #[Test]
    #[DataProvider('lockedPages')]
    public function a_locked_page_is_reachable_outside_demo_mode(string $page): void
    {
        config()->set('demo.enabled', false);

        $this->actingAs(User::factory()->create())
            ->get($page::getUrl(panel: 'admin'))
            ->assertSuccessful();
    }
}
