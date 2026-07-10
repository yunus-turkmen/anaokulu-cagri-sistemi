<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }


    public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = auth()->user();

    if (! $user || $user->role === 'super_admin') {
        return $query;
    }

    return $query
        ->where('school_id', $user->school_id)
        ->whereIn('role', ['school_admin', 'teacher']);
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
        return $record->role !== 'super_admin';
    }

    return $user->role === 'school_admin'
        && (int) $record->school_id === (int) $user->school_id
        && $record->role === 'teacher';
}

public static function canDelete(Model $record): bool
{
    return static::canEdit($record);
}
}
