<?php

namespace App\Filament\Resources\RencanaKinerjaResource\Pages;

use App\Filament\Resources\RencanaKinerjaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRencanaKinerjas extends ListRecords
{
    protected static string $resource = RencanaKinerjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
