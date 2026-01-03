<?php

namespace App\Filament\Resources\StagingPerfumeResource\Pages;

use App\Filament\Resources\StagingPerfumeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStagingPerfumes extends ListRecords
{
    protected static string $resource = StagingPerfumeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
