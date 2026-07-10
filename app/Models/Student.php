<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'school_id',
        'school_class_id',
        'student_no',
        'first_name',
        'last_name',
        'name',
        'photo',
        'birth_date',
        'gender',
        'qr_code',
        'card_uid',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function getFullNameAttribute(): string
    {
        return trim(
            ($this->first_name ?? '') . ' ' .
            ($this->last_name ?? '')
        );
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function guardians()
    {
        return $this->belongsToMany(Guardian::class, 'guardian_student')
            ->withPivot('qr_code')
            ->withTimestamps();
    }

    public function pickupCalls()
    {
        return $this->hasMany(PickupCall::class)
            ->orderByDesc('called_at')
            ->orderByDesc('id');
    }
}