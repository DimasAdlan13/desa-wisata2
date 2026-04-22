<?php

namespace App\Filament\Pages;

use App\Models\Service;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
            Select::make('service_id')
            ->label('Filter Laporan Berdasarkan Layanan')
            ->options(function () {
            $user = auth()->user();
            $query = Service::query()->where('is_approved', true)->where('is_active', true);

            if ($user->isAdminLayanan()) {
                $query->where('user_id', $user->id);
            }

            return $query->pluck('name', 'id')->toArray();
        })
            ->searchable()
            ->placeholder('Semua Layanan (Keseluruhan)')
            ->columnSpanFull(),
        ]);
    }

    /**
     * Widget yang muncul DI ATAS filter form
     */
    protected function getHeaderWidgets(): array
    {
        return [
            \Filament\Widgets\AccountWidget::class ,
            \Filament\Widgets\FilamentInfoWidget::class ,
            \App\Filament\Widgets\StatsOverviewWidget::class ,
        ];
    }

    /**
     * Widget yang muncul DI BAWAH filter form
     */
    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\RevenueChart::class ,
            \App\Filament\Widgets\BookingPerMonthChart::class ,
            \App\Filament\Widgets\TopServicesChart::class ,
            \App\Filament\Widgets\BookingStatusChart::class ,
            \App\Filament\Widgets\VisitorOriginChart::class ,
            \App\Filament\Widgets\RecentReviewsWidget::class ,
        ];
    }
}
