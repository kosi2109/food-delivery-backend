<?php

namespace App\Filament\Resources\ItemResource\Pages;

use Filament\Actions;
use App\Filament\Resources\ItemResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

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

        if ($user && !$user->isSuperadmin()) {
            $query->where('created_by', $user->id);
        }

        return $query;
    }
}
