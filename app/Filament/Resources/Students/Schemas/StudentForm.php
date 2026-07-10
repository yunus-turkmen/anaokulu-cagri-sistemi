<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class StudentForm
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

                Select::make('school_class_id')
                    ->label('Sınıf')
                    ->relationship(
                        name: 'schoolClass',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query, callable $get): Builder {
                            $user = auth()->user();

                            if ($user?->role === 'teacher') {
                                return $query->whereKey($user->school_class_id);
                            }

                            $schoolId = $user?->role === 'super_admin'
                                ? $get('school_id')
                                : $user?->school_id;

                            return $query->when(
                                $schoolId,
                                fn (Builder $query): Builder =>
                                    $query->where('school_id', $schoolId)
                            );
                        }
                    )
                    ->searchable()
                    ->preload()
                    ->default(
                        fn () => auth()->user()?->role === 'teacher'
                            ? auth()->user()?->school_class_id
                            : null
                    )
                    ->disabled(fn () => auth()->user()?->role === 'teacher')
                    ->dehydrated()
                    ->required(),

                TextInput::make('student_no')
                    ->label('Öğrenci No')
                    ->maxLength(191),

                TextInput::make('first_name')
                    ->label('Ad')
                    ->required()
                    ->maxLength(191),

                TextInput::make('last_name')
                    ->label('Soyad')
                    ->required()
                    ->maxLength(191),

                TextInput::make('qr_code')
                    ->label('QR Kodu')
                    ->maxLength(191),

                TextInput::make('card_uid')
                    ->label('Kart UID')
                    ->maxLength(191),

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