<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class VisitorOriginChart extends ChartWidget
{
    protected static ?string $heading = 'Asal Daerah Wisatawan';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 2;

    /**
     * Hanya Super Admin yang bisa melihat widget ini.
     */
    public static function canView(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    protected function getData(): array
    {
        // Ambil 10 provinsi terbanyak dari wisatawan yang sudah mengisi asal daerah
        $data = User::query()
            ->where('role', 'wisatawan')
            ->whereNotNull('province')
            ->where('province', '!=', '')
            ->selectRaw('province, COUNT(*) as total')
            ->groupBy('province')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label'           => 'Jumlah Wisatawan',
                    'data'            => $data->pluck('total')->toArray(),
                    'backgroundColor' => [
                        'rgba(20, 184, 166, 0.8)',   // teal-500
                        'rgba(6, 182, 212, 0.8)',    // cyan-500
                        'rgba(59, 130, 246, 0.8)',   // blue-500
                        'rgba(139, 92, 246, 0.8)',   // violet-500
                        'rgba(236, 72, 153, 0.8)',   // pink-500
                        'rgba(249, 115, 22, 0.8)',   // orange-500
                        'rgba(234, 179, 8, 0.8)',    // yellow-500
                        'rgba(34, 197, 94, 0.8)',    // green-500
                        'rgba(239, 68, 68, 0.8)',    // red-500
                        'rgba(107, 114, 128, 0.8)',  // gray-500
                    ],
                    'borderColor'     => 'transparent',
                    'borderRadius'    => 6,
                ],
            ],
            'labels' => $data->pluck('province')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',   // Horizontal bar chart (lebih mudah dibaca untuk nama provinsi)
            'plugins'   => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'callbacks' => [],
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'ticks'       => ['stepSize' => 1],
                    'grid'        => ['color' => 'rgba(0,0,0,0.05)'],
                ],
                'y' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }
}
