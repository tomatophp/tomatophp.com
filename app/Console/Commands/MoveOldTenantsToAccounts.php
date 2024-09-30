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
        $tenants = Tenant::query()->get();

        foreach ($tenants as $tenant){
            echo "Move tenant: {$tenant->name}\n";
            $account = Account::query()->where('email', $tenant->email)->first();
            if(!$account){
                $account = new Account();
                $account->name = $tenant->name;
                $account->email = $tenant->email;
                $account->username =  $tenant->email;
                $account->phone = $tenant->phone;
                $account->password = $tenant->password;
                $account->is_active = true;
                $account->otp_activated_at = Carbon::now();
                $account->save();
            }

            echo "Link To account: {$account->email}\n";

            $tenant->account_id = $account->id;
            $tenant->save();

            if($tenant->social()->count() > 0){
                $socials = $tenant->social()->get();
                foreach ($socials as $social){
                    echo "Link To social: {$social->provider}\n";
                    $account->meta($social->provider, $social->provider_id);
                }
            }
        }
    }
}
