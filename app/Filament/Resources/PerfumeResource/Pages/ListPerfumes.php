<?php

namespace App\Filament\Resources\PerfumeResource\Pages;

use App\Filament\Resources\PerfumeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPerfumes extends ListRecords
{
    protected static string $resource = PerfumeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
