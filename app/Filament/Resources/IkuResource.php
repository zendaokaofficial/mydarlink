<?php

namespace App\Filament\Resources;

use App\Models\Iku;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Imports\IkuImporter;
use Filament\Tables\Actions\ImportAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\IkuResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\IkuResource\RelationManagers;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class IkuResource extends Resource
{
    protected static ?string $model = Iku::class;

    protected static ?string $navigationLabel = 'Indikator Kinerja Utama';

    protected static ?string $navigationGroup = 'Pelaksanaan Kinerja';

    protected static ?string $navigationIcon = 'heroicon-s-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                    Section::make('IKU')->schema([
                        Forms\Components\TextInput::make('tahun')
                            ->required()
                            ->label('Tahun'),

                        Forms\Components\TextInput::make('sasaran')
                            ->required()
                            ->label('Sasaran'),

                        Forms\Components\TextInput::make('iku')
                            ->required()
                            ->label('IKU'),

                        Forms\Components\TextInput::make('target')
                            ->required()
                            ->numeric()
                            ->label('Target'),

                        Forms\Components\TextInput::make('satuan')
                            ->required()
                            ->label('Satuan'),
                    ]),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tahun')
                    ->sortable()
                    ->label('Tahun'),

                Tables\Columns\TextColumn::make('sasaran')
                    ->sortable()
                    ->label('Sasaran'),

                Tables\Columns\TextColumn::make('iku')
                    ->sortable()
                    ->label('IKU'),

                Tables\Columns\TextColumn::make('target')
                    ->sortable()
                    ->label('Target'),

                Tables\Columns\TextColumn::make('satuan')
                    ->sortable()
                    ->label('Satuan'),

                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->label('Created At'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(IkuImporter::class)
            ]);;
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
            'index' => Pages\ListIkus::route('/'),
        ];
    }

    public static function getPluralModelLabel(): string
    {
        return 'IKU'; // Mengubah header daftar menjadi "Artikel"
    }

    public static function getModelLabel(): string
    {
        return 'IKU'; // Mengubah header detail menjadi "Artikel"
    }

    public static function canCreate(): bool
    {
        return in_array(Auth::user()->role, ['Kepala BPS', 'Admin']);
    }

    public static function canDelete(Model $record): bool
    {
        return in_array(Auth::user()->role, ['Kepala BPS', 'Admin']);
    }

    public static function canUpdate(Model $record): bool
    {
        return in_array(Auth::user()->role, ['Kepala BPS', 'Admin']);
    }

    public static function canEdit(Model $record): bool
    {
        return in_array(Auth::user()->role, ['Kepala BPS', 'Admin']);
    }
}
