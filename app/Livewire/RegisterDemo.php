<?php

namespace App\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class RegisterDemo extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithFormActions;
    use InteractsWithForms;

    public array $data=[];

    public function getRegisterAction(): Action
    {
        return Action::make('getRegisterAction')
            ->label('Get Started')
            ->form([
                Forms\Components\Grid::make([
                    'sm' => 1,
                    'lg' => 2
                ])->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->unique(table:'tenants', ignoreRecord: true)->live(onBlur: true)
                        ->columnSpanFull()
                        ->afterStateUpdated(function(Forms\Set $set, $state) {
                            $set('id', $slug = \Str::of($state)->slug('_')->toString());
                            $set('domain', \Str::of($state)->slug()->toString());
                        }),
                    Forms\Components\TextInput::make('id')
                        ->label('Unique ID')
                        ->required()
                        ->unique(table: 'tenants', ignoreRecord: true),
                    Forms\Components\TextInput::make('domain')
                        ->label('Sub-Domain')
                        ->required()
                        ->unique(table: 'domains',ignoreRecord: true)
                        ->prefix('https://')
                        ->suffix(".".request()->getHost())
                    ,
                    Forms\Components\TextInput::make('email')
                        ->required()
                        ->email(),
                    Forms\Components\TextInput::make('phone')
                        ->required()
                        ->tel(),
                    Forms\Components\TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->revealable(filament()->arePasswordsRevealable())
                        ->rule(Password::default())
                        ->autocomplete('new-password')
                        ->dehydrated(fn ($state): bool => filled($state))
                        ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                        ->live(debounce: 500)
                        ->same('passwordConfirmation'),
                    Forms\Components\TextInput::make('passwordConfirmation')
                        ->label('Password Confirmation')
                        ->password()
                        ->revealable(filament()->arePasswordsRevealable())
                        ->required()
                        ->dehydrated(false),
                    Forms\Components\Select::make('packages')
                        ->columnSpanFull()
                        ->required()
                        ->default(["filament-users"])
                        ->multiple()
                        ->options(collect(config('app.packages'))->pluck('label', 'key')->toArray()),
                ])
            ])
            ->action(function (array $data){
                $otp = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
                session()->put('demo_user', json_encode($data));
                session()->put('demo_otp', $otp);


                Notification::make()
                    ->title('New Translations Demo User')
                    ->body(collect([
                        'NAME: '.$data['name'],
                        'EMAIL: '.$data['email'],
                        'USERNAME: '.$data['domain'],
                        'OTP: '.$otp,
                        'URL: '.url('/'),
                    ])->implode("\n"))
                    ->sendToDiscord();


                try {
                    $embeds = [];
                    $embeds['description'] = "your OTP is: ". $otp;
                    $embeds['url'] = url('/otp');

                    $params = [
                        'content' => "@" . $data['domain'],
                        'embeds' => [
                            $embeds
                        ]
                    ];

                    Http::post(config('services.discord.otp-webhook'), $params)->json();

                }catch (\Exception $e){
                    Notification::make()
                        ->title('Something went wrong')
                        ->danger()
                        ->send();
                }

                Notification::make()
                    ->title('Check discord server')
                    ->body('We have sent your OTP to our discord server #otp channel')
                    ->success()
                    ->send();

                return redirect()->route('verify.otp');
            });
    }


    public function render()
    {
        return view('livewire.register-demo');
    }
}
