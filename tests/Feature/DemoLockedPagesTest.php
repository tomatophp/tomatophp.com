<?php

namespace Tests\Feature;

use App\Http\Middleware\LockDemoPages;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as Router;
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

    /**
     * The filament-plugins table builder writes migrations and generates code. It is off on the
     * demo (allowGenerator(false) unregisters the resource), and its path is locked as well.
     */
    #[Test]
    #[DataProvider('lockedPaths')]
    public function a_locked_path_redirects_to_the_dashboard_in_demo_mode(string $path): void
    {
        config()->set('demo.enabled', true);

        $request = Request::create($path);
        $request->setRouteResolver(fn (): Route => new Route('GET', $path, []));

        $response = app(LockDemoPages::class)->handle(
            $request,
            fn (): Response => new Response('reached'),
        );

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(url('/admin'), $response->headers->get('Location'));
    }

    #[Test]
    #[DataProvider('lockedPaths')]
    public function a_locked_path_is_not_intercepted_outside_demo_mode(string $path): void
    {
        config()->set('demo.enabled', false);

        $request = Request::create($path);
        $request->setRouteResolver(fn (): Route => new Route('GET', $path, []));

        $response = app(LockDemoPages::class)->handle(
            $request,
            fn (): Response => new Response('reached'),
        );

        $this->assertSame('reached', $response->getContent());
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function lockedPaths(): array
    {
        return [
            'plugins table builder' => ['/admin/tables'],
            'plugins table builder record' => ['/admin/tables/1/edit'],
        ];
    }

    /**
     * The table builder resource is not even registered on the demo panel.
     */
    #[Test]
    public function the_plugins_table_builder_resource_is_not_registered(): void
    {
        $this->assertFalse(Router::has('filament.admin.resources.tables.index'));
    }
}
