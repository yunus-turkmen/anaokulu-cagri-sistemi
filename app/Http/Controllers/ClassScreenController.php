<?php

namespace App\Http\Controllers;

use App\Models\PickupCall;
use App\Models\SchoolClass;

class ClassScreenController extends Controller
{
    public function show($id)
    {
        $schoolClass = SchoolClass::findOrFail($id);

        $calls = PickupCall::with('student')
            ->where('school_class_id', $id)
            ->where('status', 'waiting')
            ->latest()
            ->get();

        return view('class-screen.show', compact('schoolClass', 'calls'));
    }

   public function complete(\Illuminate\Http\Request $request, $id)
{
    $call = \App\Models\PickupCall::findOrFail($id);

    $call->update([
        'status' => 'completed',
        'completed_at' => now(),
    ]);

    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'Öğrenci teslim edildi olarak işaretlendi.',
            'call_id' => $call->id,
        ]);
    }

    return back()->with(
        'success',
        'Öğrenci teslim edildi olarak işaretlendi.'
    );
}
}