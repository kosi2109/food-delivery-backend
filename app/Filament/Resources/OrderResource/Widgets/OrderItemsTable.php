<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\OrderItem;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class OrderItemsTable extends BaseWidget
{
    public $record;

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return OrderItem::query()->where('order_id', $this->record->id);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('item.name')->label('Item Name'),
            TextColumn::make('quantity')->label('Quantity'),
            TextColumn::make('price')->label('Price'),
        ];
    }
}
