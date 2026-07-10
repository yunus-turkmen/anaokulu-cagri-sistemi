<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Kiosk extends Model
{
    protected $fillable = [
        'school_id',
        'device_code',
        'api_key',
        'name',
        'location',
        'device_name',
        'app_version',
        'ip_address',
        'last_seen_at',
        'status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Kiosk $kiosk): void {
            if (blank($kiosk->device_code)) {
                $kiosk->device_code = static::generateDeviceCode();
            }

            if (blank($kiosk->api_key)) {
                $kiosk->api_key = Str::random(64);
            }

            $kiosk->status ??= 'active';
            $kiosk->is_active ??= true;
        });
    }

    public static function generateDeviceCode(): string
    {
        do {
            $code = '';

            for ($i = 0; $i < 16; $i++) {
                $code .= random_int(0, 9);
            }
        } while (static::query()->where('device_code', $code)->exists());

        return $code;
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}