<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class MoveOldTenantsToAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:move';

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
        $accounts = Account::query()->where('is_active', 1)->get();

        foreach ($accounts as $account){
            $account->logs()->delete();

            if($account->comments()->count()>0){
               foreach ($account->comments as $comment){
                   $account->log($comment->content, 'comment', $comment->comment, $comment->created_at);
               }
            }

            if($account->likes()->count()>0){
                foreach ($account->likes as $like){
                    $account->log($like->post, 'like', 'liked', $like->created_at);
                }
            }
        }
    }
}
