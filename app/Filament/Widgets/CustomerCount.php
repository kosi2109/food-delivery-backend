<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class CustomerCount extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total Customers', Customer::count())
                ->description('Total registered customers')
                // ->descriptionIcon('heroicon-s-user')
                ->color('success')
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()->isSuperadmin();
    }
}
