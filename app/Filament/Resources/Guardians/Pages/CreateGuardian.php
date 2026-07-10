<?php

namespace App\Filament\Resources\Guardians\Pages;

use App\Filament\Resources\Guardians\GuardianResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateGuardian extends CreateRecord
{
    protected static string $resource = GuardianResource::class;
    protected function afterCreate(): void
{
    $this->record->ensureStudentQrCodes();
}
    protected function handleRecordCreation(array $data): Model
    {
        $studentIds = $data['students'] ?? [];

        unset($data['students']);

        $guardian = static::getModel()::create($data);

        $syncData = [];

        foreach ($studentIds as $studentId) {
            do {
                $qrCode = 'GS-' . strtoupper(Str::random(32));
            } while (
                \DB::table('guardian_student')
                    ->where('qr_code', $qrCode)
                    ->exists()
            );

            $syncData[$studentId] = [
                'qr_code' => $qrCode,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $guardian->students()->sync($syncData);

        return $guardian;
    }
}