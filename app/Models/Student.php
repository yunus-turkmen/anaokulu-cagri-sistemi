<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'school_id',
        'school_class_id',
        'name',
        'qr_code',
        'card_uid',
        'status',
        'photo',
        'birth_date',
        'tc_no',
    ];

    public function pickupCalls()
    {
        return $this->hasMany(PickupCall::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }
}
