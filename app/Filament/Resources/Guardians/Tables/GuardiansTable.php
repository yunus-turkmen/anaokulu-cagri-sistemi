<?php

namespace App\Filament\Resources\Guardians\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GuardiansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('school.name')
                    ->label('Okul')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('first_name')
                    ->label('Ad')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('last_name')
                    ->label('Soyad')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('students')
                    ->label('Öğrenciler')
                    ->state(function ($record): array {
                        return $record->students
                            ->map(function ($student): string {
                                $fullName = trim(
                                    ($student->first_name ?? '') . ' ' .
                                    ($student->last_name ?? '')
                                );

                                $className = $student->schoolClass?->name ?? 'Sınıfsız';

                                return "{$fullName} — {$className}";
                            })
                            ->toArray();
                    })
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->limitList(3)
                    ->expandableLimitedList(),

                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('relationship')
                    ->label('Yakınlık')
                    ->badge()
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

                TextColumn::make('status')
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

                TextColumn::make('created_at')
                    ->label('Kayıt Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('school_id')
                    ->label('Okul')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('relationship')
                    ->label('Yakınlık')
                    ->options([
                        'anne' => 'Anne',
                        'baba' => 'Baba',
                        'dede' => 'Dede',
                        'babaanne' => 'Babaanne',
                        'anneanne' => 'Anneanne',
                        'servis' => 'Servis',
                        'diger' => 'Diğer',
                    ]),

                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'active' => 'Aktif',
                        'passive' => 'Pasif',
                    ]),
                    SelectFilter::make('school_id')
    ->label('Okul')
    ->relationship('school', 'name')
    ->searchable()
    ->preload()
    ->visible(fn (): bool => auth()->user()?->role === 'super_admin'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Görüntüle'),

                EditAction::make()
                    ->label('Düzenle'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Seçilenleri Sil'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Henüz veli kaydı yok')
            ->emptyStateDescription('Yeni veli ekleyerek başlayabilirsiniz.');
    }
}