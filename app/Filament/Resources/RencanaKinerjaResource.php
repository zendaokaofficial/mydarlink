<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\RencanaKinerja;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use App\Tables\Columns\ProgressColumn;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RencanaKinerjaResource\Pages;
use App\Filament\Resources\RencanaKinerjaResource\RelationManagers;

class RencanaKinerjaResource extends Resource
{
    protected static ?string $model = RencanaKinerja::class;

    protected static ?string $navigationLabel = 'Rencana Kinerja';

    protected static ?string $navigationGroup = 'Pelaksanaan Kinerja';

    protected static ?string $navigationIcon = 'heroicon-m-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Rencana Kinerja')->schema([
                    Select::make('proyek_id') // Relasi ke Proyek
                        ->label('Proyek')
                        ->relationship('proyek', 'nama_proyek')
                        ->required()
                        ->placeholder('Pilih Proyek')
                        ->searchable(),
                    TextInput::make('rencana_kinerja')
                        ->label('Rencana Kinerja')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextArea::make('description')
                        ->required()
                        ->maxLength(255)
                        ->label('Deskripsi'),
                    DateTimePicker::make('start_at')
                        ->required()
                        ->default(now())
                        ->label('Waktu Mulai')
                        ->reactive()
                        ->seconds(false)
                        ->timezone('Asia/Makassar'),
                    DateTimePicker::make('end_at')
                        ->required()
                        ->label('Waktu Selesai')
                        ->reactive()
                        ->rules([
                            fn ($get) => function (string $attribute, $value, $fail) use ($get) {
                                Log::info('Validasi Waktu', [
                                    'start_at' => $get('start_at'),
                                    'end_at' => $value
                                ]);

                                if ($get('start_at') && $value <= $get('start_at')) {
                                    $fail('Waktu selesai harus lebih besar dari waktu mulai.');
                                }
                            },
                        ])
                        ->validationMessages([
                            'after' => 'Waktu selesai harus lebih besar dari waktu mulai.',
                        ])
                        ->seconds(false)
                        ->timezone('Asia/Makassar'),
                    Select::make('tempat')
                        ->options([
                            'Ruang Rapat' => 'Ruang Rapat',
                            'PST' => 'PST',
                            'Kantor' => 'Kantor',
                            'Luar Kantor' => 'Luar Kantor',
                            'Online' => 'Online',
                            'Lainnya' => 'Lainnya',
                        ])
                        ->required()
                        ->reactive()
                        ->placeholder('Pilih Tempat'),
                    TextInput::make('tempat_lainnya')
                        ->required(fn ($get) => $get('tempat') === 'Lainnya')
                        ->visible(fn ($get) => $get('tempat') === 'Lainnya')
                        ->placeholder('Isi Tempat Lainnya'),
                    CheckboxList::make('users')
                        ->label('Nama Pegawai')
                        ->relationship('users', 'name')
                        ->options(function () {
                            return \App\Models\User::where('role', '!=', 'Admin')->pluck('name', 'id');
                        })
                        ->columns(3),
                ]),
                Section::make()->schema([
                    Select::make('kategori')
                        ->options([
                            'Rapat' => 'Rapat',
                            'Supervisi/Pengawasan' => 'Supervisi/Pengawasan',
                            'Zoom' => 'Zoom',
                            'Pelatihan/Briefing' => 'Pelatihan/Briefing',
                            'SPPD' => 'SPPD',
                            'Cuti' => 'Cuti',
                            'Deadline' => 'Deadline',
                            'Lainnya' => 'Lainnya',
                        ])
                        ->required()
                        ->placeholder('Pilih Kategori'),
                    TextInput::make('target')
                        ->required()
                        ->placeholder('Target'),
                    Select::make('satuan')
                        ->options([
                            '%' => '%',
                            'Kegiatan' => 'Kegiatan',
                            'Ruta' => 'Ruta',
                            'Usaha' => 'Usaha',
                            'Poin' => 'Poin',
                            'Pemda' => 'Pemda',
                            'Satker' => 'Satker',
                        ])
                        ->required()
                        ->placeholder('Pilih Satuan'),
                    TextInput::make('realisasi')
                        ->required()
                        ->placeholder('Realisasi')
                        ->rules([
                            fn ($get) => function (string $attribute, $value, $fail) use ($get) {
                                Log::info('Validasi Realisasi', [
                                    'target' => $get('target'),
                                    'realisasi' => $value
                                ]);

                                if ($get('target') && $value > $get('target')) {
                                    $fail('Realisasi tidak boleh lebih besar dari target.');
                                }
                            },
                        ])
                        ->validationMessages([
                            'max' => 'Realisasi tidak boleh lebih besar dari target.',
                        ]),

                ]),
                Section::make()->schema([
                    TextInput::make('daftar_hadir')
                        ->placeholder('Daftar Hadir'),
                    TextInput::make('rekap_daftar_hadir')
                        ->placeholder('Rekap Daftar Hadir'),
                    TextInput::make('link_materi')
                        ->placeholder('Link Materi'),
                    TextInput::make('notulensi')
                        ->placeholder('Notulensi'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ])
            ->filters([
                //
                SelectFilter::make('users')
                    ->label('Nama Pegawai')
                    ->relationship('users', 'name') // Uses the Many-to-Many relation
                    ->multiple() // Allows selecting multiple users
                    ->searchable()
                    ->preload()
                    ->default(fn () => Auth::check() ? [Auth::id()] : []),

                SelectFilter::make('tempat')
                    ->options([
                        'Ruang Rapat' => 'Ruang Rapat',
                        'PST' => 'PST',
                        'Kantor' => 'Kantor',
                        'Luar Kantor' => 'Luar Kantor',
                        'Online' => 'Online',
                        'Lainnya' => 'Lainnya',
                    ]),
                SelectFilter::make('kategori')
                    ->options([
                        'Rapat' => 'Rapat',
                        'Supervisi/Pengawasan' => 'Supervisi/Pengawasan',
                        'Zoom' => 'Zoom',
                        'Pelatihan/Briefing' => 'Pelatihan/Briefing',
                        'SPPD' => 'SPPD',
                        'Cuti' => 'Cuti',
                        'Deadline' => 'Deadline',
                        'Lainnya' => 'Lainnya',
                    ]),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRencanaKinerjas::route('/'),
            /*'create' => Pages\CreateRencanaKinerja::route('/create'),
            'edit' => Pages\EditRencanaKinerja::route('/{record}/edit'),*/
        ];
    }

    public static function getPluralModelLabel(): string
    {
        return 'Rencana Kinerja'; // Mengubah header daftar menjadi "Artikel"
    }

    public static function getModelLabel(): string
    {
        return 'Rencana Kinerja'; // Mengubah header detail menjadi "Artikel"
    }
}
