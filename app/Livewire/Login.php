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
            Forms\Components\Section::make(trans('cms::messages.login.title'))
                ->description(trans('cms::messages.login.description'))
                ->schema([
                    Forms\Components\TextInput::make('email')
                        ->label(trans('cms::messages.login.form.email'))
                        ->required()
                        ->email(),
                    Forms\Components\TextInput::make('password')
                        ->label(trans('cms::messages.login.form.password'))
                        ->required()
                        ->password()
                        ->revealable(filament()->arePasswordsRevealable())
                        ->rule(Password::default())
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
                $this->form->fill([]);

                session()->regenerate();

                $token = tenancy()->impersonate($record, 1, '/app', 'web');

                return redirect()->to('https://' . $record->domains[0]->domain . '.' . config('app.domain') . '/login/url?token=' . $token->token . '&email=' . $record->email);
            }
            else {
                Notification::make()
                    ->title(trans('cms::messages.login.notifications.error.title'))
                    ->body(trans('cms::messages.login.notifications.error.body'))
                    ->danger()
                    ->send();
            }
        }
        else {
            Notification::make()
                ->title(trans('cms::messages.login.notifications.error.title'))
                ->body(trans('cms::messages.login.notifications.error.body'))
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.login');
    }
}
