<?php

namespace App\Filament\Widgets;

use App\Models\PickupCall;
use App\Models\SchoolClass;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TeacherDashboard extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $user = auth()->user();

        if (! $user || $user->role !== 'teacher') {
            return [];
        }

        $schoolClass = SchoolClass::find($user->school_class_id);

        $todayCalls = PickupCall::query()
            ->where('school_id', $user->school_id)
            ->where('school_class_id', $user->school_class_id)
            ->whereDate('called_at', today())
            ->count();

        $waitingCalls = PickupCall::query()
            ->where('school_id', $user->school_id)
            ->where('school_class_id', $user->school_class_id)
            ->where('status', 'waiting')
            ->count();

        return [
            Stat::make(
                'Sınıfınız',
                $schoolClass?->name ?? 'Sınıf atanmadı'
            )
                ->description('Öğretmen sınıfı')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make('Bugünkü Çağrılar', $todayCalls)
                ->description('Bugün oluşturulan çağrı sayısı')
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color('info'),

            Stat::make('Bekleyen Çağrılar', $waitingCalls)
                ->description('Teslim edilmeyi bekleyen öğrenciler')
                ->descriptionIcon('heroicon-m-clock')
                ->color($waitingCalls > 0 ? 'warning' : 'success'),

            Stat::make('Çağrı Ekranı', 'Aç')
                ->description('Canlı sınıf çağrı ekranını aç')
                ->descriptionIcon('heroicon-m-arrow-top-right-on-square')
                ->url(
                    fn (): string =>
                        url('/class-screen/' . $user->school_class_id)
                )
                ->openUrlInNewTab()
                ->color('success'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->role === 'teacher';
    }
}