<?php

namespace App\Filament\Resources\IkuResource\Pages;

use App\Filament\Resources\IkuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIku extends EditRecord
{
    protected static string $resource = IkuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
