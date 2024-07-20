<?php

namespace App\Filament\Resources\RestaurantResource\Widgets;

use App\Models\Restaurant;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class RestaurantCountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Restaurants', Restaurant::count()),
        ];
    }

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];
}
