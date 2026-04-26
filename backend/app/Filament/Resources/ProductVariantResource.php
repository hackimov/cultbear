<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductVariantResource\Pages;
use App\Models\Product;
use App\Models\ProductVariant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductVariantResource extends Resource
{
    protected static ?string $model = ProductVariant::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $modelLabel = 'Вариант товара';

    protected static ?string $pluralModelLabel = 'Варианты товаров';

    protected static ?string $navigationLabel = 'Варианты';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Товар')
                    ->options(Product::query()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('model')->label('Модель')->required(),
                Forms\Components\TextInput::make('size')->label('Размер')->required(),
                Forms\Components\TextInput::make('color')->label('Цвет')->required(),
                Forms\Components\TextInput::make('sku_variant')->label('SKU варианта')->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('price')->label('Цена, ₽')->numeric()->required(),
                Forms\Components\TextInput::make('stock_quantity')->label('Остаток на складе')->numeric()->required(),
                Forms\Components\Toggle::make('is_active')->label('Активен')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Товар')->searchable(),
                Tables\Columns\TextColumn::make('sku_variant')->label('SKU')->searchable(),
                Tables\Columns\TextColumn::make('model')->label('Модель'),
                Tables\Columns\TextColumn::make('size')->label('Размер'),
                Tables\Columns\TextColumn::make('color')->label('Цвет'),
                Tables\Columns\TextColumn::make('price')->label('Цена')->money('RUB'),
                Tables\Columns\TextColumn::make('stock_quantity')->label('Остаток')->sortable(),
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
            'index' => Pages\ListProductVariants::route('/'),
            'create' => Pages\CreateProductVariant::route('/create'),
            'edit' => Pages\EditProductVariant::route('/{record}/edit'),
        ];
    }
}
