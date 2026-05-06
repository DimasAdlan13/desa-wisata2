<?php

namespace App\Filament\Widgets;

use App\Models\Rating;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentReviewsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Ulasan Pelanggan Terbaru';
    protected static ?int $sort = 6;
    public function getColumnSpan(): int | string | array
    {
        // 1 kolom di desktop jika difilter, full kolom di mobile atau jika di menu 'Semua Layanan'
        return !empty($this->filters['service_id']) ? [
            'default' => 'full',
            'md' => 1,
        ] : 'full';
    }

    public function table(Table $table): Table
    {
        $serviceId = $this->filters['service_id'] ?? null;
        $user = auth()->user();

        $query = Rating::query()->latest();

        if ($user->isAdminLayanan()) {
            $query->whereHas('service', fn($q) => $q->where('user_id', $user->id));
        }

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('booking.user.name')
                    ->label('Wisatawan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Layanan')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: $serviceId !== null),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state === 3 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn (int $state): string => $state . ' ⭐'),
                Tables\Columns\TextColumn::make('review')
                    ->label('Ulasan Pelanggan')
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc');
    }
}
