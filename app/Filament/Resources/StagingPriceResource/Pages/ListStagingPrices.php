<?php

namespace App\Filament\Resources\StagingPriceResource\Pages;

use App\Filament\Resources\StagingPriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStagingPrices extends ListRecords
{
    protected static string $resource = StagingPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
