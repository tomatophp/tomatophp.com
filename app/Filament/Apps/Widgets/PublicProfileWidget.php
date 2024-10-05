<?php

namespace App\Filament\Apps\Widgets;

use Filament\Widgets\Widget;

class PublicProfileWidget extends Widget
{
    protected int | string | array $columnSpan = 2;

    protected static string $view = 'filament.apps.widgets.public-profile-widget';

    public static function canView(): bool
    {
        return !(bool)auth('accounts')->user()->meta('is_public');
    }
}
