<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Demo model for tomatophp/filament-translation-component and tomatophp/filament-icons.
 */
#[Fillable(['name', 'description', 'icon'])]
class Category extends Model
{
    /**
     * filament-cms owns the `categories` table in the demo.
     */
    protected $table = 'showcase_categories';

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
        ];
    }

    /**
     * The value of a translated attribute in the current locale, falling back to English, then any locale.
     */
    public function translated(string $attribute): string
    {
        $values = $this->{$attribute} ?? [];

        return (string) ($values[app()->getLocale()] ?? $values['en'] ?? (reset($values) ?: ''));
    }
}
