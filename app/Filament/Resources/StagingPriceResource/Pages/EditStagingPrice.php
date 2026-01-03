<?php

namespace App\Filament\Resources\StagingPriceResource\Pages;

use App\Filament\Resources\StagingPriceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStagingPrice extends EditRecord
{
    protected static string $resource = StagingPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
