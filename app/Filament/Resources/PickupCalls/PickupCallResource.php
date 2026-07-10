<?php

namespace App\Filament\Resources\PickupCalls;

use App\Filament\Resources\PickupCalls\Pages\ListPickupCalls;
use App\Filament\Resources\PickupCalls\Pages\ViewPickupCall;
use App\Filament\Resources\PickupCalls\Schemas\PickupCallInfolist;
use App\Filament\Resources\PickupCalls\Tables\PickupCallsTable;
use App\Models\PickupCall;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PickupCallResource extends Resource
{
    protected static ?string $model = PickupCall::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedBellAlert;

    protected static ?string $navigationLabel = 'Çağrı Kayıtları';

    protected static ?string $modelLabel = 'Çağrı Kaydı';

    protected static ?string $pluralModelLabel = 'Çağrı Kayıtları';

    public static function infolist(Schema $schema): Schema
    {
        return PickupCallInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PickupCallsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPickupCalls::route('/'),
            'view' => ViewPickupCall::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with([
                'school',
                'schoolClass',
                'student',
                'guardian',
                'parentGuardian',
                'kiosk',
            ]);

        $user = auth()->user();

        if (! $user || $user->role === 'super_admin') {
            return $query;
        }

        if ($user->role === 'teacher') {
            return $query
                ->where('school_id', $user->school_id)
                ->where('school_class_id', $user->school_class_id);
        }

        return $query->where('school_id', $user->school_id);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->role === 'super_admin';
    }
    
}