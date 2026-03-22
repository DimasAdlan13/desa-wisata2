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
                Forms\Components\TextInput::make('user.name')->label('Wisatawan')->disabled(),
                Forms\Components\TextInput::make('service.name')->label('Layanan')->disabled(),
                Forms\Components\DatePicker::make('booking_date')->label('Tanggal Wisata')->disabled(),
                Forms\Components\TextInput::make('pax')->label('Jumlah Pax')->disabled(),
                Forms\Components\TextInput::make('total_price')->label('Total Harga')->prefix('Rp')->disabled(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending'   => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'rejected'  => 'Rejected',
                    ]),
                Forms\Components\Textarea::make('rejection_reason')->label('Alasan Penolakan')->rows(2),
            ])->columns(2),

            Forms\Components\Section::make('Bukti Pembayaran')->schema([
                Forms\Components\FileUpload::make('payment_proof')
                    ->label('Bukti Bayar')
                    ->image()
                    ->disk('public')
                    ->directory('payment-proofs')
                    ->disabled(),
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
                Tables\Columns\TextColumn::make('pax')->label('Pax')->alignCenter(),
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
                Tables\Columns\IconColumn::make('payment_proof')
                    ->label('Bukti Bayar')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
            ])
            ->filters([
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
                Tables\Actions\Action::make('confirm_payment')
                    ->label('Konfirmasi Bayar')
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->visible(fn(Booking $r) => $r->status === 'pending' && $r->payment_proof)
                    ->requiresConfirmation()
                    ->action(function (Booking $record) {
                        (new BookingService())->confirmPayment($record, auth()->user());
                        Notification::make()->title('Pembayaran dikonfirmasi!')->success()->send();
                    }),

                Tables\Actions\Action::make('complete')
                    ->label('Selesai')
                    ->icon('heroicon-o-check')
                    ->color('primary')
                    ->visible(fn(Booking $r) => $r->status === 'confirmed')
                    ->requiresConfirmation()
                    ->action(function (Booking $record) {
                        (new BookingService())->completeBooking($record);
                        Notification::make()->title('Booking diselesaikan!')->success()->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn(Booking $r) => in_array($r->status, ['pending', 'confirmed']))
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Booking $record, array $data) {
                        (new BookingService())->rejectBooking($record, $data['rejection_reason']);
                        Notification::make()->title('Booking ditolak!')->warning()->send();
                    }),

                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'view'  => Pages\ViewBooking::route('/{record}'),
        ];
    }
}
