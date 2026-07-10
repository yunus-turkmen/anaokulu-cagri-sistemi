<?php

namespace App\Filament\Resources\PickupCalls\Pages;

use App\Filament\Resources\PickupCalls\PickupCallResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPickupCalls extends ListRecords
{
    protected static string $resource = PickupCallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
