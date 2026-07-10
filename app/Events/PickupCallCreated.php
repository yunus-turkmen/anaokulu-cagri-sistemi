<?php

namespace App\Events;

use App\Models\PickupCall;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PickupCallCreated implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public PickupCall $pickupCall
    ) {
        $this->pickupCall->load([
            'student',
            'guardian',
            'parentGuardian',
            'schoolClass',
            'kiosk',
        ]);
    }

    public function broadcastOn(): array
    {
        return [
            new Channel(
                'school.' .
                $this->pickupCall->school_id .
                '.class.' .
                $this->pickupCall->school_class_id
            ),
        ];
    }

    public function broadcastAs(): string
    {
        return 'pickup-call.created';
    }

    public function broadcastWith(): array
    {
        $guardian = $this->pickupCall->guardian
            ?? $this->pickupCall->parentGuardian;

        return [
            'id' => $this->pickupCall->id,
            'student_name' => $this->pickupCall->student?->full_name
                ?: $this->pickupCall->student?->name,
            'guardian_name' => $guardian?->full_name
                ?: $guardian?->name,
            'relationship' => $guardian?->relationship,
            'class_name' => $this->pickupCall->schoolClass?->name,
            'kiosk_name' => $this->pickupCall->kiosk?->name,
            'called_at' => optional($this->pickupCall->called_at)
                ->format('H:i:s'),
        ];
    }
}