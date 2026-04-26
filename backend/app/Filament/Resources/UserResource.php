<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Concerns\HasAccusativeCreateTitle;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    use HasAccusativeCreateTitle;

    protected static ?string $recordTitleAttribute = 'name';

    protected static function createAccusativeLabel(): ?string
    {
        return 'пользователя';
    }

    protected static function roleTitle(string $name): string
    {
        return match ($name) {
            'super_admin' => 'Супер-администратор',
            'admin' => 'Администратор',
            default => $name,
        };
    }

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Пользователь';

    protected static ?string $pluralModelLabel = 'Пользователи';

    protected static ?string $navigationLabel = 'Пользователи';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Имя')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('phone')
                    ->label('Телефон')
                    ->maxLength(20),
                Forms\Components\TextInput::make('password')
                    ->label('Пароль')
                    ->password()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                Forms\Components\Select::make('roles')
                    ->label('Роли')
                    ->relationship('roles', 'name')
                    ->options(
                        Role::query()
                            ->orderBy('name')
                            ->get(['id', 'name'])
                            ->mapWithKeys(fn (Role $role): array => [$role->id => static::roleTitle($role->name)])
                    )
                    ->preload()
                    ->multiple()
                    ->required()
                    ->visible(fn (): bool => auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Имя')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone')->label('Телефон'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Роли')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state ? static::roleTitle($state) : '—')
                    ->separator(','),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->actions([
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
