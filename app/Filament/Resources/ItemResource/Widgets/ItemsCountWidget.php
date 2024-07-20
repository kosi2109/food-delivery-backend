<?php

namespace App\Filament\Resources\ItemResource\Widgets;

use App\Models\Item;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ItemsCountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Items', Item::count()),
        ];
    }

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];
}
