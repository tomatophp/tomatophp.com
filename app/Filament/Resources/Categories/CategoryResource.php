<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use TomatoPHP\FilamentIcons\Components\IconColumn;
use TomatoPHP\FilamentIcons\Components\IconPicker;
use TomatoPHP\FilamentTranslationComponent\Components\Translation;
use UnitEnum;

/**
 * Showcases tomatophp/filament-translation-component and tomatophp/filament-icons.
 */
class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $slug = 'showcase-categories';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    protected static string|UnitEnum|null $navigationGroup = 'Showcase';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Translation::make('name')
                ->label('Name')
                ->columnSpanFull(),
            Translation::make('description')
                ->label('Description')
                ->textarea()
                ->columnSpanFull(),
            IconPicker::make('icon')
                ->label('Icon')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('icon')
                    ->label('Icon'),
                TextColumn::make('name')
                    ->label('Name')
                    ->state(fn (Category $record): string => $record->translated('name'))
                    ->weight('medium'),
                TextColumn::make('description')
                    ->label('Description')
                    ->state(fn (Category $record): string => $record->translated('description'))
                    ->limit(70),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCategories::route('/'),
        ];
    }
}
