<?php

namespace App\Filament\Resources\Kiosks;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\Kiosks\Pages\CreateKiosk;
use App\Filament\Resources\Kiosks\Pages\EditKiosk;
use App\Filament\Resources\Kiosks\Pages\ListKiosks;
use App\Filament\Resources\Kiosks\Pages\ViewKiosk;
use App\Filament\Resources\Kiosks\Schemas\KioskForm;
use App\Filament\Resources\Kiosks\Schemas\KioskInfolist;
use App\Filament\Resources\Kiosks\Tables\KiosksTable;
use App\Models\Kiosk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KioskResource extends Resource
{
    protected static ?string $model = Kiosk::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return KioskForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KioskInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KiosksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();

    $user = auth()->user();

    if (! $user) {
        return $query->whereRaw('1 = 0');
    }

    if ($user->role === 'super_admin') {
        return $query;
    }

    if ($user->role === 'school_admin') {
        return $query->where('school_id', $user->school_id);
    }

    return $query->whereRaw('1 = 0');
}

public static function shouldRegisterNavigation(): bool
{
    return in_array(auth()->user()?->role, [
        'super_admin',
        'school_admin',
    ], true);
}

public static function canViewAny(): bool
{
    return in_array(auth()->user()?->role, [
        'super_admin',
        'school_admin',
    ], true);
}

public static function canView(Model $record): bool
{
    $user = auth()->user();

    if (! $user) {
        return false;
    }

    if ($user->role === 'super_admin') {
        return true;
    }

    return $user->role === 'school_admin'
        && $record->school_id == $user->school_id;
}

public static function canCreate(): bool
{
    return auth()->user()?->role === 'super_admin';
}

public static function canEdit(Model $record): bool
{
    return auth()->user()?->role === 'super_admin';
}

public static function canDelete(Model $record): bool
{
    return auth()->user()?->role === 'super_admin';
}

public static function canDeleteAny(): bool
{
    return auth()->user()?->role === 'super_admin';
}


    public static function getPages(): array
    {
        return [
            'index' => ListKiosks::route('/'),
            'create' => CreateKiosk::route('/create'),
            'view' => ViewKiosk::route('/{record}'),
            'edit' => EditKiosk::route('/{record}/edit'),
        ];
    }
}
