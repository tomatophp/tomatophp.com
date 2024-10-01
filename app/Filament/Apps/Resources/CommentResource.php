<?php

namespace App\Filament\Apps\Resources;

use App\Filament\Apps\Resources\CommentResource\Pages;
use App\Filament\Apps\Resources\CommentResource\RelationManagers;
use App\Models\Like;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use TomatoPHP\FilamentCms\Models\Comment;
use TomatoPHP\FilamentTypes\Components\TypeColumn;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';


    public static function getNavigationLabel(): string
    {
        return trans('cms::messages.comments.title');
    }

    public static function getPluralLabel(): ?string
    {
        return  trans('cms::messages.comments.title');
    }

    public static function getLabel(): ?string
    {
        return  trans('cms::messages.comments.single');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            MarkdownEditor::make('comment')
                ->label(trans('cms::messages.comments.comment'))
                ->columnSpanFull()
                ->required()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('content.title')
                    ->label(trans('cms::messages.comments.post'))
                    ->url(fn($record) => $record->content->type === 'open-source' ? url(app()->getLocale() . '/open-source/' . $record->content->slug) : url(app()->getLocale() . '/blog/' . $record->content->slug))
                    ->openUrlInNewTab()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment')
                    ->label(trans('cms::messages.comments.comment'))
                    ->state(fn($record) => str($record->comment)->limit(50))
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(trans('cms::messages.comments.comment'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageComments::route('/'),
        ];
    }
}
