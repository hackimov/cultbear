<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Theme;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Товар';

    protected static ?string $pluralModelLabel = 'Товары';

    protected static ?string $navigationLabel = 'Товары';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('theme_id')
                    ->label('Тематика')
                    ->options(Theme::query()->orderBy('sort_order')->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('name')->label('Название')->required(),
                Forms\Components\TextInput::make('slug')->label('Slug (URL)')->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('article')->label('Артикул')->required()->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('description')->label('Описание'),
                Forms\Components\TextInput::make('base_price')->label('Базовая цена, ₽')->numeric()->required(),
                Forms\Components\Toggle::make('is_pinned')->label('Закрепить в топе')->default(false),
                Forms\Components\TextInput::make('sort_order')->label('Порядок сортировки')->numeric()->default(0)->required(),
                Forms\Components\Toggle::make('is_active')->label('Активен')->default(true),
                Forms\Components\Repeater::make('media')
                    ->label('Медиа')
                    ->relationship('media')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options(['image' => 'Изображение', 'video' => 'Видео'])
                            ->default('image')
                            ->required()
                            ->reactive(),
                        Forms\Components\FileUpload::make('path')
                            ->label('Файл')
                            ->disk('s3')
                            ->directory('products/media')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'video/mp4', 'video/webm'])
                            ->maxSize(10240)
                            ->required(),
                        Forms\Components\Toggle::make('is_primary')->label('Основное')->default(false),
                        Forms\Components\TextInput::make('sort_order')->label('Порядок')->numeric()->default(0),
                    ])
                    ->columnSpanFull()
                    ->defaultItems(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Название')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('article')->label('Артикул')->searchable(),
                Tables\Columns\TextColumn::make('theme.name')->label('Тематика'),
                Tables\Columns\TextColumn::make('base_price')->label('Цена')->money('RUB'),
                Tables\Columns\IconColumn::make('is_pinned')->label('В топе')->boolean(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
