<?php

namespace App\Filament\Resources\PortionResource\Pages;

use App\Filament\Resources\PortionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPortions extends ListRecords
{
    protected static string $resource = PortionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
