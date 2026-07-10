<?php

namespace App\Filament\Resources\Schools\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SchoolsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Okul Adı')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('authorized_name')
                    ->label('Yetkili')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->copyable()
                    ->placeholder('-'),

                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->copyable()
                    ->placeholder('-'),

                TextColumn::make('city')
                    ->label('İl')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('district')
                    ->label('İlçe')
                    ->searchable()
                    ->sortable()
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
                    ->label('Kullanıma Açık')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Güncellenme Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'active' => 'Aktif',
                        'passive' => 'Pasif',
                    ]),

                SelectFilter::make('is_active')
                    ->label('Kullanıma Açık')
                    ->options([
                        1 => 'Açık',
                        0 => 'Kapalı',
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
                        ->label('Seçilenleri Sil'),
                ]),
            ])
            ->defaultSort('name')
            ->emptyStateHeading('Henüz okul kaydı yok')
            ->emptyStateDescription('Yeni okul ekleyerek başlayabilirsiniz.');
    }
}