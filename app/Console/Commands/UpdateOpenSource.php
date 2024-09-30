<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use TomatoPHP\FilamentCms\Jobs\GitHubMetaRefreshJob;

class UpdateOpenSource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Open Source Repos Update';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        dispatch(new GitHubMetaRefreshJob());
    }
}
