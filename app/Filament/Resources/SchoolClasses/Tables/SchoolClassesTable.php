<?php

namespace App\Filament\Resources\SchoolClasses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SchoolClassesTable
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

                TextColumn::make('name')
                    ->label('Sınıf Adı')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('screen_code')
                    ->label('Ekran Kodu')
                    ->searchable()
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
                SelectFilter::make('school_id')
                    ->label('Okul')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn (): bool => auth()->user()?->role === 'super_admin'),

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
                        ->label('Seçilenleri Sil'),
                ]),
            ])
            ->defaultSort('name')
            ->emptyStateHeading('Henüz sınıf kaydı yok')
            ->emptyStateDescription('Yeni sınıf ekleyerek başlayabilirsiniz.');
    }
}