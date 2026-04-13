<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Tren Pendapatan (12 Bulan Terakhir)';
    protected static ?int $sort = 2; // Biar tampil setelah StatsOverview

    protected function getData(): array
    {
        $serviceId = $this->filters['service_id'] ?? null;
        $user = auth()->user();

        $data = [];
        $labels = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonthsNoOverflow($i);
            $labels[] = $month->translatedFormat('M Y');

            $query = Booking::query()
                ->where('status', Booking::STATUS_COMPLETED)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month);

            if ($user->isAdminLayanan()) {
                $query->whereHas('service', fn($q) => $q->where('user_id', $user->id));
            }

            if ($serviceId) {
                $query->where('service_id', $serviceId);
            }

            $data[] = $query->sum('total_price');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pendapatan (Rp)',
                    'data' => $data,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)', // Emerald/Green transparent
                    'borderColor' => '#10b981', // Emerald/Green solid
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
