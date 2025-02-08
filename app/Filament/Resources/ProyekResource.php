<?php

namespace App\Filament\Resources;

use App\Models\Iku;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Proyek;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProyekResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProyekResource\RelationManagers;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class ProyekResource extends Resource
{
    protected static ?string $model = Proyek::class;

    protected static ?string $navigationLabel = 'Proyek';

    protected static ?string $navigationGroup = 'Pelaksanaan Kinerja';

    protected static ?string $navigationIcon = 'heroicon-m-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Proyek')->schema([
                    TextInput::make('rencana_kinerja')
                        ->required()
                        ->label('Rencana Kinerja')
                        ->disabled(fn () => Auth::user()->role === 'Anggota Tim'),

                    TextInput::make('nama_proyek')
                        ->required()
                        ->label('Nama Proyek')
                        ->disabled(fn () => Auth::user()->role === 'Anggota Tim'),

                    // Field untuk memilih tahun
                    Select::make('tahun')
                        ->label('Tahun')
                        ->options([
                            '2024' => '2024',
                            '2025' => '2025',
                            '2026' => '2026',
                        ]) // Menampilkan tahun 10 tahun terakhir
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set) {
                            // Reset IKU jika tahun berubah
                            $set('iku_id', null);
                        })
                        ->disabled(fn () => Auth::user()->role === 'Anggota Tim'),

                    // Field untuk memilih IKU berdasarkan tahun
                    Select::make('iku_id')
                        ->label('IKU')
                        ->options(function (callable $get) {
                            $tahun = $get('tahun');
                            Log::debug('Tahun yang dipilih:', ['tahun' => $tahun]);

                            $ikuOptions = Iku::where('tahun', $tahun)
                                ->pluck('iku', 'id')
                                ->toArray();

                            Log::debug('IKU Options:', ['ikuOptions' => $ikuOptions]);

                            return $ikuOptions;
                        })
                        ->required()
                        ->disabled(fn () => Auth::user()->role === 'Anggota Tim'),

                    // Menambahkan MultiSelect untuk memilih anggota proyek
                    CheckboxList::make('anggota')
                        ->label('Anggota Proyek')
                        ->relationship('anggota', 'name')  // Menghubungkan relasi many-to-many dengan User
                        ->required()
                        ->disabled(fn () => Auth::user()->role === 'Anggota Tim'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('iku.iku')
                    ->label('IKU')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Ketua Tim')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('rencana_kinerja')
                    ->label('Rencana Kinerja')->searchable(),
                Tables\Columns\TextColumn::make('nama_proyek')
                    ->label('Nama Proyek')->searchable(),
            ])
            ->filters([
                //

                Tables\Filters\SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->options([
                        '2024' => '2024',
                        '2025' => '2025',
                        '2026' => '2026',
                    ]),
                Tables\Filters\SelectFilter::make('iku_id')
                    ->label('IKU')
                    ->options(Iku::all()
                    ->pluck('iku', 'id')->toArray()),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Ketua Tim')
                    ->relationship('user', 'name') // Menggunakan relasi ke tabel users
                    ->searchable(),
                Tables\Filters\SelectFilter::make('anggota')
                    ->label('Anggota Proyek')
                    ->relationship('anggota', 'name') // Gunakan relasi many-to-many
                    ->searchable(),

            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4)

            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProyeks::route('/'),
            /*
            'create' => Pages\CreateProyek::route('/create'),
            'edit' => Pages\EditProyek::route('/{record}/edit'),*/
        ];
    }

    public static function getPluralModelLabel(): string
    {
        return 'Proyek'; // Mengubah header daftar menjadi "Artikel"
    }

    public static function getModelLabel(): string
    {
        return 'Proyek'; // Mengubah header detail menjadi "Artikel"
    }

    public static function canCreate(): bool
    {
        return in_array(Auth::user()->role, ['Ketua Tim', 'Admin']);
    }

    public static function canUpdate(): bool
    {
        return in_array(Auth::user()->role, ['Ketua Tim', 'Admin']);
    }

    public static function canEdit(Model $record): bool
    {
        return in_array(Auth::user()->role, ['Ketua Tim', 'Admin', 'Anggota Tim']);
    }

}
