<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PickupCall;
use App\Models\Student;
use Illuminate\Http\Request;

class KioskCallController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $student = Student::where('qr_code', $request->qr_code)
            ->orWhere('card_uid', $request->qr_code)
            ->first();

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Öğrenci bulunamadı.',
            ], 404);
        }

        $call = PickupCall::create([
            'school_id' => $student->school_id,
            'student_id' => $student->id,
            'school_class_id' => $student->school_class_id,
            'status' => 'waiting',
            'called_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $student->name . ' için çağrı oluşturuldu.',
            'call' => $call,
        ]);
    }
}
