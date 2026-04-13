<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class BookingStatusChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Booking Status Breakdown';
    protected static ?int $sort = 5;
    protected static ?string $maxHeight = '250px'; // <-- Membatasi tinggi agar senada dengan grafik lain

    protected function getData(): array
    {
        $serviceId = $this->filters['service_id'] ?? null;
        $user = auth()->user();

        $query = Booking::query();

        if ($user->isAdminLayanan()) {
            $query->whereHas('service', fn($q) => $q->where('user_id', $user->id));
        }

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        // Hitung jumlah masing-masing status
        $counts = [
            'pending'   => (clone $query)->where('status', Booking::STATUS_PENDING)->count(),
            'confirmed' => (clone $query)->where('status', Booking::STATUS_CONFIRMED)->count(),
            'completed' => (clone $query)->where('status', Booking::STATUS_COMPLETED)->count(),
            'cancelled' => (clone $query)->whereIn('status', [Booking::STATUS_CANCELLED, Booking::STATUS_REJECTED])->count(),
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Total Bookings',
                    'data' => array_values($counts),
                    'backgroundColor' => [
                        'rgba(234, 179, 8, 0.8)',   // Yellow for Pending
                        'rgba(56, 189, 248, 0.8)',  // Blue for Confirmed
                        'rgba(34, 197, 94, 0.8)',   // Green for Completed
                        'rgba(239, 68, 68, 0.8)',   // Red for Cancelled/Rejected
                    ],
                    'borderColor' => [
                        '#eab308', // Yellow
                        '#38bdf8', // Blue
                        '#22c55e', // Green
                        '#ef4444', // Red
                    ],
                ],
            ],
            'labels' => ['Pending', 'Confirmed', 'Completed', 'Rejected/Cancelled'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
