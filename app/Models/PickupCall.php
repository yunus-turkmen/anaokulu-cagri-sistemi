<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupCall extends Model
{
    protected $fillable = [
        'school_id',
        'student_id',
        'school_class_id',
        'guardian_id',
        'kiosk_id',
        'status',
        'called_at',
        'completed_at',
    ];

    protected $casts = [
        'called_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }
}
