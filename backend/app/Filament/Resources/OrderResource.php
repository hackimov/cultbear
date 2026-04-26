<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Concerns\HasAccusativeCreateTitle;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\OrderItemsRelationManager;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    use HasAccusativeCreateTitle;

    protected static ?string $model = Order::class;

    protected static ?string $recordTitleAttribute = 'number';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $modelLabel = 'Заказ';

    protected static ?string $pluralModelLabel = 'Заказы';

    protected static ?string $navigationLabel = 'Заказы';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Заказ')
                    ->schema([
                        Forms\Components\TextInput::make('number')
                            ->label('Номер')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->label('Статус заказа')
                            ->options([
                                'new' => 'Новый',
                                'paid' => 'Оплачен',
                                'cancelled' => 'Отменён',
                                'shipped' => 'Отправлен',
                                'completed' => 'Выполнен',
                            ])
                            ->required(),
                        Forms\Components\Select::make('payment_status')
                            ->label('Оплата')
                            ->options([
                                'awaiting_payment' => 'Ожидает оплаты',
                                'authorized' => 'Авторизована',
                                'paid' => 'Оплачена',
                                'declined' => 'Отклонена',
                                'refunded' => 'Возврат',
                            ])
                            ->required(),
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Дата оплаты')
                            ->seconds(false)
                            ->nullable(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Покупатель')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Пользователь')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Имя')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Доставка')
                    ->schema([
                        Forms\Components\Textarea::make('address_line')
                            ->label('Адрес')
                            ->rows(2)
                            ->maxLength(500),
                        Forms\Components\TextInput::make('city')
                            ->label('Город')
                            ->maxLength(120),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('Индекс')
                            ->maxLength(20),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Суммы')
                    ->description('В рублях, как в корзине и при оплате.')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal_amount')
                            ->label('Подытог')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Итого')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')->label('Номер')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Статус')->badge(),
                Tables\Columns\TextColumn::make('payment_status')->label('Оплата')->badge(),
                Tables\Columns\TextColumn::make('customer_name')->label('Покупатель')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('phone')->label('Телефон')->toggleable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Сумма')
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => number_format((int) $state, 0, ',', ' ').' ₽'),
                Tables\Columns\TextColumn::make('paid_at')->label('Оплачен')->dateTime('d.m.Y H:i')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->label('Создан')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
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
            ->emptyStateHeading('Заказов пока нет')
            ->emptyStateDescription('Они появятся автоматически, когда покупатель оформит заказ на сайте (корзина → оплата). Создать заказ из админки нельзя.');
    }

    public static function getRelations(): array
    {
        return [
            OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
