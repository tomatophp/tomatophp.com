<?php

namespace App\Filament\Apps\Resources\DemosResource\Pages;

use App\Filament\Apps\Resources\DemosResource;
use App\Models\Tenant;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ListDemos extends ManageRecords
{
    protected static string $resource = DemosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data){
                    $countCurrentUserTenants = Tenant::query()->where('account_id', auth('accounts')->user()->id)->get();
                    if(count($countCurrentUserTenants)>=3){
                        Notification::make()
                            ->title('Sorry')
                            ->body('You have reached the maximum number of demos you can create.')
                            ->danger()
                            ->send();

                        return $countCurrentUserTenants->first();
                    }
                    else {
                        $data['id'] = \Str::of($data['name'])->slug('_')->toString();
                        $data['domain'] =  \Str::of($data['name'])->slug()->toString();
                        if(!empty(auth('accounts')->user()->password)){
                            $data['password'] = auth('accounts')->user()->password;
                        }
                        else {
                            $data['password'] = Hash::make(Str::random(8));
                        }
                        $data['email'] = auth('accounts')->user()->email;
                        $data['phone'] = auth('accounts')->user()->phone;
                        $data['account_id'] = auth('accounts')->user()->id;


                        $record = Tenant::create($data);

                        $record->domains()->create(['domain' => $data['domain']]);

                        Notification::make()
                            ->title('New Demo')
                            ->body(collect([
                                'NAME: '.$data['name'],
                                'EMAIL: '.$data['email'],
                                'USERNAME: '.$data['domain'],
                                'PACKAGES: '.collect($data['packages'])->implode(','),
                                'URL: '.'https://'.\Str::of($data['name'])->slug()->toString().'.'.config('app.domain'),
                            ])->implode("\n"))
                            ->sendToDiscord();

                        return $record;
                    }
                }),
        ];
    }
}
