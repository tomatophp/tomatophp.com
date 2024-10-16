<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use TomatoPHP\FilamentCms\Models\Post;
use TomatoPHP\FilamentSeo\Jobs\GoogleIndexURLJob;
use Ymigval\LaravelIndexnow\Facade\IndexNow;

class MoveOldTenantsToAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $posts = Post::query()->where('is_published', 1)->get();

        foreach ($posts as $post){
            $url = url(($post->type === 'post' ? '/blog/' : '/open-source/') . $post->slug);

            $this->info("Google Indexing: $url");
            dispatch(new GoogleIndexURLJob(
                url: $url,
            ));

            $this->info("IndexNow Indexing: $url");
            IndexNow::submit($url);
        }
    }
}
