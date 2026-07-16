<?php

namespace App\Filament\Resources\Kiosks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class KioskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_id')
                    ->label('Okul')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('name')
                    ->label('Kiosk Adı')
                    ->placeholder('Örneğin: Ana Giriş Kiosku')
                    ->required()
                    ->maxLength(191),

                TextInput::make('location')
                    ->label('Konum')
                    ->placeholder('Örneğin: Ana giriş kapısı')
                    ->maxLength(191),

                Select::make('status')
                    ->label('Durum')
                    ->options([
                        'active' => 'Aktif',
                        'passive' => 'Pasif',
                        'maintenance' => 'Bakımda',
                    ])
                    ->default('active')
                    ->required(),

                Toggle::make('is_active')
                    ->label('Kullanıma Açık')
                    ->default(true),
            ])
            ->columns(2);
    }
}