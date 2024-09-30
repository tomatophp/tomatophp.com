<?php

namespace App\Filament\Apps\Resources\DemosResource\Pages;

use App\Filament\Apps\Resources\DemosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDemos extends EditRecord
{
    protected static string $resource = DemosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
