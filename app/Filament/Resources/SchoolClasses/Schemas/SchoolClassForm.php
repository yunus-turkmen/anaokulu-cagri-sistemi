<?php

namespace App\Filament\Resources\SchoolClasses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SchoolClassForm
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
                    ->default(fn () => auth()->user()?->school_id)
                    ->disabled(fn () => auth()->user()?->role !== 'super_admin')
                    ->dehydrated()
                    ->required(),

                TextInput::make('name')
                    ->label('Sınıf Adı')
                    ->required()
                    ->maxLength(191),

                TextInput::make('screen_code')
                    ->label('Ekran Kodu')
                    ->maxLength(191),

                Select::make('status')
                    ->label('Durum')
                    ->options([
                        'active' => 'Aktif',
                        'passive' => 'Pasif',
                    ])
                    ->default('active')
                    ->required(),

                Toggle::make('is_active')
                    ->label('Kullanıma Açık')
                    ->default(true)
                    ->required(),
            ])
            ->columns(2);
    }
}