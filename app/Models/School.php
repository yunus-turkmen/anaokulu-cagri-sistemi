<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'phone',
        'email',
        'address',
        'city',
        'district',
        'tax_number',
        'status',
        'package_id',
    ];

    public function guardians()
    {
        return $this->hasMany(Guardian::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function schoolClasses()
    {
        return $this->hasMany(SchoolClass::class);
    }
}
