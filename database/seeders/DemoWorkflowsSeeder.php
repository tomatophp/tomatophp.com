<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Workflows\NotifyAdmins;
use Illuminate\Database\Seeder;
use TomatoPHP\FilamentCms\Models\Post;
use TomatoPHP\FilamentWorkflows\Models\Action;
use TomatoPHP\FilamentWorkflows\Models\Trigger;
use TomatoPHP\FilamentWorkflows\Models\Workflow;
use TomatoPHP\FilamentWorkflows\Models\WorkflowHasTrigger;

/**
 * Demo content for tomatophp/filament-workflows: two workflows with model and webhook triggers,
 * the demo-safe "Notify admins" action attached, and a few log rows.
 */
class DemoWorkflowsSeeder extends Seeder
{
    public function run(): void
    {
        $notify = Action::query()->updateOrCreate(
            ['name' => 'Notify admins'],
            [
                'description' => 'Send a database notification to the panel users.',
                'action' => NotifyAdmins::class,
                'color' => '#4E9A3E',
                'icon' => 'heroicon-s-bell',
            ],
        );

        $postPublished = Trigger::query()->updateOrCreate(
            ['name' => 'Post published'],
            [
                'type' => 'model',
                'model_type' => Post::class,
                'model_action' => 'updated',
                'where' => 'is_published',
                'is' => '=',
                'value' => true,
                'color' => '#D64524',
                'icon' => 'heroicon-s-newspaper',
            ],
        );

        $categoryCreated = Trigger::query()->updateOrCreate(
            ['name' => 'Category created'],
            [
                'type' => 'model',
                'model_type' => Category::class,
                'model_action' => 'created',
                'color' => '#D64524',
                'icon' => 'heroicon-s-tag',
            ],
        );

        $invoicePaid = Trigger::query()->updateOrCreate(
            ['name' => 'Invoice paid webhook'],
            [
                'type' => 'webhook',
                'webhook' => 'invoice_paid',
                'color' => '#0B1429',
                'icon' => 'heroicon-s-globe-alt',
            ],
        );

        $content = $this->workflow('content-review', 'Content review', 'Tell the team when content changes.', [$postPublished, $categoryCreated], $notify);
        $billing = $this->workflow('billing-alerts', 'Billing alerts', 'React to payment provider webhooks.', [$invoicePaid], $notify);

        foreach ([[$content, $postPublished], [$content, $categoryCreated], [$billing, $invoicePaid]] as $index => [$workflow, $trigger]) {
            $workflow->logs()->firstOrCreate(
                ['trigger_id' => $trigger->getKey(), 'action_id' => $notify->getKey()],
                ['log' => 'Action: '.class_basename(NotifyAdmins::class).' executed', 'payload' => ['id' => $index + 1]],
            );
        }
    }

    /**
     * @param  array<int, Trigger>  $triggers
     */
    protected function workflow(string $key, string $name, string $description, array $triggers, Action $action): Workflow
    {
        $workflow = Workflow::query()->updateOrCreate(
            ['key' => $key],
            ['name' => $name, 'description' => $description, 'is_active' => true],
        );

        foreach ($triggers as $trigger) {
            $link = WorkflowHasTrigger::query()->firstOrCreate([
                'workflow_id' => $workflow->getKey(),
                'trigger_id' => $trigger->getKey(),
            ]);

            $link->actions()->firstOrCreate(
                ['action_id' => $action->getKey()],
                ['payload' => ['title' => "{$trigger->name} ({$name})"]],
            );
        }

        return $workflow;
    }
}
