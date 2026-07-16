<?php

namespace App\Filament\Resources\Kiosks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KiosksTable
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

                TextColumn::make('name')
                    ->label('Kiosk Adı')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('location')
                    ->label('Konum')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('device_code')
                    ->label('Cihaz Kodu')
                    ->copyable()
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('device_name')
                    ->label('Bağlı Cihaz')
                    ->placeholder('Henüz etkinleştirilmedi')
                    ->toggleable(),

                TextColumn::make('last_seen_at')
                    ->label('Son Bağlantı')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'active' => 'Aktif',
                            'passive' => 'Pasif',
                            'maintenance' => 'Bakımda',
                            default => $state ?: '-',
                        }
                    )
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'active' => 'success',
                            'passive' => 'danger',
                            'maintenance' => 'warning',
                            default => 'gray',
                        }
                    ),

                IconColumn::make('is_active')
                    ->label('Kullanıma Açık')
                    ->boolean(),
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

                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'active' => 'Aktif',
                        'passive' => 'Pasif',
                        'maintenance' => 'Bakımda',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Görüntüle'),

                EditAction::make()
                    ->label('Düzenle')
                    ->visible(
                        fn (): bool =>
                            auth()->user()?->role === 'super_admin'
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Seçilenleri Sil')
                        ->visible(
                            fn (): bool =>
                                auth()->user()?->role === 'super_admin'
                        ),
                ]),
            ])
            ->defaultSort('name')
            ->emptyStateHeading('Henüz kiosk kaydı yok');
    }
}