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
            "is_public" => $this->getUser()->meta('is_public')??false,
            "location" => $this->getUser()->meta('location')??null,
            "bio" => $this->getUser()->meta('bio')??null,
            "social" => $this->getUser()->meta('social')??null,
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
                        ->label("Allow Public Profile"),
                    Forms\Components\TextInput::make('location')
                        ->visible(fn(Forms\Get $get) => $get('is_public'))
                        ->label("Location")
                        ->columnSpan(2),
                    Forms\Components\Textarea::make('bio')
                        ->visible(fn(Forms\Get $get) => $get('is_public'))
                        ->label("Bio")
                        ->rows(3)
                        ->columnSpan(2),
                    Forms\Components\Repeater::make('social')
                        ->visible(fn(Forms\Get $get) => $get('is_public'))
                        ->columns(2)
                        ->schema([
                            Forms\Components\Select::make('network')
                                ->searchable()
                                ->options([
                                    "github" => "GitHub",
                                    "twitter" => "Twitter",
                                    "linkedin" => "LinkedIn",
                                    "whatsapp" => "Whatsapp",
                                    "facebook" => "Facebook",
                                    "instagram" => "Instagram",
                                    "youtube" => "Youtube",
                                    "twitch" => "Twitch",
                                    "reddit" => "Reddit",
                                    "behance" => "Behance",
                                    "dribbble" => "Dribbble",
                                    "link" => "Website"
                                ])
                                ->live()
                                ->required(),
                            Forms\Components\Hidden::make('url'),
                            Forms\Components\TextInput::make('username')
                                ->lazy()
                                ->prefix(function(Forms\Get $get){
                                    return match($get('network')){
                                        "github" => "https://www.gitHub.com/",
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
                                })
                                ->afterStateUpdated(function(Forms\Get $get, Forms\Set $set){
                                    $networkURl = match($get('network')){
                                        "github" => "https://www.gitHub.com/",
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
                                    $set('url',$networkURl . $get('username'));
                                })
                                ->suffixIcon('heroicon-o-link')
                                ->required()
                        ])
                ]),
        ])
        ->statePath('publicProfileData');
    }


    public function updatePublicProfile()
    {
        $this->editPublicProfile->validate();

        $data = $this->editPublicProfile->getState();

        $this->getUser()->meta('is_public', $data['is_public']);
        isset($data['location'])? $this->getUser()->meta('location', $data['location']):null;
        isset($data['bio'])? $this->getUser()->meta('bio', $data['bio']):null;
        isset($data['social'])? $this->getUser()->meta('social', $data['social']):null;

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
