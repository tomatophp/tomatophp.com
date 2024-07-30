<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Filament\Resources\TenantResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Modules\Core\Models\Tenant;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->unique(table:'tenants', ignoreRecord: true)->live(onBlur: true)
                        ->afterStateUpdated(function(Forms\Set $set, $state) {
                            $set('id', $slug = \Str::of($state)->slug('_')->toString());
                            $set('domain', \Str::of($state)->slug()->toString());
                        }),
                    Forms\Components\TextInput::make('id')
                        ->label('Unique ID')
                        ->required()
                        ->disabled(fn($context) => $context !=='create')
                        ->unique(table: 'tenants', ignoreRecord: true),
                    Forms\Components\TextInput::make('domain')
                        ->label('Sub-Domain')
                        ->required()
                        ->visible(fn($context) => $context ==='create')
                        ->unique(table: 'domains',ignoreRecord: true)
                        ->prefix('https://')
                        ->suffix(".".request()->getHost())
                    ,
                    Forms\Components\TextInput::make('email')->required()->email(),
                    Forms\Components\TextInput::make('phone')->required()->tel(),
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
                    Forms\Components\ToggleButtons::make('packages')
                        ->label('Plugins')
                        ->multiple()
                        ->inline()
                        ->hint('Select the plugins you want to install')
                        ->icons(collect(config('app.packages'))->pluck('icon', 'key')->toArray())
                        ->view('components.packages')
                        ->columnSpanFull()
                        ->required()
                        ->default(["filament-users"])
                        ->options(collect(config('app.packages'))->pluck('label', 'key')->toArray()),
                ])->columns()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            RelationManagers\DomainsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'view' => Pages\ViewTenant::route('/{record}'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
