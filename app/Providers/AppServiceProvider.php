<?php

namespace App\Providers;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\ServiceProvider;
use Livewire\Component;
use Stichoza\GoogleTranslate\GoogleTranslate;
use TomatoPHP\FilamentDocs\Facades\FilamentDocs;
use TomatoPHP\FilamentDocs\Services\Contracts\DocsVar;
use TomatoPHP\FilamentInvoices\Facades\FilamentInvoices;
use TomatoPHP\FilamentInvoices\Filament\Resources\InvoiceResource\Pages\ListInvoices;
use TomatoPHP\FilamentInvoices\Filament\Resources\InvoiceResource\Pages\ViewInvoice;
use TomatoPHP\FilamentInvoices\Services\Contracts\InvoiceFor;
use TomatoPHP\FilamentInvoices\Services\Contracts\InvoiceFrom;
use TomatoPHP\FilamentIssues\Filament\Resources\IssueResource\Pages\ListIssues;
use TomatoPHP\FilamentMeta\Filament\RelationManager\MetaRelationManager;
use TomatoPHP\FilamentUsers\Facades\FilamentUser;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Package actions hidden in the public demo because they send mail or call external APIs,
     * keyed by action name, with the pages they are hidden on.
     *
     * @var array<string, array<int, class-string<Component>>>
     */
    public const DEMO_LOCKED_ACTIONS = [
        'send_email' => [ListInvoices::class, ViewInvoice::class],
        'bulk_send_email' => [ListInvoices::class],
        'refresh' => [ListIssues::class],
        'clean' => [ListIssues::class],
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Public demo: the Google translate plugin rewrites text through the translator; keep it offline.
        if (config('demo.enabled')) {
            $this->app->bind(GoogleTranslate::class, fn () => new class extends GoogleTranslate
            {
                public function translate(string $string): ?string
                {
                    return $string;
                }
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // tomatophp/filament-meta: a meta tab on every user.
        FilamentUser::register([
            MetaRelationManager::class,
        ]);

        // tomatophp/filament-invoices: who invoices can be billed from and to.
        FilamentInvoices::registerFrom([
            InvoiceFrom::make(User::class)->label('Users'),
        ]);
        FilamentInvoices::registerFor([
            InvoiceFor::make(User::class)->label('Users'),
        ]);

        // tomatophp/filament-docs: the only model column templates can read.
        FilamentDocs::register([
            DocsVar::make('$USER_NAME')
                ->label('User name')
                ->model(User::class)
                ->column('name'),
        ]);

        $this->lockDemoActions();
    }

    /**
     * In the public demo, hide actions that send mail or call GitHub, and the form-builder
     * "is relation" option that reads columns from any model.
     */
    protected function lockDemoActions(): void
    {
        Action::configureUsing(function (Action $action): void {
            $pages = self::DEMO_LOCKED_ACTIONS[$action->getName()] ?? null;

            if ($pages === null) {
                return;
            }

            $action->hidden(fn (mixed $livewire): bool => config('demo.enabled')
                && collect($pages)->contains(fn (string $page): bool => $livewire instanceof $page));
        });

        Toggle::configureUsing(function (Toggle $toggle): void {
            if ($toggle->getName() === 'is_relation') {
                $toggle->hidden(fn (): bool => (bool) config('demo.enabled'));
            }
        });
    }
}
