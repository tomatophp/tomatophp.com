<?php

namespace App\Livewire;

use App\Models\Tenant;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Login extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithFormActions;
    use InteractsWithForms;
    use WithRateLimiting;

    public array $data = [];


    public function mount()
    {
        $this->form->fill([
            'loginBy' => 'register',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make("Login To Your SaaS Demo")
                ->description('Please use your email and password to login to your account.')
                ->schema([
                    Forms\Components\TextInput::make('email')
                        ->hidden(fn(Get $get) => $get('loginBy') !== 'register')
                        ->required()
                        ->email(),
                    Forms\Components\TextInput::make('password')
                        ->required()
                        ->hidden(fn(Get $get) => $get('loginBy') !== 'register')
                        ->label('Password')
                        ->password()
                        ->revealable(filament()->arePasswordsRevealable())
                        ->rule(Password::default())
                        ->autocomplete('new-password')
                ])
        ])->statePath('data');
    }


    public function submit()
    {
        $this->form->validate();

        $data = $this->form->getState();

        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }


        $record = Tenant::query()
            ->where('email', $data['email'])
            ->first();

        if($record){
            if(Hash::check($data['password'], $record->password)){
                session()->regenerate();

                $token = tenancy()->impersonate($record, 1, '/app', 'web');

                return redirect()->to('https://' . $record->domains[0]->domain . '.' . config('app.domain') . '/login/url?token=' . $token->token . '&email=' . $record->email);
            }
            else {
                Notification::make()
                    ->title('Invalid Credentials')
                    ->body('Please check your email and password')
                    ->danger()
                    ->send();
            }
        }
        else {
            Notification::make()
                ->title('Invalid Credentials')
                ->body('Please check your email and password')
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.login');
    }
}
