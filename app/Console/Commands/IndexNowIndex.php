<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use TomatoPHP\FilamentCms\Models\Post;
use TomatoPHP\FilamentSeo\Facades\FilamentSeo;
use TomatoPHP\FilamentSeo\Jobs\GoogleIndexURLJob;
use Ymigval\LaravelIndexnow\Facade\IndexNow;

class IndexNowIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:index-now';

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

        $links = [];
        foreach ($posts as $post){
            $ar = url('/ar'. ($post->type === 'post' ? '/blog/' : '/open-source/') . $post->slug);
            $en = url('/en'.($post->type === 'post' ? '/blog/' : '/open-source/') . $post->slug);

            $links[] = $ar;
            $links[] = $en;
        }

        $this->info("IndexNow Indexing: " . url('/bing.txt'));
        IndexNow::keyFile(url('/bing.txt'))->submit($links);
    }
}
