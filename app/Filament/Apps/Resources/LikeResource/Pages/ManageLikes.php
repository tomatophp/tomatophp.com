<?php

namespace App\Filament\Apps\Resources\LikeResource\Pages;

use App\Filament\Apps\Resources\LikeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLikes extends ManageRecords
{
    protected static string $resource = LikeResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
