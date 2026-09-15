<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TomatoPHP\FilamentIssues\Database\Factories\IssueFactory;
use TomatoPHP\FilamentIssues\Database\Factories\LabelFactory;
use TomatoPHP\FilamentIssues\Models\Issue;
use TomatoPHP\FilamentIssues\Models\IssueOwner;
use TomatoPHP\FilamentIssues\Models\Org;
use TomatoPHP\FilamentIssues\Models\Repository;

/**
 * Demo data for tomatophp/filament-issues, seeded locally so the demo never calls the GitHub API.
 */
class DemoIssuesSeeder extends Seeder
{
    public function run(): void
    {
        if (Issue::query()->exists()) {
            return;
        }

        $org = Org::query()->firstOrCreate(['name' => 'tomatophp'], ['last_update' => now()]);
        $owner = IssueOwner::query()->firstOrCreate(
            ['name' => 'fadymondy'],
            ['url' => 'https://github.com/fadymondy', 'profilePictureUrl' => 'https://avatars.githubusercontent.com/u/12937929?s=48'],
        );

        $labels = collect([
            ['name' => 'bug', 'color' => '#d73a4a'],
            ['name' => 'enhancement', 'color' => '#a2eeef'],
            ['name' => 'help wanted', 'color' => '#008672'],
        ])->map(fn (array $label) => LabelFactory::new()->create($label));

        $titles = [
            'filament-users' => ['Impersonate button hidden for super admins', 'Add avatar column to the users table'],
            'filament-alerts' => ['Allow scheduling notification templates'],
            'filament-issues' => ['Filter issues by milestone', 'Share an issue board publicly'],
        ];

        foreach ($titles as $repoName => $repoTitles) {
            $repository = Repository::query()->firstOrCreate(['owner_id' => $org->id, 'name' => $repoName]);

            foreach ($repoTitles as $index => $title) {
                $issue = IssueFactory::new()->create([
                    'repo_id' => $repository->id,
                    'createdBy' => $owner->id,
                    'repoName' => $repository->repo,
                    'repoUrl' => 'https://github.com/'.$repository->repo,
                    'title' => $title,
                    'isPullRequest' => $index === 1 && $repoName === 'filament-issues',
                    'is_trend' => $index === 0,
                ]);

                $issue->labels()->sync([$labels[$index % $labels->count()]->id]);
            }
        }
    }
}
