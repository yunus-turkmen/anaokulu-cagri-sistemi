<?php

namespace App\Filament\Resources\Schools\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SchoolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Okul Adı')
                    ->required()
                    ->maxLength(191),

                TextInput::make('authorized_name')
                    ->label('Yetkili Adı Soyadı')
                    ->maxLength(191),
                    TextInput::make('admin_name')
                        ->label('Okul Yöneticisi Ad Soyad')
                        ->required()
                        ->maxLength(191)
                        ->dehydrated(),

                    TextInput::make('admin_email')
                        ->label('Yönetici Giriş E-postası')
                        ->email()
                        ->required()
                        ->maxLength(191)
                        ->unique('users', 'email')
                        ->dehydrated(),

                    TextInput::make('admin_password')
                        ->label('Yönetici Giriş Şifresi')
                        ->password()
                        ->revealable()
                        ->required()
                        ->minLength(8)
                        ->dehydrated(),

                TextInput::make('phone')
                    ->label('Telefon')
                    ->tel()
                    ->maxLength(30),

                TextInput::make('email')
                    ->label('E-posta')
                    ->email()
                    ->maxLength(191),

                TextInput::make('city')
                    ->label('İl')
                    ->maxLength(100),

                TextInput::make('district')
                    ->label('İlçe')
                    ->maxLength(100),

                Textarea::make('address')
                    ->label('Adres')
                    ->rows(4)
                    ->columnSpanFull(),

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
