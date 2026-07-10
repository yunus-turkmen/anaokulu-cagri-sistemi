<?php

namespace App\Filament\Resources\Students\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('school.name')
                    ->label('Okul')
                    ->searchable()
                    ->sortable()
                    ->visible(fn (): bool => auth()->user()?->role === 'super_admin'),

                TextColumn::make('schoolClass.name')
                    ->label('Sınıf')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('full_name')
                    ->label('Öğrenci')
                    ->searchable([
                        'first_name',
                        'last_name',
                        'name',
                    ])
                    ->sortable(),

                TextColumn::make('qr_code')
                    ->label('QR Kodu')
                    ->copyable()
                    ->placeholder('-'),

                TextColumn::make('card_uid')
                    ->label('Kart UID')
                    ->copyable()
                    ->placeholder('-'),

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

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('school_id')
                    ->label('Okul')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn (): bool => auth()->user()?->role === 'super_admin'),

                SelectFilter::make('school_class_id')
                    ->label('Sınıf')
                    ->relationship(
                        name: 'schoolClass',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query): Builder {
                            $user = auth()->user();

                            if (! $user || $user->role === 'super_admin') {
                                return $query;
                            }

                            if ($user->role === 'teacher') {
                                return $query->whereKey($user->school_class_id);
                            }

                            return $query->where('school_id', $user->school_id);
                        }
                    )
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'active' => 'Aktif',
                        'passive' => 'Pasif',
                    ]),
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
                        ->label('Sil'),
                ]),
            ])
            ->defaultSort('first_name');
    }
}