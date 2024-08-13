<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\OrderResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        $user = Auth::user();
        
        return $infolist
            ->schema([
                Section::make('Order Details')
                    ->schema([
                        TextEntry::make('id')->label('Order ID'),
                        TextEntry::make('customer.fullname')->label('Customer'),
                        TextEntry::make('delivery_note'),
                        TextEntry::make('delivery_cost'),
                        TextEntry::make('payment_type_text')->label('Payment Type'),
                        TextEntry::make('created_at')->label('Ordered At'),
                    ])->columns(4),
                Section::make('Order Items')
                    ->schema([
                        RepeatableEntry::make('orderItems')
                            ->schema([
                                TextEntry::make('item.name')
                                    ->label('Item Name'),
                                TextEntry::make('portion.name')->label('Portion'),
                                TextEntry::make('quantity'),
                                TextEntry::make('price'),
                                TextEntry::make('total'),
                                TextEntry::make('status'),
                                TextEntry::make('deliveryMan.name')->label('Delivery Man Name'),
                            ])
                            ->columns(6)
                            ->hidden(fn ($record) => !$user->isSuperadmin() && !$record->orderItems->contains(fn ($orderItem) => $orderItem->item && $orderItem->item->created_by === $user->id))
                            ->state(function ($record) use ($user) {
                                if ($user->isSuperadmin()) {
                                    return $record->orderItems;
                                }
                                return $record->orderItems->filter(fn ($orderItem) => $orderItem->item && $orderItem->item->created_by === $user->id);
                            }),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
}