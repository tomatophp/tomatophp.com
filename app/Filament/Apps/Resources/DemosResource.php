<?php

namespace App\Filament\Apps\Resources;

use App\Filament\Apps\Resources\DemosResource\Pages;
use App\Filament\Apps\Resources\DemosResource\RelationManagers;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DemosResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';


    public static function getNavigationLabel(): string
    {
        return trans('cms::messages.demos.title');
    }

    public static function getPluralLabel(): ?string
    {
        return  trans('cms::messages.demos.title');
    }

    public static function getLabel(): ?string
    {
        return  trans('cms::messages.demos.single');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(trans('cms::messages.register.form.name'))
                    ->required()
                    ->unique(table:'tenants', ignoreRecord: true)->live(onBlur: true)
                    ->columnSpanFull()
                    ->afterStateUpdated(function(Forms\Set $set, $state) {
                        $set('id', $slug = \Str::of($state)->slug('_')->toString());
                        $set('domain', \Str::of($state)->slug()->toString());
                    }),
                Forms\Components\TextInput::make('id')
                    ->label(trans('cms::messages.register.form.id'))
                    ->readOnly()
                    ->required()
                    ->unique(table: 'tenants', ignoreRecord: true),
                Forms\Components\TextInput::make('domain')
                    ->label(trans('cms::messages.register.form.domain'))
                    ->readOnly()
                    ->required()
                    ->unique(table: 'domains',ignoreRecord: true)
                    ->prefix('https://')
                    ->suffix(".".request()->getHost()),
                Forms\Components\CheckboxList::make('packages')
                    ->label(trans('cms::messages.register.form.packages'))
                    ->hint(trans('cms::messages.register.form.packages_hint'))
                    ->bulkToggleable()
                    ->searchable()
                    ->columnSpanFull()
                    ->required()
                    ->view('components.packages')
                    ->descriptions(collect(config('app.packages'))->pluck('description', 'key')->toArray())
                    ->options(collect(config('app.packages'))->pluck('label', 'key')->toArray()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->whereHas('account', function (Builder $query) {
                    $query->where('id', auth('accounts')->user()->id);
                });
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(trans('filament-tenancy::messages.columns.id'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(trans('filament-tenancy::messages.columns.name'))
                    ->description(function ($record){
                        return "https://".$record->domains()->first()?->domain .'.'.config('filament-tenancy.central_domain'). '/app';
                    }),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->sortable()
                    ->label(trans('filament-tenancy::messages.columns.is_active'))
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(trans('filament-tenancy::messages.columns.is_active'))
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(trans('filament-tenancy::messages.actions.view'))
                    ->tooltip(trans('filament-tenancy::messages.actions.view'))
                    ->iconButton()
                    ->icon('heroicon-s-link')
                    ->url(fn($record) => "https://".$record->domains()->first()?->domain .'.'.config('filament-tenancy.central_domain'). '/app')
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('login')
                    ->label(trans('filament-tenancy::messages.actions.login'))
                    ->tooltip(trans('filament-tenancy::messages.actions.login'))
                    ->requiresConfirmation()
                    ->color('warning')
                    ->iconButton()
                    ->icon('heroicon-s-arrow-left-on-rectangle')
                    ->action(function ($record){
                        $token = tenancy()->impersonate($record, 1, '/app', 'web');

                        return redirect()->to('https://'.$record->domains[0]->domain.'.'. config('filament-tenancy.central_domain') . '/login/url?token='.$token->token .'&email='. $record->email);
                    }),
                Tables\Actions\Action::make('password')
                    ->label(trans('filament-tenancy::messages.actions.password'))
                    ->tooltip(trans('filament-tenancy::messages.actions.password'))
                    ->requiresConfirmation()
                    ->icon('heroicon-s-lock-closed')
                    ->iconButton()
                    ->color('danger')
                    ->form([
                        Forms\Components\TextInput::make('password')
                            ->label(trans('filament-tenancy::messages.columns.password'))
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->rule(Password::default())
                            ->autocomplete('new-password')
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->live(debounce: 500)
                            ->same('passwordConfirmation'),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label(trans('filament-tenancy::messages.columns.passwordConfirmation'))
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->dehydrated(false),
                    ])
                    ->action(function (array $data, $record) {
                        $record->password = bcrypt($data['password']);
                        $record->save();

                        Notification::make()
                            ->title(trans('filament-tenancy::messages.actions.notificaitons.password.title'))
                            ->body(trans('filament-tenancy::messages.actions.notificaitons.password.body'))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label(trans('filament-tenancy::messages.actions.delete'))
                    ->tooltip(trans('filament-tenancy::messages.actions.delete'))
                    ->iconButton(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDemos::route('/'),
        ];
    }
}
