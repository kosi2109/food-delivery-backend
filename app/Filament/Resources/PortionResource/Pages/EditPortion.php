<?php

namespace App\Filament\Resources\PortionResource\Pages;

use App\Filament\Resources\PortionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPortion extends EditRecord
{
    protected static string $resource = PortionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
