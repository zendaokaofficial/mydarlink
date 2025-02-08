<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Password;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\RelationManagers;
use Illuminate\Support\Facades\Auth;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('User Details')->schema([
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('email')
                        ->required(),
                    TextInput::make('password')
                        ->required()
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->visible(fn ($livewire) => $livewire instanceof CreateUser)
                        ->rule(Password::default()),
                    Select::make('role')
                    ->label('Role')
                    ->options([
                        'Admin' => 'Admin',
                        'Kepala BPS' => 'Kepala BPS',
                        'Ketua Tim' => 'Ketua Tim',
                        'Anggota Tim' => 'Anggota Tim',
                    ])->required(),
                Section::make('User New Password')->schema([
                    TextInput::make('new_password')
                        ->nullable()
                        ->password()
                        ->visible(fn ($livewire) => $livewire instanceof EditUser)
                        ->rule(Password::default()),
                    TextInput::make('new_password_confirmation')
                        ->password()
                        ->visible(fn ($livewire) => $livewire instanceof EditUser)
                        ->same('new_password')
                        ->requiredWith('new_password')
                ])->visible(fn ($livewire) => $livewire instanceof EditUser)
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('role')->searchable()->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        // Hanya Admin yang bisa melihat daftar pengguna
        return Auth::user()->role === 'Admin';
    }
}
