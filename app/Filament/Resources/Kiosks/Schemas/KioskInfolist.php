<?php

namespace App\Filament\Resources\Kiosks\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class KioskInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('school.name')
                    ->label('Okul'),

                TextEntry::make('name')
                    ->label('Kiosk Adı'),

                TextEntry::make('location')
                    ->label('Konum')
                    ->placeholder('-'),

                TextEntry::make('device_code')
                    ->label('16 Haneli Cihaz Kodu')
                    ->copyable()
                    ->badge()
                    ->color('primary'),

                TextEntry::make('api_key')
                    ->label('API Anahtarı')
                    ->copyable()
                    ->visible(
                        fn (): bool =>
                            auth()->user()?->role === 'super_admin'
                    ),

                TextEntry::make('device_name')
                    ->label('Bağlı Cihaz')
                    ->placeholder('Henüz etkinleştirilmedi'),

                TextEntry::make('app_version')
                    ->label('Uygulama Sürümü')
                    ->placeholder('-'),

                TextEntry::make('ip_address')
                    ->label('Son IP Adresi')
                    ->placeholder('-'),

                TextEntry::make('activated_at')
                    ->label('Etkinleştirilme Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('-'),

                TextEntry::make('last_seen_at')
                    ->label('Son Bağlantı')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('-'),

                TextEntry::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'active' => 'Aktif',
                            'passive' => 'Pasif',
                            'maintenance' => 'Bakımda',
                            default => $state ?: '-',
                        }
                    )
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'active' => 'success',
                            'passive' => 'danger',
                            'maintenance' => 'warning',
                            default => 'gray',
                        }
                    ),

                IconEntry::make('is_active')
                    ->label('Kullanıma Açık')
                    ->boolean(),
            ])
            ->columns(2);
    }
}