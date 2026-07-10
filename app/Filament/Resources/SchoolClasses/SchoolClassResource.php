<?php

namespace App\Filament\Resources\SchoolClasses;

use App\Filament\Resources\SchoolClasses\Pages\CreateSchoolClass;
use App\Filament\Resources\SchoolClasses\Pages\EditSchoolClass;
use App\Filament\Resources\SchoolClasses\Pages\ListSchoolClasses;
use App\Filament\Resources\SchoolClasses\Pages\ViewSchoolClass;
use App\Filament\Resources\SchoolClasses\Schemas\SchoolClassForm;
use App\Filament\Resources\SchoolClasses\Schemas\SchoolClassInfolist;
use App\Filament\Resources\SchoolClasses\Tables\SchoolClassesTable;
use App\Models\SchoolClass;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SchoolClassResource extends Resource
{
    protected static ?string $model = SchoolClass::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Sınıflar';

    protected static ?string $modelLabel = 'Sınıf';

    protected static ?string $pluralModelLabel = 'Sınıflar';

    public static function form(Schema $schema): Schema
    {
        return SchoolClassForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SchoolClassInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchoolClassesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSchoolClasses::route('/'),
            'create' => CreateSchoolClass::route('/create'),
            'view' => ViewSchoolClass::route('/{record}'),
            'edit' => EditSchoolClass::route('/{record}/edit'),
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
            return $query->whereKey($user->school_class_id);
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

        if ($user->role === 'school_admin') {
            return (int) $record->school_id === (int) $user->school_id;
        }

        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return static::canEdit($record);
    }
}