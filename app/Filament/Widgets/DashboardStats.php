<?php

namespace App\Filament\Widgets;

use App\Models\Guardian;
use App\Models\PickupCall;
use App\Models\SchoolClass;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class DashboardStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        return [
            Stat::make('Toplam Öğrenci', $this->studentQuery()->count())
                ->description('Sistemde kayıtlı öğrenci')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),

            Stat::make('Toplam Veli', $this->guardianQuery()->count())
                ->description('Kayıtlı veli sayısı')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Toplam Sınıf', $this->classQuery()->count())
                ->description('Aktif sınıflar')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('gray'),

            Stat::make(
                'Bugünkü Çağrı',
                $this->callQuery()
                    ->whereDate('called_at', today())
                    ->count()
            )
                ->description('Bugün oluşturulan çağrılar')
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color('warning'),

            Stat::make(
                'Bekleyen',
                $this->callQuery()
                    ->where('status', 'waiting')
                    ->count()
            )
                ->description('Teslim edilmeyi bekliyor')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(
                'Bugün Teslim Edilen',
                $this->callQuery()
                    ->where('status', 'completed')
                    ->whereDate('completed_at', today())
                    ->count()
            )
                ->description('Bugün tamamlanan teslimler')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }

    private function studentQuery(): Builder
    {
        return $this->applyScope(Student::query());
    }

    private function guardianQuery(): Builder
    {
        $query = Guardian::query();
        $user = auth()->user();

        if (! $user || $user->role === 'super_admin') {
            return $query;
        }

        if ($user->role === 'teacher') {
            return $query->whereHas(
                'students',
                fn (Builder $studentQuery): Builder =>
                    $studentQuery->where(
                        'school_class_id',
                        $user->school_class_id
                    )
            );
        }

        return $query->where('school_id', $user->school_id);
    }

    private function classQuery(): Builder
    {
        $query = SchoolClass::query();
        $user = auth()->user();

        if (! $user || $user->role === 'super_admin') {
            return $query;
        }

        if ($user->role === 'teacher') {
            return $query->whereKey($user->school_class_id);
        }

        return $query->where('school_id', $user->school_id);
    }

    private function callQuery(): Builder
    {
        return $this->applyScope(PickupCall::query());
    }

    private function applyScope(Builder $query): Builder
    {
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
