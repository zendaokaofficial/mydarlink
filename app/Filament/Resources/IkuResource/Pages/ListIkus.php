<?php

namespace App\Filament\Resources\IkuResource\Pages;

use App\Filament\Resources\IkuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIkus extends ListRecords
{
    protected static string $resource = IkuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
