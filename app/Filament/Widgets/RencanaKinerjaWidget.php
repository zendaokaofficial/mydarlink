<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\RencanaKinerja;
use Pages\ListRencanaKinerjas;
use Filament\Pages\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Filament\Resources\RencanaKinerjaResource;
use Filament\Tables\Actions\ViewAction as ActionsViewAction;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class RencanaKinerjaWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    use InteractsWithPageFilters;

    public function table(Table $table): Table
    {
        $query = RencanaKinerja::query();

        $filters = $this->filters ?? [];

        // Filter berdasarkan user_id jika ada
        if (!empty($filters['rencana_kinerja_user']['user_id'])) {
            $query->whereHas('users', function ($query) use ($filters) {
                $query->whereIn('users.id', $filters['rencana_kinerja_user']['user_id']);
            });
        }

        // Filter berdasarkan tempat jika ada
        if (!empty($filters['tempat'])) {
            $query->whereIn('tempat', $filters['tempat']);
        }

        // Filter berdasarkan kategori jika ada
        if (!empty($filters['kategori'])) {
            $query->whereIn('kategori', $filters['kategori']);
        }

        $columns =[
            Tables\Columns\TextColumn::make('proyek.nama_proyek')
                    ->label('Proyek')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rencana_kinerja')
                    ->label('Rencana Kinerja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_at')
                    ->label('Waktu Mulai')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_at')
                    ->label('Waktu Selesai')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tempat')
                    ->label('Tempat')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->toggleable(),
                TextColumn::make('progress')
                    ->label('Progress')
                    ->getStateUsing(function (RencanaKinerja $record) {
                        $progress = $record->target > 0 ? ($record->realisasi / $record->target) * 100 : 0;
                        return round($progress, 2);
                    })
                    ->suffix('%')
                    ->view('tables.columns.progress-column'),
                TextColumn::make('daftar_hadir')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($state): string =>
                        $state ? (str_starts_with($state, 'http') ? $state : "https://$state") : 'https://default-url.com')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('rekap_daftar_hadir')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($state): string =>
                        $state ? (str_starts_with($state, 'http') ? $state : "https://$state") : 'https://default-url.com')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('link_materi')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($state): string =>
                        $state ? (str_starts_with($state, 'http') ? $state : "https://$state") : 'https://default-url.com')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('notulensi')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($state): string =>
                        $state ? (str_starts_with($state, 'http') ? $state : "https://$state") : 'https://default-url.com')
                    ->toggleable(isToggledHiddenByDefault: true),
        ];
        return $table
            ->query($query)
            ->columns($columns)
            ->defaultSort('start_at', 'desc');
    }

}
