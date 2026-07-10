<?php

namespace App\Models;
use Filament\Models\Contracts\FilamentUser;

use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use Notifiable;

    protected $fillable = [
        'school_id',
        'school_class_id',
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

public function canAccessPanel(Panel $panel): bool
{
    return $this->status === 'active'
        && in_array($this->role, [
            'super_admin',
            'school_admin',
            'teacher',
        ], true);
}

}
