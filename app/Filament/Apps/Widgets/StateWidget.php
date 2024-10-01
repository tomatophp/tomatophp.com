<?php

namespace App\Filament\Apps\Widgets;

use App\Models\Like;
use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use TomatoPHP\FilamentCms\Models\Comment;

class StateWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make(trans('cms::messages.widgets.demos'), Tenant::query()->where('account_id', auth('accounts')->user()->id)->count())
                ->chart(collect(range(1, 10))->map(fn ($item) => (float)rand(1,1000))->toArray())
                ->color('info')
                ->icon('heroicon-o-globe-alt'),
            Stat::make(trans('cms::messages.widgets.likes'), Like::query()->where('account_id', auth('accounts')->user()->id)->count())
                ->chart(collect(range(1, 10))->map(fn ($item) => (float)rand(1,1000))->toArray())
                ->color('danger')
                ->icon('heroicon-o-heart'),
            Stat::make(trans('cms::messages.widgets.comments'), Comment::query()->where('user_id', auth('accounts')->user()->id)->count())
                ->chart(collect(range(1, 10))->map(fn ($item) => (float)rand(1,1000))->toArray())
                ->color('warning')
                ->icon('heroicon-o-chat-bubble-left-right'),
        ];
    }
}
