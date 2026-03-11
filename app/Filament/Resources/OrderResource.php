<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Заказы';

    protected static ?string $modelLabel = 'заказ';

    protected static ?string $pluralModelLabel = 'Заказы';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Информация о заказе')
                    ->schema([
                        Infolists\Components\TextEntry::make('order_number')
                            ->label('Номер заказа'),

                        Infolists\Components\TextEntry::make('order_type')
                            ->label('Тип заказа')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pickup' => 'success',
                                'delivery' => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pickup' => 'На месте',
                                'delivery' => 'Доставка',
                                default => $state,
                            }),

                        Infolists\Components\TextEntry::make('driver.name')
                            ->label('Водитель'),

                        Infolists\Components\TextEntry::make('customer_name')
                            ->label('Имя клиента')
                            ->placeholder('—'),

                        Infolists\Components\TextEntry::make('customer_phone')
                            ->label('Телефон клиента')
                            ->placeholder('—'),

                        Infolists\Components\TextEntry::make('delivery_address')
                            ->label('Адрес доставки')
                            ->placeholder('—')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('payment_method')
                            ->label('Способ оплаты')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'cash' => 'success',
                                'qr' => 'info',
                                'delivery' => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'cash' => 'Наличные',
                                'qr' => 'QR-код',
                                'delivery' => 'При получении',
                                default => $state,
                            }),

                        Infolists\Components\TextEntry::make('status')
                            ->label('Статус')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'confirmed' => 'success',
                                'delivered' => 'primary',
                                'cancelled' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'Ожидает подтверждения',
                                'confirmed' => 'Подтверждён',
                                'delivered' => 'Доставлен',
                                'cancelled' => 'Отменён',
                                default => $state,
                            }),

                        Infolists\Components\TextEntry::make('total')
                            ->label('Сумма')
                            ->money('UZS'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Дата создания')
                            ->dateTime('d.m.Y H:i'),
                    ])
                    ->columns(2),

                Section::make('Состав заказа')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('product.name')
                                    ->label('Товар'),

                                Infolists\Components\TextEntry::make('quantity')
                                    ->label('Кол-во'),

                                Infolists\Components\TextEntry::make('price')
                                    ->label('Цена')
                                    ->money('UZS'),

                                Infolists\Components\TextEntry::make('subtotal')
                                    ->label('Сумма')
                                    ->money('UZS'),
                            ])
                            ->columns(4),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Номер')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order_type')
                    ->label('Тип')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pickup' => 'success',
                        'delivery' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pickup' => 'На месте',
                        'delivery' => 'Доставка',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Клиент')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('customer_phone')
                    ->label('Телефон')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('driver.name')
                    ->label('Водитель')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Оплата')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'qr' => 'info',
                        'delivery' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Наличные',
                        'qr' => 'QR-код',
                        'delivery' => 'При получении',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'delivered' => 'primary',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Ожидает',
                        'confirmed' => 'Подтверждён',
                        'delivered' => 'Доставлен',
                        'cancelled' => 'Отменён',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('total')
                    ->label('Сумма')
                    ->money('UZS')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('order_type')
                    ->label('Тип заказа')
                    ->options([
                        'pickup' => 'На месте',
                        'delivery' => 'Доставка',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Ожидает подтверждения',
                        'confirmed' => 'Подтверждён',
                        'delivered' => 'Доставлен',
                        'cancelled' => 'Отменён',
                    ]),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Способ оплаты')
                    ->options([
                        'cash' => 'Наличные',
                        'qr' => 'QR-код',
                        'delivery' => 'При получении',
                    ]),

                Tables\Filters\SelectFilter::make('driver_id')
                    ->label('Водитель')
                    ->relationship('driver', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('С даты'),
                        Forms\Components\DatePicker::make('until')
                            ->label('По дату'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('changeStatus')
                    ->label('Сменить статус')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Новый статус')
                            ->options([
                                'pending' => 'Ожидает подтверждения',
                                'confirmed' => 'Подтверждён',
                                'delivered' => 'Доставлен',
                                'cancelled' => 'Отменён',
                            ])
                            ->required(),
                    ])
                    ->action(fn (Order $record, array $data) => $record->update(['status' => $data['status']])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulkChangeStatus')
                        ->label('Сменить статус')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Новый статус')
                                ->options([
                                    'pending' => 'Ожидает подтверждения',
                                    'confirmed' => 'Подтверждён',
                                    'delivered' => 'Доставлен',
                                    'cancelled' => 'Отменён',
                                ])
                                ->required(),
                        ])
                        ->action(fn ($records, array $data) => $records->each->update(['status' => $data['status']]))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
