<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Öğrenci Bilgileri')
                    ->schema([
                        TextEntry::make('school.name')
                            ->label('Okul'),

                        TextEntry::make('full_name')
                            ->label('Ad Soyad'),

                        TextEntry::make('schoolClass.name')
                            ->label('Sınıf')
                            ->placeholder('Sınıfsız'),

                        TextEntry::make('student_no')
                            ->label('Öğrenci No')
                            ->placeholder('-'),

                        TextEntry::make('qr_code')
                            ->label('QR Kod')
                            ->placeholder('-'),

                        TextEntry::make('card_uid')
                            ->label('Kart UID')
                            ->placeholder('-'),

                        TextEntry::make('status')
                            ->label('Durum')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'active' => 'Aktif',
                                'passive' => 'Pasif',
                                default => $state ?: '-',
                            })
                            ->color(fn (?string $state): string => match ($state) {
                                'active' => 'success',
                                'passive' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2),

                Section::make('Veliler')
                    ->schema([
                        RepeatableEntry::make('guardians')
                            ->label('')
                            ->schema([
                                TextEntry::make('full_name')
                                    ->label('Veli'),

                                TextEntry::make('relationship')
                                    ->label('Yakınlık')
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'anne' => 'Anne',
                                        'baba' => 'Baba',
                                        'dede' => 'Dede',
                                        'babaanne' => 'Babaanne',
                                        'anneanne' => 'Anneanne',
                                        'servis' => 'Servis',
                                        'diger' => 'Diğer',
                                        default => $state ?: '-',
                                    }),

                                TextEntry::make('phone')
                                    ->label('Telefon'),
                            ])
                            ->columns(3),
                    ]),

                Section::make('Son Çağrı ve Teslim Kayıtları')
                    ->schema([
                        RepeatableEntry::make('pickupCalls')
                            ->label('')
                            ->schema([
                                TextEntry::make('guardian.full_name')
                                    ->label('Veli')
                                    ->placeholder('Belirtilmemiş'),

                                TextEntry::make('status')
                                    ->label('Durum')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'waiting' => 'Bekliyor',
                                        'completed' => 'Teslim Edildi',
                                        'cancelled' => 'İptal Edildi',
                                        default => $state ?: '-',
                                    })
                                    ->color(fn (?string $state): string => match ($state) {
                                        'waiting' => 'warning',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    }),

                                TextEntry::make('called_at')
                                    ->label('Çağrı Zamanı')
                                    ->dateTime('d.m.Y H:i'),

                                TextEntry::make('completed_at')
                                    ->label('Teslim Zamanı')
                                    ->dateTime('d.m.Y H:i')
                                    ->placeholder('-'),
                            ])
                            ->columns(4),
                    ]),
            ]);
    }
}