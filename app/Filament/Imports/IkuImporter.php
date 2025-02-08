<?php

namespace App\Filament\Imports;

use App\Models\Iku;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class IkuImporter extends Importer
{
    protected static ?string $model = Iku::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('tahun')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('sasaran')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('iku')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('target')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('satuan')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Iku
    {
        // return Iku::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Iku();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your iku import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
