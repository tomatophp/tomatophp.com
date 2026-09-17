<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Resources\Pages\Page as ResourcePage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as Router;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;
use TomatoPHP\FilamentAccounts\FilamentAccountsPlugin;

/**
 * Real-project check for the tomatophp packages: every GET page registered on every
 * Filament panel must render for an authenticated admin.
 */
class FilamentPanelSmokeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function every_panel_page_renders(): void
    {
        $admin = User::factory()->create(['email' => 'admin@tomato.fadymondy.com']);

        $checked = [];
        $skipped = [];
        $failures = [];

        foreach (Filament::getPanels() as $panel) {
            $prefix = "filament.{$panel->getId()}.";

            foreach (Router::getRoutes()->getRoutes() as $route) {
                $name = (string) $route->getName();

                // Login, registration, password reset and email verification are guest pages.
                if (! str_starts_with($name, $prefix) || str_contains($name, '.auth.') || ! in_array('GET', $route->methods(), true)) {
                    continue;
                }

                try {
                    $parameters = $this->parametersFor($route);
                } catch (RuntimeException $exception) {
                    $skipped[] = "{$name}: {$exception->getMessage()}";

                    continue;
                }

                $response = $this->actingAs($admin)->get(route($name, $parameters));
                $checked[] = $name;

                if ($response->status() >= 400) {
                    $reason = $response->exception
                        ? $response->exception::class.': '.$response->exception->getMessage()
                        : 'no exception captured';

                    $failures[] = "{$name} [{$response->status()}] {$reason}";
                }
            }
        }

        fwrite(STDERR, sprintf(
            "\nPanel smoke: %d pages rendered, %d skipped\n%s\n",
            count($checked),
            count($skipped),
            implode("\n", array_map(fn (string $line): string => "  skipped {$line}", $skipped)),
        ));

        $this->assertNotEmpty($checked, 'No Filament panel pages were found.');
        $this->assertSame([], $failures, "Pages that failed to render:\n".implode("\n", $failures));
    }

    /**
     * filament-ecommerce registers filament-accounts itself when the panel has no accounts plugin.
     * The demo registers its own first, so the demo's options must survive.
     */
    #[Test]
    public function the_ecommerce_plugin_keeps_the_demo_accounts_configuration(): void
    {
        /** @var FilamentAccountsPlugin $accounts */
        $accounts = Filament::getPanel('admin')->getPlugin('filament-accounts');

        $this->assertTrue($accounts->useTypes);
        $this->assertTrue($accounts->useAvatar);
        $this->assertTrue($accounts->showAddressField);
        $this->assertFalse($accounts->useImport);
        $this->assertFalse($accounts->useExport);
    }

    /**
     * Resolve route parameters; a resource page's {record} is the first row of its model,
     * created through the model factory when the table is empty.
     *
     * @return array<string, mixed>
     */
    private function parametersFor(Route $route): array
    {
        $parameters = [];

        foreach ($route->parameterNames() as $parameter) {
            $page = $route->getControllerClass();

            if ($parameter !== 'record' || ! $page || ! is_subclass_of($page, ResourcePage::class)) {
                throw new RuntimeException("unsupported route parameter {{$parameter}}");
            }

            $model = $page::getResource()::getModel();
            $record = $model::query()->first();

            if (! $record && method_exists($model, 'factory')) {
                $record = $model::factory()->create();
            }

            if (! $record) {
                throw new RuntimeException("no {$model} record and no factory to create one");
            }

            $parameters['record'] = $record->getRouteKey();
        }

        return $parameters;
    }
}
