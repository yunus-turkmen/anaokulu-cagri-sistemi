<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupCall extends Model
{
    protected $fillable = [
        'school_id',
        'student_id',
        'guardian_id',
        'parent_id',
        'school_class_id',
        'kiosk_id',
        'status',
        'called_at',
        'completed_at',
    ];

    protected $casts = [
        'called_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

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
        return $this->belongsTo(Guardian::class, 'guardian_id');
    }

    /*
     * Eski çağrı kayıtlarında veli ID'si parent_id alanında tutulmuş olabilir.
     */
    public function parentGuardian()
    {
        return $this->belongsTo(Guardian::class, 'parent_id');
    }

    public function kiosk()
    {
        return $this->belongsTo(Kiosk::class);
    }
}