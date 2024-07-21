<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Order Details')
                    ->schema([
                        TextEntry::make('id')->label('Order ID'),
                        TextEntry::make('customer.fullname')->label('Customer'),
                        TextEntry::make('delivery_note'),
                        TextEntry::make('delivery_cost'),
                        TextEntry::make('sub_total'),
                        TextEntry::make('total_price'),
                        TextEntry::make('status')->label('Status'),
                        TextEntry::make('payment_type_text')->label('Payment Type'),
                        TextEntry::make('created_at')->label('Ordered At'),
                    ])->columns(4),
                Section::make('Order Items')
                    ->schema([
                        RepeatableEntry::make('orderItems')
                            ->schema([
                                TextEntry::make('item.name')
                                    ->label('Item Name'),
                                TextEntry::make('quantity'),
                                TextEntry::make('price'),
                                TextEntry::make('subtotal')
                                    ->state(fn ($record) => $record->quantity * $record->price),
                            ])
                            ->columns(4)
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
}