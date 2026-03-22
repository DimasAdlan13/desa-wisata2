<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        // Scope stats by admin_layanan's own services
        $bookingQuery = Booking::query();
        $serviceQuery = Service::query();

        if ($user->isAdminLayanan()) {
            $bookingQuery->whereHas('service', fn($q) => $q->where('user_id', $user->id));
            $serviceQuery->where('user_id', $user->id);
        }

        $totalBookings   = (clone $bookingQuery)->count();
        $pendingBookings = (clone $bookingQuery)->where('status', 'pending')->count();
        $totalRevenue    = (clone $bookingQuery)->where('status', 'completed')->sum('total_price');
        $activeServices  = (clone $serviceQuery)->where('is_approved', true)->where('is_active', true)->count();

        $avgRating = \App\Models\Rating::query()
            ->when($user->isAdminLayanan(), fn($q) =>
                $q->whereHas('service', fn($s) => $s->where('user_id', $user->id))
            )
            ->avg('rating');

        $stats = [
            Stat::make('Total Booking', $totalBookings)
                ->description('Semua booking masuk')
                ->icon('heroicon-o-calendar-days')
                ->color('primary'),

            Stat::make('Booking Pending', $pendingBookings)
                ->description('Menunggu konfirmasi')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Layanan Aktif', $activeServices)
                ->description('Layanan disetujui & aktif')
                ->icon('heroicon-o-briefcase')
                ->color('success'),

            Stat::make('Rating Rata-rata', $avgRating ? number_format($avgRating, 1) . ' ⭐' : 'Belum ada')
                ->description('Dari semua ulasan')
                ->icon('heroicon-o-star')
                ->color('info'),
        ];

        if ($user->isSuperAdmin()) {
            $pendingAdmins = User::where('role', 'admin_layanan')->where('is_approved', false)->count();
            $stats[] = Stat::make('Admin Pending', $pendingAdmins)
                ->description('Admin menunggu persetujuan')
                ->icon('heroicon-o-user-plus')
                ->color('danger');

            $totalRevenue = Booking::where('status', 'completed')->sum('total_price');
            $stats[] = Stat::make('Total Pendapatan', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Dari semua booking selesai')
                ->icon('heroicon-o-currency-dollar')
                ->color('success');
        }

        return $stats;
    }
}
