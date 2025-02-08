<?php

namespace App\Filament\Resources\RencanaKinerjaResource\Pages;

use App\Filament\Resources\RencanaKinerjaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRencanaKinerja extends EditRecord
{
    protected static string $resource = RencanaKinerjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
