<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kiosk extends Model
{
    protected $fillable = [
        'school_id',
        'name',
        'location',
        'device_uuid',
        'api_key',
        'status',
        'last_seen_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function pickupCalls()
    {
        return $this->hasMany(PickupCall::class);
    }
}