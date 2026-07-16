<?php

namespace App\Http\Controllers\Api;

use App\Events\PickupCallCreated;
use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\Kiosk;
use App\Models\PickupCall;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KioskCallController extends Controller
{
    public function activate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'device_code' => ['required', 'digits:16'],
            'api_key' => ['required', 'string', 'max:191'],
            'device_token' => ['required', 'string', 'min:32', 'max:500'],
            'device_name' => ['nullable', 'string', 'max:191'],
            'app_version' => ['nullable', 'string', 'max:50'],
        ]);

        $kiosk = Kiosk::query()
            ->where('device_code', $data['device_code'])
            ->first();

        if (
            ! $kiosk ||
            blank($kiosk->api_key) ||
            ! hash_equals((string) $kiosk->api_key, $data['api_key'])
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Kiosk kodu veya API anahtarı hatalı.',
            ], 401);
        }

        if (! $kiosk->is_active || $kiosk->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Bu kiosk kullanıma kapalıdır.',
            ], 403);
        }

        if (blank($kiosk->device_token_hash)) {
            $kiosk->bindDevice($data['device_token']);
        } elseif (! $kiosk->deviceTokenMatches($data['device_token'])) {
            return response()->json([
                'success' => false,
                'message' => 'Bu kiosk lisansı başka bir cihaza tanımlıdır.',
            ], 403);
        }

        $kiosk->forceFill([
            'device_name' => $data['device_name'] ?? $kiosk->device_name,
            'app_version' => $data['app_version'] ?? $kiosk->app_version,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'last_seen_at' => now(),
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'Kiosk başarıyla etkinleştirildi.',
            'kiosk' => [
                'name' => $kiosk->name,
                'location' => $kiosk->location,
                'school_id' => $kiosk->school_id,
                'device_code' => $kiosk->device_code,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'qr_code' => ['required', 'string', 'max:500'],
            'device_code' => ['required', 'digits:16'],
            'api_key' => ['required', 'string', 'max:191'],
            'device_token' => ['required', 'string', 'min:32', 'max:500'],
        ]);

        $kiosk = Kiosk::query()
            ->where('device_code', $data['device_code'])
            ->first();

        if (
            ! $kiosk ||
            blank($kiosk->api_key) ||
            ! hash_equals((string) $kiosk->api_key, $data['api_key']) ||
            ! $kiosk->deviceTokenMatches($data['device_token'])
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Kiosk cihaz doğrulaması başarısız.',
            ], 403);
        }

        if (! $kiosk->is_active || $kiosk->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Bu kiosk kullanıma kapalıdır.',
            ], 403);
        }

        $kiosk->forceFill([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'last_seen_at' => now(),
        ])->save();

        $code = trim($data['qr_code']);

        $guardianStudent = DB::table('guardian_student')
            ->where('qr_code', $code)
            ->first();

        if ($guardianStudent) {
            $guardian = Guardian::find($guardianStudent->guardian_id);
            $student = Student::find($guardianStudent->student_id);

            if (! $guardian || ! $student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Öğrenci veya veli kaydı bulunamadı.',
                ], 404);
            }

            if ((int) $student->school_id !== (int) $kiosk->school_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu öğrenci kioskun bağlı olduğu okulda kayıtlı değil.',
                ], 403);
            }

            $call = $this->createPickupCall($kiosk, $student, $guardian);

            return response()->json([
                'success' => true,
                'message' => $this->studentName($student) . ' için çağrı oluşturuldu.',
                'call' => $call,
            ]);
        }

        $student = Student::query()
            ->where(function ($query) use ($code): void {
                $query
                    ->where('qr_code', $code)
                    ->orWhere('card_uid', $code);
            })
            ->first();

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Öğrenci veya veli bulunamadı.',
            ], 404);
        }

        if ((int) $student->school_id !== (int) $kiosk->school_id) {
            return response()->json([
                'success' => false,
                'message' => 'Bu öğrenci kioskun bağlı olduğu okulda kayıtlı değil.',
            ], 403);
        }

        $call = $this->createPickupCall($kiosk, $student, null);

        return response()->json([
            'success' => true,
            'message' => $this->studentName($student) . ' için çağrı oluşturuldu.',
            'call' => $call,
        ]);
    }

    private function createPickupCall(
        Kiosk $kiosk,
        Student $student,
        ?Guardian $guardian,
    ): PickupCall {
        $call = PickupCall::create([
            'school_id' => $student->school_id,
            'student_id' => $student->id,
            'guardian_id' => $guardian?->id,
            'school_class_id' => $student->school_class_id,
            'kiosk_id' => $kiosk->id,
            'status' => 'waiting',
            'called_at' => now(),
        ]);

        PickupCallCreated::dispatch($call);

        return $call;
    }

    private function studentName(Student $student): string
    {
        return $student->full_name
            ?: $student->name
            ?: trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))
            ?: 'Öğrenci';
    }
}