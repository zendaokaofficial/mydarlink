<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoteResource\Pages;
use App\Filament\Resources\NoteResource\RelationManagers;
use App\Models\Note;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NoteResource extends Resource
{
    protected static ?string $model = Note::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Repository';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Note Details')->schema([
                    Forms\Components\TextInput::make('judul')
                        ->required(),
                    RichEditor::make('isi')
                        ->required(),
                    Select::make('klasifikasi')
                        ->options([
                            'Kepala BPS' => 'Kepala BPS',
                            'Umum' => 'Umum',
                            'Sosial' => 'Sosial',
                            'Distribusi' => 'Distribusi',
                            'Produksi' => 'Produksi',
                            'Neraca' => 'Neraca',
                            'IPDS' => 'IPDS',
                        ])
                        ->required(),
                    Forms\Components\Select::make('kategori')
                        ->options([
                            'Link' => 'Link',
                            'Repositori' => 'Repositori',
                            'Dokumentasi' => 'Lainnya',
                        ])
                       ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    Tables\Columns\TextColumn::make('judul')
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('klasifikasi')
                        ->searchable()
                        ->sortable(),
                ]),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                //
                SelectFilter::make('klasifikasi')
                    ->options([
                        'Kepala BPS' => 'Kepala BPS',
                        'Umum' => 'Umum',
                        'Sosial' => 'Sosial',
                        'Distribusi' => 'Distribusi',
                        'Produksi' => 'Produksi',
                        'Neraca' => 'Neraca',
                        'IPDS' => 'IPDS',
                    ])
                    ->label('Klasifikasi'),
            ])
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotes::route('/'),
            /*'create' => Pages\CreateNote::route('/create'),
            'edit' => Pages\EditNote::route('/{record}/edit'),
            */
        ];
    }
}
