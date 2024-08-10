<?php

namespace App\Filament\Resources\RestaurantResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RestaurantResource;

class ListRestaurants extends ListRecords
{
    protected static string $resource = RestaurantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): ?Builder
    {
        $query = parent::getTableQuery();

        $user = auth()->user();

        // Only show records created by the user if they are not a superadmin
        if ($user && !$user->isSuperadmin()) {
            $query->where('created_by', $user->id);
        }

        return $query;
    }
}
