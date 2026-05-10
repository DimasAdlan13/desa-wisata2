<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,
            ServiceCategorySeeder::class,
            ContentSeeder::class,
            AdminLayananSeeder::class,      // Buat akun admin pemilik layanan
            ServiceSeeder::class,           // Buat data layanan wisata
            WisatawanRatingSeeder::class,   // Buat wisatawan + booking + rating untuk SAW
        ]);
    }
}
