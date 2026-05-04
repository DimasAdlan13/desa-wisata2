<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Services\BookingService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $oldStatus = $record->status;
        $newStatus = $data['status'] ?? $record->status;

        // Simpan data field form secara normal (termasuk upload bukti bayar)
        $record->update($data);

        // Jalankan logic BookingService sesuai perubahan status
        if ($oldStatus === 'pending' && $newStatus === 'confirmed') {
            (new BookingService())->confirmPayment($record, auth()->user());
        } elseif ($oldStatus === 'confirmed' && $newStatus === 'completed') {
            (new BookingService())->completeBooking($record);
        } elseif (in_array($oldStatus, ['pending', 'confirmed']) && $newStatus === 'rejected') {
            (new BookingService())->rejectBooking($record, $data['rejection_reason'] ?? '-');
        } elseif ($oldStatus !== 'cancelled' && $newStatus === 'cancelled') {
            (new BookingService())->cancelBooking($record, $data['rejection_reason'] ?? null);
        }

        return $record->fresh();
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
