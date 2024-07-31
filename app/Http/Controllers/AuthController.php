<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Modules\Core\Models\Tenant;
use TomatoPHP\FilamentAccounts\Models\AccountsMeta;
use TomatoPHP\FilamentAlerts\Services\SendNotification;

class AuthController extends Controller
{
    public function provider($provider)
    {
        try {
            return Socialite::driver($provider)
                ->redirect();
        }catch (\Exception $exception){
            Notification::make()
                ->title('Error')
                ->body('Something went wrong!')
                ->danger()
                ->send();

            return redirect()->to('app/login');
        }
    }

    public function callback($provider)
    {
        try {
            $providerHasToken = config('services.'.$provider.'.client_token');
            if($providerHasToken){
                $socialUser = Socialite::driver($provider)->userFromToken($providerHasToken);
            }
            else {
                $socialUser = Socialite::driver($provider)->user();
            }

            if(isset($socialUser->attributes['nickname'])){
                $id = str($socialUser->attributes['nickname'])->slug('_');
            }
            else {
                $id = \Str::of($socialUser->name)->slug('_')->toString();
            }

            $record = Tenant::query()->whereHas('social', function ($query) use ($socialUser, $provider) {
                $query->where('provider', $provider);
                $query->where('provider_id', $socialUser->id);
            })->first();


            $sessionData = null;
            if(session()->has('demo_user') && isset(json_decode(session()->get('demo_user'))->packages)){
                $sessionData = json_decode(session()->get('demo_user'));
            }
            if(!$record){
                $record = new Tenant();
                $record->name = $socialUser->name;
                $record->email = $socialUser->email;
                $record->id = $id;
                $record->packages = $sessionData->packages;
                $record->password = bcrypt(Str::random(8));
                $record->save();

                $record->social()->create([
                    'provider' => $provider,
                    'provider_id' => $socialUser->id
                ]);

                $record->domains()->create(['domain' => \Str::of($socialUser->name)->slug()->toString()]);

                Notification::make()
                    ->title('New Demo User')
                    ->body(collect([
                        'NAME: '.$record->name,
                        'EMAIL: '.$record->email,
                        'USERNAME: '.$record->id,
                        'PACKAGES: '.collect($sessionData->packages)->implode(','),
                        'URL: '.'https://'.\Str::of($socialUser->name)->slug()->toString().'.'.config('app.domain'),
                    ])->implode("\n"))
                    ->sendToDiscord();
            }
            else {
                if($sessionData){
                    $record->packages = $sessionData->packages;
                    $record->save();


                    config(['database.connections.dynamic.database' => config('tenancy.database.prefix').$record->id. config('tenancy.database.suffix')]);
                    DB::connection('dynamic')
                        ->table('users')
                        ->where('email', $record->email)
                        ->update([
                            "packages" => json_encode($sessionData->packages),
                        ]);
                }
            }

            session()->regenerate();

            $token = tenancy()->impersonate($record, 1, '/app', 'web');

            return redirect()->to('https://' . $record->domains[0]->domain . '.' . config('app.domain') . '/login/url?token=' . $token->token . '&email=' . $record->email);
        }
        catch (\Exception $exception){
            Notification::make()
                ->title('Error')
                ->body('Something went wrong!')
                ->danger()
                ->send();
            return redirect()->to('/');
        }
    }
}
