<?php
namespace App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Inject user_id otomatis untuk Admin Layanan karena field inputnya di-hide (hanya SuperAdmin yang bisa lihat)
        if (auth()->user()->isAdminLayanan()) {
            $data['user_id'] = auth()->id();
        }
        return $data;
    }
}
