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
    protected static ?int $sort = 2;

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

        $totalRevenue = array_sum($data);

        return [
            'datasets' => [
                [
                    'label' => 'Total Pendapatan (Rp)',
                    'data' => $data,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'borderColor' => '#10b981',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
            '__total' => $totalRevenue,
        ];
    }

    public function getDescription(): ?string
    {
        $serviceId = $this->filters['service_id'] ?? null;
        $user = auth()->user();

        $query = \App\Models\Booking::query()
            ->where('status', \App\Models\Booking::STATUS_COMPLETED)
            ->whereBetween('created_at', [
                \Illuminate\Support\Carbon::now()->subMonths(11)->startOfMonth(),
                \Illuminate\Support\Carbon::now()->endOfMonth(),
            ]);

        if ($user->isAdminLayanan()) {
            $query->whereHas('service', fn($q) => $q->where('user_id', $user->id));
        }
        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        $total = $query->sum('total_price');

        return 'Total 12 Bulan: Rp ' . number_format($total, 0, ',', '.');
    }

    protected function getType(): string
    {
        return 'line';
    }
}
