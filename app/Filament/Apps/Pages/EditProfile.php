<?php

namespace App\Filament\Apps\Pages;

use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use TomatoPHP\FilamentAccounts\Filament\Pages\EditProfile\HasBrowserSessions;
use TomatoPHP\FilamentAccounts\Filament\Pages\EditProfile\HasDeleteAccount;
use TomatoPHP\FilamentAccounts\Filament\Pages\EditProfile\HasEditPassword;
use TomatoPHP\FilamentAccounts\Filament\Pages\EditProfile\HasEditProfile;
use TomatoPHP\FilamentAccounts\Forms\EditProfileForm;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;
    use HasEditPassword;
    use HasBrowserSessions;
    use HasDeleteAccount;

    public array $publicProfileData = [];

    public function editProfileForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(trans('filament-accounts::messages.profile.edit.title'))
                    ->description(trans('filament-accounts::messages.profile.edit.description'))
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('avatar')
                            ->image()
                            ->alignCenter()
                            ->avatar()
                            ->circleCropper()
                            ->collection('avatar')
                            ->columnSpan(2)
                            ->label(trans('filament-accounts::messages.accounts.coulmns.avatar')),
                        Forms\Components\SpatieMediaLibraryFileUpload::make('cover')
                            ->image()
                            ->collection('cover')
                            ->columnSpan(2)
                            ->label("Cover"),
                        Forms\Components\TextInput::make('name')
                            ->columnSpan(2)
                            ->label(trans('filament-accounts::messages.profile.edit.name'))
                            ->required(),
                        Forms\Components\TextInput::make('username')
                            ->unique(table:'accounts', ignorable: $this->getUser())
                            ->columnSpan(2)
                            ->label("Username")
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->columnSpan(2)
                            ->label(trans('filament-accounts::messages.profile.edit.email'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                    ]),
            ])
            ->model($this->getUser())
            ->statePath('profileData');
    }

    protected function getUpdateProfileFormActions(): array
    {
        return [
            Action::make('updateProfileAction')
                ->label(trans('filament-accounts::messages.save'))
                ->submit('editProfileForm'),
        ];
    }

    public function updateProfile(): void
    {
        try {
            $data = $this->editProfileForm->getState();

            $this->handleRecordUpdate($this->getUser(), $data);
        } catch (Halt $exception) {
            return;
        }

        $this->sendSuccessNotification();
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }

    protected static string $view = 'cms::pages.edit-profile';

    protected ?string $maxWidth = '6xl';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function getTitle(): string
    {
        return  trans('filament-accounts::messages.profile.title');
    }

    public static function getNavigationLabel(): string
    {
        return  trans('filament-accounts::messages.profile.title');
    }

    public static function canAccess(): bool
    {
        return true;
    }

    public static function shouldShowDeleteAccountForm()
    {
        return true;
    }

    public static function shouldShowBrowserSessionsForm()
    {
        return true;
    }

    public static function shouldShowSanctumTokens()
    {
        return true;
    }

    public ?array $profileData = [];
    public ?array $passwordData = [];

    public function mount(): void
    {
        $this->fillForms();


    }

    protected function getForms(): array
    {
        return [
            'editProfileForm',
            'editPasswordForm',
            'deleteAccountForm',
            'browserSessionsForm',
            'editPublicProfile',
        ];
    }

    protected function fillForms(): void
    {
        $data = $this->getUser()->attributesToArray();

        $this->editProfileForm->fill($data);
        $this->editPasswordForm->fill();
        $this->editPublicProfile->fill([
            "is_public" => $this->getUser()->meta('is_public'),
        ]);
    }

    public function getUser()
    {
        return auth('accounts')->user();
    }

    public function editPublicProfile(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make("Public Profile")
                ->description("Control your public profile visibility")
                ->schema([
                    Forms\Components\Toggle::make('is_public')
                        ->live()
                        ->label("Allow Public Profile")
                ]),
        ])
        ->statePath('publicProfileData');
    }


    public function updatePublicProfile()
    {
        $this->editPublicProfile->validate();

        $data = $this->editPublicProfile->getState();

        $this->getUser()->meta('is_public', $data['is_public']);

        $this->sendSuccessNotification();
    }

    public function getUpdatePublicProfileAction(): array
    {
        return [
            Action::make('getUpdatePublicProfileAction')
                ->label('Save')
                ->submit('editPublicProfile')
        ];
    }

    public function sendSuccessNotification()
    {
        Notification::make()
            ->title("Success")
            ->success()
            ->send();
    }
}
