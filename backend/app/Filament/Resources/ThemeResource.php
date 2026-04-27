<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Concerns\HasAccusativeCreateTitle;
use App\Filament\Resources\ThemeResource\Pages;
use App\Models\Theme;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ThemeResource extends Resource
{
    use HasAccusativeCreateTitle;

    protected static ?string $recordTitleAttribute = 'name';

    protected static function createAccusativeLabel(): ?string
    {
        return 'тематику';
    }

    protected static ?string $model = Theme::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $modelLabel = 'Тематика';

    protected static ?string $pluralModelLabel = 'Тематики';

    protected static ?string $navigationLabel = 'Тематики';

    protected static ?int $navigationSort = 5;

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function getThemeFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')->label('Название')->required()->maxLength(255),
            Forms\Components\TextInput::make('slug')->label('Slug (URL)')->required()->unique(ignoreRecord: true),
            Forms\Components\Textarea::make('description')->label('Описание')->rows(3),
            Forms\Components\FileUpload::make('banner_url')
                ->label('Баннер')
                ->helperText('JPEG, PNG или WebP.')
                ->image()
                ->disk('s3')
                ->directory('themes/banners')
                ->visibility('public')
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->maxSize(10240)
                ->nullable(),
            Forms\Components\Select::make('layout_columns')
                ->label('Колонок в сетке')
                ->placeholder('Выберите сетку')
                ->options([2 => '2 колонки', 3 => '3 колонки', 4 => '4 колонки'])
                ->required(),
            Forms\Components\TextInput::make('sort_order')->label('Порядок сортировки')->numeric()->default(0)->required(),
            Forms\Components\Toggle::make('is_active')->label('Активна')->default(true),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(static::getThemeFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('banner_url')
                    ->label('Баннер')
                    ->getStateUsing(fn (Theme $record): ?string => $record->banner_src)
                    ->height(40),
                Tables\Columns\TextColumn::make('name')->label('Название')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->label('Slug')->searchable(),
                Tables\Columns\TextColumn::make('layout_columns')->label('Сетка'),
                Tables\Columns\IconColumn::make('is_active')->label('Активна')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('Порядок')->sortable(),
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
            ])
            ->reorderable('sort_order');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('is_home_theme', false);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListThemes::route('/'),
            'create' => Pages\CreateTheme::route('/create'),
            'edit' => Pages\EditTheme::route('/{record}/edit'),
        ];
    }
}
