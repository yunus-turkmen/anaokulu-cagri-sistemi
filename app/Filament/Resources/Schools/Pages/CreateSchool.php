<?php

namespace App\Filament\Resources\Schools\Pages;

use App\Filament\Resources\Schools\SchoolResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateSchool extends CreateRecord
{
    protected static string $resource = SchoolResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Model {
            $adminName = $data['admin_name'];
            $adminEmail = $data['admin_email'];
            $adminPassword = $data['admin_password'];

            unset(
                $data['admin_name'],
                $data['admin_email'],
                $data['admin_password']
            );

            $school = static::getModel()::create($data);

            User::create([
                'school_id' => $school->id,
                'school_class_id' => null,
                'name' => $adminName,
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'role' => 'school_admin',
                'status' => 'active',
            ]);

            return $school;
        });
    }
}