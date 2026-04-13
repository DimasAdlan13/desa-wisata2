<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class BookingPerMonthChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Tren Booking (12 Bulan Terakhir)';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $serviceId = $this->filters['service_id'] ?? null;
        $user = auth()->user();

        $dataPending = [];
        $dataConfirmed = [];
        $dataCompleted = [];
        $dataCancelled = [];
        $labels = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonthsNoOverflow($i);
            $labels[] = $month->translatedFormat('M Y');

            $query = Booking::query()
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month);

            if ($user->isAdminLayanan()) {
                $query->whereHas('service', fn($q) => $q->where('user_id', $user->id));
            }

            if ($serviceId) {
                $query->where('service_id', $serviceId);
            }

            $dataPending[] = (clone $query)->where('status', Booking::STATUS_PENDING)->count();
            $dataConfirmed[] = (clone $query)->where('status', Booking::STATUS_CONFIRMED)->count();
            $dataCompleted[] = (clone $query)->where('status', Booking::STATUS_COMPLETED)->count();
            $dataCancelled[] = (clone $query)->whereIn('status', [Booking::STATUS_CANCELLED, Booking::STATUS_REJECTED])->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pending',
                    'data' => $dataPending,
                    'backgroundColor' => 'rgba(234, 179, 8, 0.8)', // Kuning
                    'borderColor' => '#eab308',
                ],
                [
                    'label' => 'Confirmed',
                    'data' => $dataConfirmed,
                    'backgroundColor' => 'rgba(56, 189, 248, 0.8)', // Biru
                    'borderColor' => '#38bdf8',
                ],
                [
                    'label' => 'Completed',
                    'data' => $dataCompleted,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)', // Hijau
                    'borderColor' => '#22c55e',
                ],
                [
                    'label' => 'Rejected/Cancelled',
                    'data' => $dataCancelled,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)', // Merah
                    'borderColor' => '#ef4444',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    // 'stacked' => true,
                ],
                'y' => [
                    // 'stacked' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
