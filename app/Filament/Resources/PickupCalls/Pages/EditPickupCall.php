<?php

namespace App\Filament\Resources\PickupCalls\Pages;

use App\Filament\Resources\PickupCalls\PickupCallResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPickupCall extends EditRecord
{
    protected static string $resource = PickupCallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
