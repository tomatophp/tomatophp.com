<?php

declare(strict_types=1);

namespace TomatoPHP\FilamentIssues\Console\Commands;

use TomatoPHP\FilamentIssues\Jobs\EnsureRepoIsCrawlable;
use TomatoPHP\FilamentIssues\Services\RepoService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

final class EnsureCrawlableRepos extends Command
{
    protected $signature = 'repos:crawlable';

    protected $description = 'Loop through each repo and ensure it is crawlable. Alert if not.';

    /**
     * @throws \Throwable
     */
    public function handle(): int
    {
        $jobsCount = app(RepoService::class)
            ->reposToCrawl()
            ->chunk(25)
            ->each(function (Collection $repos): void {
                dispatch(new EnsureRepoIsCrawlable($repos));
            })
            ->count();

        $this->components->info('Dispatched '.$jobsCount.' jobs to ensure repos are crawlable.');

        return self::SUCCESS;
    }
}
