<?php

namespace App\Workflows;

use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use TomatoPHP\FilamentWorkflows\Models\TriggerHasAction;
use TomatoPHP\FilamentWorkflows\Services\Abstracts\Action;

/**
 * Demo workflow action for tomatophp/filament-workflows: a database notification to every user.
 * It never sends mail or calls an external service, so it is safe on the public demo.
 */
class NotifyAdmins extends Action
{
    public static function run(mixed $event, TriggerHasAction $action): void
    {
        $record = property_exists($event, 'data') && $event->data instanceof Model ? $event->data : null;
        $title = $action->payload['title'] ?? 'Workflow ran';

        Notification::make()
            ->title($title)
            ->body($record ? class_basename($record).' #'.$record->getKey() : null)
            ->success()
            ->sendToDatabase(User::query()->get());
    }

    public static function form(): array
    {
        return [
            TextInput::make('title')->required()->default('Workflow ran'),
        ];
    }
}
