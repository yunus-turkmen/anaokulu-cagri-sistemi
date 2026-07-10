<?php

namespace App\Filament\Resources\PickupCalls\Pages;

use App\Filament\Resources\PickupCalls\PickupCallResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPickupCall extends ViewRecord
{
    protected static string $resource = PickupCallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
