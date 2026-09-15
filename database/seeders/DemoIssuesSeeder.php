<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TomatoPHP\FilamentIssues\Models\Issue;
use TomatoPHP\FilamentIssues\Models\IssueOwner;
use TomatoPHP\FilamentIssues\Models\Label;
use TomatoPHP\FilamentIssues\Models\Org;
use TomatoPHP\FilamentIssues\Models\Repository;

/**
 * Demo data for tomatophp/filament-issues, seeded locally so the demo never calls the GitHub API.
 * Rows are created directly: production installs without dev dependencies, so there is no Faker.
 */
class DemoIssuesSeeder extends Seeder
{
    /**
     * @var array<string, array<int, array{0: string, 1: string}>>
     */
    protected array $issues = [
        'filament-users' => [
            ['Impersonate button hidden for super admins', 'The impersonate action should stay visible for super admins on the users table.'],
            ['Add avatar column to the users table', 'Show the user avatar next to the name, with a fallback to initials.'],
        ],
        'filament-alerts' => [
            ['Allow scheduling notification templates', 'Pick a date and time when sending a template to users.'],
        ],
        'filament-issues' => [
            ['Filter issues by milestone', 'Add a milestone filter next to the repository filter.'],
            ['Share an issue board publicly', 'A read-only public link for the issues board.'],
        ],
    ];

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
        ])->map(fn (array $label): Label => Label::query()->create($label));

        $number = 100;

        foreach ($this->issues as $repoName => $repoIssues) {
            $repository = Repository::query()->firstOrCreate(['owner_id' => $org->id, 'name' => $repoName]);
            $repoUrl = 'https://github.com/'.$repository->repo;

            foreach ($repoIssues as $index => [$title, $body]) {
                $number++;
                $isPullRequest = $index === 1 && $repoName === 'filament-issues';

                $issue = Issue::query()->create([
                    'issue_id' => (string) (900000000 + $number),
                    'repo_id' => $repository->id,
                    'createdBy' => $owner->id,
                    'number' => $number,
                    'repoName' => $repository->repo,
                    'repoUrl' => $repoUrl,
                    'title' => $title,
                    'url' => $repoUrl.($isPullRequest ? '/pull/' : '/issues/').$number,
                    'body' => $body,
                    'commentCount' => $number % 7,
                    'createdAt' => now()->subDays($number % 60),
                    'isPullRequest' => $isPullRequest,
                    'is_public' => true,
                    'is_trend' => $index === 0,
                ]);

                $issue->labels()->sync([$labels[$index % $labels->count()]->id]);
            }
        }
    }
}
