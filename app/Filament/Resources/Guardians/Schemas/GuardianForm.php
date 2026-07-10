<?php

namespace App\Filament\Resources\Guardians\Schemas;

use App\Models\Student;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class GuardianForm
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

                ->afterStateUpdated(fn (callable $set) => $set('students', []))

                ->required(),
                Select::make('students')
                    ->label('Öğrenciler')
                    ->relationship(
                        name: 'students',
                        titleAttribute: 'first_name',
                        modifyQueryUsing: function (Builder $query, callable $get): Builder {
                            $schoolId = $get('school_id');

                            return $query
                                ->with('schoolClass')
                                ->when(
                                    $schoolId,
                                    fn (Builder $query): Builder => $query->where('school_id', $schoolId)
                                );
                        }
                    )
                    ->getOptionLabelFromRecordUsing(function (Student $record): string {
                        $fullName = trim(
                            ($record->first_name ?? '') . ' ' .
                            ($record->last_name ?? '')
                        );

                        $className = $record->schoolClass?->name ?? 'Sınıfsız';

                        return "{$fullName} — {$className}";
                    })
                    ->multiple()
                    ->searchable(['first_name', 'last_name'])
                    ->preload()
                    ->disabled(fn (callable $get): bool => blank($get('school_id')))
                    ->helperText('Önce okul seçiniz.')
                    ->required(),

                TextInput::make('first_name')
                    ->label('Ad')
                    ->required(),

                TextInput::make('last_name')
                    ->label('Soyad')
                    ->required(),

                TextInput::make('phone')
                    ->label('Telefon')
                    ->tel()
                    ->required(),

                TextInput::make('email')
                    ->label('E-posta')
                    ->email(),

                Select::make('relationship')
                    ->label('Yakınlık')
                    ->options([
                        'anne' => 'Anne',
                        'baba' => 'Baba',
                        'dede' => 'Dede',
                        'babaanne' => 'Babaanne',
                        'anneanne' => 'Anneanne',
                        'servis' => 'Servis',
                        'diger' => 'Diğer',
                    ])
                    ->required(),

                Select::make('status')
                    ->label('Durum')
                    ->options([
                        'active' => 'Aktif',
                        'passive' => 'Pasif',
                    ])
                    ->default('active')
                    ->required(),
                    TextInput::make('qr_code')
    ->label('QR Kodu')
    ->disabled()
    ->dehydrated(false)
    ->helperText('Yeni veli kaydedildiğinde otomatik oluşturulur.'),
            ]);
    }
}