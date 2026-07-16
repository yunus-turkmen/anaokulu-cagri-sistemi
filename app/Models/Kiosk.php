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
        'device_token_hash',
        'name',
        'location',
        'device_name',
        'app_version',
        'ip_address',
        'user_agent',
        'activated_at',
        'last_seen_at',
        'status',
        'is_active',
        'code',
    ];

    protected $hidden = [
        'device_token_hash',
    ];

    protected function casts(): array
    {
        return [
            'activated_at' => 'datetime',
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

        if (blank($kiosk->code)) {
            $kiosk->code = $kiosk->device_code;
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
                $code .= (string) random_int(0, 9);
            }
        } while (
            static::query()
                ->where('device_code', $code)
                ->exists()
        );

        return $code;
    }

    public function bindDevice(string $deviceToken): void
    {
        $this->forceFill([
            'device_token_hash' => hash('sha256', $deviceToken),
            'activated_at' => now(),
        ])->save();
    }

    public function deviceTokenMatches(string $deviceToken): bool
    {
        if (blank($this->device_token_hash)) {
            return false;
        }

        return hash_equals(
            (string) $this->device_token_hash,
            hash('sha256', $deviceToken)
        );
    }

    public function resetDeviceBinding(): void
    {
        $this->forceFill([
            'device_token_hash' => null,
            'activated_at' => null,
            'device_name' => null,
            'app_version' => null,
            'ip_address' => null,
            'user_agent' => null,
            'last_seen_at' => null,
        ])->save();
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}