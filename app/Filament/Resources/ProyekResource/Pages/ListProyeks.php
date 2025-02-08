<?php

namespace App\Filament\Resources\ProyekResource\Pages;

use App\Filament\Resources\ProyekResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProyeks extends ListRecords
{
    protected static string $resource = ProyekResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
