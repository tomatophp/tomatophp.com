<?php

namespace App\Livewire;

use App\Models\Account;
use App\Models\Tenant;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Events\Auth\Registered;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class RegisterOtp extends Component implements HasActions, HasForms
{
    use InteractsWithFormActions;
    use InteractsWithForms;
    use InteractsWithActions;
    use WithRateLimiting;

    public array $data;
    public $user;
    public string $otp;

    public function mount(): void
    {
        if(!session()->has('demo_user')){
            abort(404);
        }
        else {
            $this->user = Account::query()->find(session('demo_user'));
        }
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('otp')
                ->label('OTP Code')
                ->numeric()
                ->maxLength(6)
                ->autocomplete('current-password')
                ->required()
                ->extraInputAttributes(['tabindex' => 2])
        ])->statePath('data');
    }

    public function submitAction()
    {
        return Action::make('submitAction')
            ->label('Check')
            ->action(function (){
                $this->authenticate();
            });
    }

    protected function throwFailureOtpException(): never
    {
        throw ValidationException::withMessages([
            'data.otp' => "otp not correct",
        ]);
    }
    public function authenticate()
    {
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

        $data = $this->form->getState();
        $user = $this->user;


        if($data['otp'] != $user->otp_code){
            $this->throwFailureOtpException();
        }

        $user->is_active = true;
        $user->otp_code = true;
        $user->otp_activated_at = now();
        $user->save();

        event(new Registered($user));

        Filament::auth()->login($user);

        session()->regenerate();

        return redirect()->to('/user');
    }

    protected function getResendAction(): Action
    {
        return Action::make('getResendAction')
            ->requiresConfirmation()
            ->fillForm(['email' => $this->user->email])
            ->form([
                TextInput::make('email')
                    ->required()
                    ->email()
                    ->label('Please enter your email'),
            ])
            ->link()
            ->label('Resend OTP')
            ->color('warning')
            ->action(function (array $data){
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

                $otp = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
                session()->put('demo_otp', $otp);
                $data = json_decode(session()->get('demo_user'));

                try {
                    $embeds = [];
                    $embeds['description'] = "your OTP is: ". $otp;
                    $embeds['url'] = url('/otp');

                    $params = [
                        'content' => "@" . $data->domain,
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
            });
    }

    public function render()
    {
        return view('livewire.register-otp');
    }

}
