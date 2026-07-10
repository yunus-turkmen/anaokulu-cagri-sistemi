<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\Pages\ViewStudent;
use App\Filament\Resources\Students\Schemas\StudentForm;
use App\Filament\Resources\Students\Tables\StudentsTable;
use App\Filament\Resources\Students\Schemas\StudentInfolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Öğrenciler';

    protected static ?string $modelLabel = 'Öğrenci';

    protected static ?string $pluralModelLabel = 'Öğrenciler';

    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }
    public static function infolist(Schema $schema): Schema
{
    return StudentInfolist::configure($schema);
}

    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'view' => ViewStudent::route('/{record}'),
            'edit' => EditStudent::route('/{record}/edit'),
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
        return $query
            ->where('school_id', $user->school_id)
            ->where('school_class_id', $user->school_class_id);
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