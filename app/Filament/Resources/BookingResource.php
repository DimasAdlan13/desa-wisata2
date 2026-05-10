<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Services\BookingService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Data Booking';
    protected static ?string $navigationGroup = 'Manajemen Layanan';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detail Booking')->schema([
                Forms\Components\TextInput::make('booking_code')->label('Kode Booking')->disabled(),
                Forms\Components\Placeholder::make('wisatawan_name')
                    ->label('Wisatawan')
                    ->content(fn (?Booking $record) => $record?->user?->name ?? '-'),
                Forms\Components\Placeholder::make('service_name')
                    ->label('Layanan')
                    ->content(fn (?Booking $record) => $record?->service?->name ?? '-'),
                Forms\Components\DatePicker::make('booking_date')->label('Tanggal Wisata')->disabled(),
                Forms\Components\Placeholder::make('pax')
                    ->label(fn (?Booking $record) => 'Jumlah ' . ($record?->service?->unit_name ?? 'Pax'))
                    ->content(fn (?Booking $record) => $record?->pax ?? '-'),
                Forms\Components\TextInput::make('total_price')->label('Total Harga')->prefix('Rp')->disabled(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending'   => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'rejected'  => 'Rejected',
                    ])
                    ->live(),
                Forms\Components\Textarea::make('rejection_reason')
                    ->label('Alasan Penolakan')
                    ->rows(2)
                    ->visible(fn (Forms\Get $get) => in_array($get('status'), ['rejected', 'cancelled']))
                    ->required(fn (Forms\Get $get) => $get('status') === 'rejected'),
            ])->columns(2),

            Forms\Components\Section::make('Bukti Pembayaran')->schema([
                Forms\Components\FileUpload::make('payment_proof')
                    ->label('Bukti Bayar')
                    ->image()
                    ->imageResizeMode('cover')
                    ->imageResizeTargetWidth('800')
                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                        $manager = new ImageManager(new Driver());
                        $filename = Str::random(40) . '.webp';
                        $path = 'payment-proofs/' . $filename;

                        $image = $manager->read($file->getRealPath());
                        $encoded = $image->toWebp(75);

                        Storage::disk('public')->put($path, (string) $encoded);

                        return $path;
                    })
                    ->maxSize(2048)
                    ->disk('public')
                    ->directory('payment-proofs')
                    ->visible(fn (Forms\Get $get) => in_array($get('status'), ['confirmed', 'completed']))
                    ->required(fn (Forms\Get $get) => $get('status') === 'confirmed'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->isAdminLayanan()) {
                    $query->whereHas('service', fn($q) => $q->where('user_id', auth()->id()));
                }
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('booking_code')->label('Kode Booking')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('user.name')->label('Wisatawan')->searchable(),
                Tables\Columns\TextColumn::make('service.name')->label('Layanan')->limit(30),
                Tables\Columns\TextColumn::make('booking_date')->label('Tanggal')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('pax')
                    ->label('Jumlah (Qty)')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state, Booking $record) => $state . ' ' . strtolower($record->service->unit_name ?? 'orang')),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'info'    => 'confirmed',
                        'success' => 'completed',
                        'gray'    => 'cancelled',
                        'danger'  => 'rejected',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service_id')
                    ->label('Layanan')
                    ->options(function () {
                        $user = auth()->user();
                        $query = \App\Models\Service::query()->where('is_approved', true)->where('is_active', true);

                        if ($user->isAdminLayanan()) {
                            $query->where('user_id', $user->id);
                        }

                        return $query->pluck('name', 'id')->toArray();
                    })
                    ->searchable(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'   => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'rejected'  => 'Rejected',
                    ]),
                Tables\Filters\Filter::make('booking_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari'),
                        Forms\Components\DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn($q, $v) => $q->whereDate('booking_date', '>=', $v))
                            ->when($data['until'], fn($q, $v) => $q->whereDate('booking_date', '<=', $v));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'edit'  => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
