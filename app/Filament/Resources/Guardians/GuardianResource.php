<?php

namespace App\Filament\Resources\Guardians;

use App\Filament\Resources\Guardians\Pages\CreateGuardian;
use App\Filament\Resources\Guardians\Pages\EditGuardian;
use App\Filament\Resources\Guardians\Pages\ListGuardians;
use App\Filament\Resources\Guardians\Pages\ViewGuardian;
use App\Filament\Resources\Guardians\Schemas\GuardianForm;
use App\Filament\Resources\Guardians\Schemas\GuardianInfolist;
use App\Filament\Resources\Guardians\Tables\GuardiansTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Guardian;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GuardianResource extends Resource
{
    protected static ?string $model = Guardian::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Veliler';

    protected static ?string $modelLabel = 'Veli';

    protected static ?string $pluralModelLabel = 'Veliler';

    public static function form(Schema $schema): Schema
    {
        return GuardianForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GuardianInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GuardiansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGuardians::route('/'),
            'create' => CreateGuardian::route('/create'),
            'view' => ViewGuardian::route('/{record}'),
            'edit' => EditGuardian::route('/{record}/edit'),
        ];
    }

 public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = auth()->user();

    if (! $user || $user->role === 'super_admin') {
        return $query;
    }

    if ($user->role === 'teacher') {
        return $query->whereHas(
            'students',
            fn (Builder $studentQuery): Builder =>
                $studentQuery
                    ->where('school_id', $user->school_id)
                    ->where('school_class_id', $user->school_class_id)
        );
    }

    return $query->where('school_id', $user->school_id);
}

public static function canCreate(): bool
{
    return in_array(auth()->user()?->role, [
        'super_admin',
        'school_admin',
    ], true);
}

public static function canEdit(Model $record): bool
{
    $user = auth()->user();

    if (! $user) {
        return false;
    }

    if ($user->role === 'super_admin') {
        return true;
    }

    return $user->role === 'school_admin'
        && (int) $record->school_id === (int) $user->school_id;
}

public static function canDelete(Model $record): bool
{
    return static::canEdit($record);
}
}