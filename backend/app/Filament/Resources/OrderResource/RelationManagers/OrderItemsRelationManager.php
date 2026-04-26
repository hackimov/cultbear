<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Позиции заказа';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product_name')->label('Товар'),
                Tables\Columns\TextColumn::make('sku_variant')->label('Артикул'),
                Tables\Columns\TextColumn::make('quantity')->label('Кол-во'),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Цена')
                    ->formatStateUsing(fn ($state): string => number_format((int) $state, 0, ',', ' ').' ₽'),
                Tables\Columns\TextColumn::make('line_total')
                    ->label('Сумма')
                    ->formatStateUsing(fn ($state): string => number_format((int) $state, 0, ',', ' ').' ₽'),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
