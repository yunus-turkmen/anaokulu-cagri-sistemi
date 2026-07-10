<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserForm
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
                    ->live()
                    ->default(fn () => auth()->user()?->school_id)
                    ->disabled(fn () => auth()->user()?->role !== 'super_admin')
                    ->dehydrated()
                    ->afterStateUpdated(fn (callable $set) => $set('school_class_id', null))
                    ->required(),

                TextInput::make('name')
                    ->label('Ad Soyad')
                    ->required()
                    ->maxLength(191),

                TextInput::make('email')
                    ->label('E-posta')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(191),

                TextInput::make('password')
                    ->label('Şifre')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->minLength(8),

                Select::make('role')
                    ->label('Rol')
                    ->options(function (): array {
                        if (auth()->user()?->role === 'super_admin') {
                            return [
                                'school_admin' => 'Okul Yöneticisi',
                                'teacher' => 'Öğretmen',
                            ];
                        }

                        return [
                            'teacher' => 'Öğretmen',
                        ];
                    })
                    ->default('teacher')
                    ->live()
                    ->required(),

                Select::make('school_class_id')
                    ->label('Sınıf')
                    ->relationship(
                        name: 'schoolClass',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query, callable $get): Builder {
                            $schoolId = auth()->user()?->role === 'super_admin'
                                ? $get('school_id')
                                : auth()->user()?->school_id;

                            return $query->when(
                                $schoolId,
                                fn (Builder $query): Builder =>
                                    $query->where('school_id', $schoolId)
                            );
                        }
                    )
                    ->searchable()
                    ->preload()
                    ->visible(fn (callable $get): bool => $get('role') === 'teacher')
                    ->required(fn (callable $get): bool => $get('role') === 'teacher'),

                Select::make('status')
                    ->label('Durum')
                    ->options([
                        'active' => 'Aktif',
                        'passive' => 'Pasif',
                    ])
                    ->default('active')
                    ->required(),
            ])
            ->columns(2);
    }
}