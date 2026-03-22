<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Snorkeling',    'icon' => '🤿', 'description' => 'Aktivitas snorkeling di spot-spot terindah Kepulauan Seribu'],
            ['name' => 'Diving',        'icon' => '🥽', 'description' => 'Scuba diving untuk menjelajahi kedalaman laut Kepulauan Seribu'],
            ['name' => 'Homestay',      'icon' => '🏠', 'description' => 'Menginap di rumah warga lokal dengan suasana autentik'],
            ['name' => 'Kuliner Tour',  'icon' => '🍽️', 'description' => 'Wisata kuliner khas Kepulauan Seribu: ikan bakar, seafood segar'],
            ['name' => 'Island Tour',   'icon' => '🏝️', 'description' => 'Tur keliling pulau-pulau cantik di Kepulauan Seribu'],
            ['name' => 'Fotografi',     'icon' => '📸', 'description' => 'Sesi foto dan dokumentasi wisata profesional'],
        ];

        foreach ($categories as $data) {
            ServiceCategory::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                [...$data, 'slug' => Str::slug($data['name']), 'is_active' => true]
            );
        }

        $this->command->info('✅ Service categories seeded: ' . count($categories) . ' categories');
    }
}
