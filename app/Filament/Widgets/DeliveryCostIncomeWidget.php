<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class DeliveryCostIncomeWidget extends BaseWidget
{
    // No need to specify a view here

    protected function getCards(): array
    {
        // Calculate the total delivery cost
        $totalDeliveryCost = Order::sum('delivery_cost');

        // Admin gets 80% of the total delivery cost
        $adminDeliveryIncome = $totalDeliveryCost * 0.2;

        return [
            Card::make('Total Delivery Income', number_format($adminDeliveryIncome, 2) . ' MMK')
                ->description('20% of total delivery cost')
                // ->descriptionIcon('heroicon-s-user')
                ->color('success')
        ];
    }

    // Restrict widget view to only superadmins
    public static function canView(): bool
    {
        return Auth::user()->isSuperadmin();
    }
}
