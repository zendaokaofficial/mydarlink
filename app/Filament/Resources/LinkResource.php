<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LinkResource\Pages;
use App\Filament\Resources\LinkResource\RelationManagers;
use App\Models\Link;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LinkResource extends Resource
{
    protected static ?string $model = Link::class;

    protected static ?string $navigationIcon = 'heroicon-c-link';

    protected static ?string $navigationGroup = 'Repository';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Link Details')->schema([
                    Forms\Components\TextInput::make('judul')
                        ->required()
                        ->label('Judul'),
                    Forms\Components\Textarea::make('deskripsi'),
                    Forms\Components\TextInput::make('url')
                        ->required()
                        ->label('URL'),
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
                //
                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('url')
                    ->searchable()
                    ->sortable()
                    ->url(fn (string $state): string =>
                        str_starts_with($state, 'http') ? $state : "https://$state",
                        true
                    ),
                Tables\Columns\TextColumn::make('klasifikasi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategori')
                    ->searchable()
                    ->sortable(),
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
                    ]),
                SelectFilter::make('kategori')
                    ->options([
                        'Link' => 'Link',
                        'Repositori' => 'Repositori',
                        'Dokumentasi' => 'Lainnya',
                    ]),
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
            'index' => Pages\ListLinks::route('/'),
            /*'create' => Pages\CreateLink::route('/create'),
            'edit' => Pages\EditLink::route('/{record}/edit'),*/
        ];
    }
}
