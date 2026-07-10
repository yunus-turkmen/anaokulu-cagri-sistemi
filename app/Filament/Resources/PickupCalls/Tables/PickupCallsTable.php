<?php

namespace App\Filament\Resources\PickupCalls\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PickupCallsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('school.name')
                    ->label('Okul')
                    ->searchable()
                    ->sortable()
                    ->visible(
                        fn (): bool =>
                            auth()->user()?->role === 'super_admin'
                    ),

                TextColumn::make('schoolClass.name')
                    ->label('Sınıf')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sınıf yok'),

                TextColumn::make('student.full_name')
                    ->label('Öğrenci')
                    ->searchable([
                        'first_name',
                        'last_name',
                        'name',
                    ])
                    ->sortable()
                    ->placeholder('Öğrenci bulunamadı'),

                TextColumn::make('guardian_name')
                    ->label('Veli')
                    ->state(function ($record): string {
                        $guardian =
                            $record->guardian ??
                            $record->parentGuardian;

                        return $guardian?->full_name
                            ?? $guardian?->name
                            ?? 'Belirtilmemiş';
                    })
                    ->searchable(query: function ($query, string $search) {
                        return $query->where(function ($query) use ($search) {
                            $query
                                ->whereHas(
                                    'guardian',
                                    fn ($guardianQuery) =>
                                        $guardianQuery
                                            ->where('first_name', 'like', "%{$search}%")
                                            ->orWhere('last_name', 'like', "%{$search}%")
                                            ->orWhere('name', 'like', "%{$search}%")
                                )
                                ->orWhereHas(
                                    'parentGuardian',
                                    fn ($guardianQuery) =>
                                        $guardianQuery
                                            ->where('first_name', 'like', "%{$search}%")
                                            ->orWhere('last_name', 'like', "%{$search}%")
                                            ->orWhere('name', 'like', "%{$search}%")
                                );
                        });
                    }),

                TextColumn::make('kiosk.name')
                    ->label('Kiosk')
                    ->placeholder('Belirtilmemiş')
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'waiting' => 'Bekliyor',
                            'completed' => 'Teslim Edildi',
                            'cancelled' => 'İptal Edildi',
                            default => $state ?: '-',
                        }
                    )
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'waiting' => 'warning',
                            'completed' => 'success',
                            'cancelled' => 'danger',
                            default => 'gray',
                        }
                    ),

                TextColumn::make('called_at')
                    ->label('Çağrı Zamanı')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),

                TextColumn::make('completed_at')
                    ->label('Teslim Zamanı')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('school_id')
                    ->label('Okul')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(
                        fn (): bool =>
                            auth()->user()?->role === 'super_admin'
                    ),
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
                        'waiting' => 'Bekliyor',
                        'completed' => 'Teslim Edildi',
                        'cancelled' => 'İptal Edildi',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Görüntüle'),
            ])
            ->defaultSort('called_at', 'desc')
            ->emptyStateHeading('Çağrı kaydı bulunamadı')
            ->emptyStateDescription('Kiosk üzerinden oluşturulan çağrılar burada görüntülenir.');
    }
}