<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Models\User;
use App\Notifications\NewServiceNotification;
use Filament\Resources\Pages\CreateRecord;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    /**
     * Inject user_id otomatis untuk Admin Layanan
     * (field ini di-hide dari view admin layanan, hanya Super Admin yang bisa lihat & isi sendiri)
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()->isAdminLayanan()) {
            $data['user_id'] = auth()->id();
        }
        return $data;
    }

    /**
     * Setelah layanan tersimpan ke database, kirim email notifikasi ke semua Super Admin.
     * Hanya dikirim jika yang membuat adalah Admin Layanan — bukan Super Admin.
     */
    protected function afterCreate(): void
    {
        if (!auth()->user()->isAdminLayanan()) {
            return; // Super Admin buat layanan sendiri → tidak perlu notif
        }

        $service = $this->record->load(['user', 'category']);

        User::where('role', User::ROLE_SUPER_ADMIN)
            ->get()
            ->each(fn(User $superAdmin) => $superAdmin->notify(
                new NewServiceNotification($service)
            ));
    }
}

