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

class GoogleIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:google-index';

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
            $ar = url('/ar'. ($post->type === 'post' ? '/blog/' : '/open-source/') . $post->slug);
            $en = url('/en'.($post->type === 'post' ? '/blog/' : '/open-source/') . $post->slug);

            $this->info("Google Indexing: $en");
            try {
                FilamentSeo::google()->indexUrl($en);
            }catch (\Exception $e){
                $this->error($e->getMessage());
            }

            $this->info("Google Indexing: $ar");
            try {
                FilamentSeo::google()->indexUrl($ar);
            }catch (\Exception $e){
                $this->error($e->getMessage());
            }

            $this->info("====================================");
        }
    }
}
