<?php

namespace App\Filament\Resources\Kiosks\Pages;

use App\Filament\Resources\Kiosks\KioskResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewKiosk extends ViewRecord
{
    protected static string $resource = KioskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Düzenle')
                ->visible(
                    fn (): bool =>
                        auth()->user()?->role === 'super_admin'
                ),

            Action::make('resetDevice')
                ->label('Cihaz Bağlantısını Sıfırla')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Cihaz bağlantısı sıfırlansın mı?')
                ->modalDescription(
                    'Mevcut cihaz bağlantısı kaldırılacak. Kiosk yeni bir cihazda tekrar etkinleştirilebilir.'
                )
                ->visible(
                    fn (): bool =>
                        auth()->user()?->role === 'super_admin'
                )
                ->action(function (): void {
                    $this->record->resetDeviceBinding();

                    Notification::make()
                        ->title('Cihaz bağlantısı sıfırlandı')
                        ->success()
                        ->send();

                    $this->redirect(
                        KioskResource::getUrl('view', [
                            'record' => $this->record,
                        ])
                    );
                }),
        ];
    }
}