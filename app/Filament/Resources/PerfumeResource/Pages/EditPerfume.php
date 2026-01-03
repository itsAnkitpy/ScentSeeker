<?php

namespace App\Filament\Resources\PerfumeResource\Pages;

use App\Filament\Resources\PerfumeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPerfume extends EditRecord
{
    protected static string $resource = PerfumeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
