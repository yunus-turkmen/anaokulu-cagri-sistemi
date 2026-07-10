<?php

namespace App\Filament\Resources\Guardians\Schemas;

use App\Filament\Resources\Students\StudentResource;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GuardianInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Veli Bilgileri')
                    ->schema([
                        TextEntry::make('school.name')
                            ->label('Okul'),

                        TextEntry::make('full_name')
                            ->label('Ad Soyad'),

                        TextEntry::make('phone')
                            ->label('Telefon'),

                        TextEntry::make('email')
                            ->label('E-posta')
                            ->placeholder('-'),

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

                Section::make('Öğrenciler')
                    ->schema([
                        RepeatableEntry::make('students')
                            ->label('')
                            ->schema([
                                TextEntry::make('full_name')
                                    ->label('Öğrenci')
                                    ->icon('heroicon-o-user')
                                    ->url(
                                        fn ($record): string => StudentResource::getUrl('view', [
                                            'record' => $record,
                                        ])
                                    )
                                    ->openUrlInNewTab(),

                                TextEntry::make('schoolClass.name')
                                    ->label('Sınıf')
                                    ->placeholder('Sınıfsız'),
                                    TextEntry::make('pivot.qr_code')
                            ->label('QR Kodu')
                            ->copyable()
                            ->placeholder('QR kodu yok'),
                            ])
                            ->columns(2),

                            TextEntry::make('qr_code')
                            ->label('QR Kodu')
                            ->copyable()
                            ->placeholder('-'),
                            
                            
                    ]),
            ]);
    }
}