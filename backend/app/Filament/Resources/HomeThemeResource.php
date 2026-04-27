<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomeThemeResource\Pages;
use App\Models\Theme;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomeThemeResource extends Resource
{
    protected static ?string $model = Theme::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $modelLabel = 'Главная тематика';

    protected static ?string $pluralModelLabel = 'Главная тематика';

    protected static ?string $navigationLabel = 'Главная тематика';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema(ThemeResource::getThemeFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('banner_url')
                    ->label('Баннер')
                    ->getStateUsing(fn (Theme $record): ?string => $record->banner_src)
                    ->height(40),
                Tables\Columns\TextColumn::make('name')->label('Название')->searchable(),
                Tables\Columns\TextColumn::make('slug')->label('Slug'),
                Tables\Columns\IconColumn::make('is_active')->label('Активна')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->label('Обновлено')->dateTime('d.m.Y H:i'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomeThemes::route('/'),
            'create' => Pages\CreateHomeTheme::route('/create'),
            'edit' => Pages\EditHomeTheme::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('is_home_theme', true);
    }
}
