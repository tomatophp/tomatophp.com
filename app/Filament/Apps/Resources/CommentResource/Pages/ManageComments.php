<?php

namespace App\Filament\Apps\Resources\CommentResource\Pages;

use App\Filament\Apps\Resources\CommentResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageComments extends ManageRecords
{
    protected static string $resource = CommentResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
