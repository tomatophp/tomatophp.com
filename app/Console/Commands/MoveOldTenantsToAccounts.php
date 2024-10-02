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
            if($account->meta('social') && !empty($account->meta('social'))){
                echo "Move account: {$account->name}\n";
                $social = [];

                foreach ($account->meta('social') as $item){
                    $networkURl = match($item['network']){
                        "github" => "https://www.github.com/",
                        "twitter" => "https://twitter.com/",
                        "linkedin" => "https://www.linkedin.com/in/",
                        "whatsapp" => "https://wa.me/",
                        "facebook" => "https://www.facebook.com/",
                        "instagram" => "https://www.instagram.com/",
                        "youtube" => "https://www.youtube.com/",
                        "twitch" => "https://www.twitch.tv/",
                        "reddit" => "https://www.reddit.com/user/",
                        "behance" => "https://be.net/",
                        "dribbble" => "https://dribbble.com/",
                        "link" => "https://"
                    };

                    $social[] = [
                        "network" => $item['network'],
                        "url" => $item['url'],
                        "username" => str($item['url'])->replace($networkURl, '')->toString()
                    ];
                }

                $account->meta('social', $social);
            }



        }
    }
}
