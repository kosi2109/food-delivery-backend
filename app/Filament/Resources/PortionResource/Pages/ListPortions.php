<?php

namespace App\Filament\Resources\PortionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PortionResource;
use Illuminate\Database\Eloquent\Builder;

class ListPortions extends ListRecords
{
    protected static string $resource = PortionResource::class;

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
