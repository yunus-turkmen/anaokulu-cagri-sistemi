<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Guardian extends Model
{
    protected $fillable = [
        'school_id',
        'first_name',
        'last_name',
        'name',
        'phone',
        'email',
        'relationship',
        'photo',
        'qr_code',
        'card_uid',
        'can_pickup',
        'emergency_contact',
        'status',
    ];

    protected $casts = [
        'can_pickup' => 'boolean',
        'emergency_contact' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Guardian $guardian): void {
            $guardian->name = trim(
                ($guardian->first_name ?? '') . ' ' .
                ($guardian->last_name ?? '')
            );

            if (blank($guardian->qr_code)) {
                do {
                    $qrCode = 'GRD-' . strtoupper(Str::random(12));
                } while (
                    Guardian::query()
                        ->where('qr_code', $qrCode)
                        ->exists()
                );

                $guardian->qr_code = $qrCode;
            }
        });

        static::updating(function (Guardian $guardian): void {
            $guardian->name = trim(
                ($guardian->first_name ?? '') . ' ' .
                ($guardian->last_name ?? '')
            );
        });
    }

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

    public function students()
    {
       

    return $this->belongsToMany(Student::class, 'guardian_student')

        ->withPivot('qr_code')

        ->withTimestamps();
    }
    public function ensureStudentQrCodes(): void
{
    foreach ($this->students()->get() as $student) {
        $pivot = $student->pivot;

        if (blank($pivot->qr_code)) {
            do {
                $qrCode = 'GS-' . strtoupper(\Illuminate\Support\Str::random(32));
            } while (
                \Illuminate\Support\Facades\DB::table('guardian_student')
                    ->where('qr_code', $qrCode)
                    ->exists()
            );

            \Illuminate\Support\Facades\DB::table('guardian_student')
                ->where('guardian_id', $this->id)
                ->where('student_id', $student->id)
                ->update([
                    'qr_code' => $qrCode,
                    'updated_at' => now(),
                ]);
        }
    }
}
}