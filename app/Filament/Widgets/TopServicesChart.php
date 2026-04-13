<?php

namespace App\Filament\Widgets;

use App\Models\Service;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class TopServicesChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Tren Layanan (Paling Banyak Dipesan)';
    protected static ?int $sort = 4;

    public function render(): \Illuminate\Contracts\View\View
    {
        $serviceId = $this->filters['service_id'] ?? null;
        if ($serviceId) {
            return view('components.empty-widget');
        }

        return parent::render();
    }

    protected function getData(): array
    {
        $serviceId = $this->filters['service_id'] ?? null;
        $user = auth()->user();

        // Ambil top 5 layanan yang paling banyak dibooking
        $query = Service::query()
            ->withCount('bookings')
            ->orderByDesc('bookings_count')
            ->take(5);

        if ($user->isAdminLayanan()) {
            $query->where('user_id', $user->id);
        }

        if ($serviceId) {
            $query->where('id', $serviceId);
        }

        $services = $query->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Booking Masuk',
                    'data' => $services->pluck('bookings_count')->toArray(),
                    'backgroundColor' => 'rgba(249, 115, 22, 0.8)', // Orange
                    'borderColor' => '#f97316',
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $services->pluck('name')->map(fn($name) => \Illuminate\Support\Str::limit($name, 15))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
        ];
    }
}
