<?php

namespace App\Filament\Widgets;

use App\Models\PickupCall;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class WeeklyCallsChart extends ChartWidget
{
    protected ?string $heading = 'Son 7 Günlük Çağrı Grafiği';

    protected ?string $description = 'Toplam çağrı ve teslim edilen öğrenci sayıları';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        $labels = [];
        $calls = [];
        $completed = [];

        for ($day = 6; $day >= 0; $day--) {
            $date = now()->subDays($day);

            $labels[] = $date->translatedFormat('d M');

            $calls[] = $this->baseQuery()
                ->whereDate('called_at', $date->toDateString())
                ->count();

            $completed[] = $this->baseQuery()
                ->where('status', 'completed')
                ->whereDate('completed_at', $date->toDateString())
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Toplam Çağrı',
                    'data' => $calls,
                ],
                [
                    'label' => 'Teslim Edilen',
                    'data' => $completed,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function baseQuery(): Builder
    {
        $query = PickupCall::query();
        $user = auth()->user();

        if (! $user || $user->role === 'super_admin') {
            return $query;
        }

        if ($user->role === 'teacher') {
            return $query
                ->where('school_id', $user->school_id)
                ->where('school_class_id', $user->school_class_id);
        }

        return $query->where('school_id', $user->school_id);
    }
}