<?php

namespace App\Filament\Resources\StagingPerfumeResource\Pages;

use App\Filament\Resources\StagingPerfumeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStagingPerfume extends EditRecord
{
    protected static string $resource = StagingPerfumeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
