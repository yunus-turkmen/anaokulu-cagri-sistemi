<?php

namespace App\Http\Controllers\Api;
use App\Events\PickupCallCreated;
use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\PickupCall;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KioskCallController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'qr_code' => ['required', 'string'],
            'kiosk_id' => ['nullable', 'integer', 'exists:kiosks,id'],
        ]);

        $code = trim($request->qr_code);

        $guardianStudent = DB::table('guardian_student')
            ->where('qr_code', $code)
            ->first();

        if ($guardianStudent) {
            $guardian = Guardian::find($guardianStudent->guardian_id);
            $student = Student::find($guardianStudent->student_id);

            if (! $guardian || ! $student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Veli veya öğrenci kaydı bulunamadı.',
                ], 404);
            }

            $call = PickupCall::create([
                'school_id' => $student->school_id,
                'student_id' => $student->id,
                'guardian_id' => $guardian->id,
                'school_class_id' => $student->school_class_id,
                'kiosk_id' => $request->kiosk_id ?? 1,
                'status' => 'waiting',
                'called_at' => now(),
            ]);
PickupCallCreated::dispatch($call);
            return response()->json([
                'success' => true,
                'message' => $student->full_name . ' için çağrı oluşturuldu.',
                'call' => $call,
            ]);
        }

        $student = Student::query()
            ->where('qr_code', $code)
            ->orWhere('card_uid', $code)
            ->first();

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Öğrenci veya veli bulunamadı.',
            ], 404);
        }

        $call = PickupCall::create([
            'school_id' => $student->school_id,
            'student_id' => $student->id,
            'guardian_id' => null,
            'school_class_id' => $student->school_class_id,
            'kiosk_id' => $request->kiosk_id ?? 1,
            'status' => 'waiting',
            'called_at' => now(),
        ]);
PickupCallCreated::dispatch($call);
        return response()->json([
            'success' => true,
            'message' => $student->full_name . ' için çağrı oluşturuldu.',
            'call' => $call,
        ]);
    }
}