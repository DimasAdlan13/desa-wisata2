<?php

namespace Database\Seeders;

use App\Models\Content;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $contents = [
            [
                'type'  => 'umkm',
                'title' => 'Kerajinan Tangan Khas Pulau Pramuka',
                'body'  => '<p>Pulau Pramuka menyimpan kekayaan kerajinan tangan yang dibuat oleh warga lokal. Dari anyaman bambu, perhiasan kulit kerang, hingga miniatur perahu nelayan, semua bisa kamu temukan di sini.</p><p>Para pengrajin lokal menjual produk mereka langsung dari rumah atau di lapak kecil dekat dermaga. Harga sangat terjangkau dan bisa dijadikan oleh-oleh unik untuk keluarga.</p>',
            ],
            [
                'type'  => 'kuliner',
                'title' => 'Ikan Bakar Khas Kepulauan Seribu yang Wajib Dicoba',
                'body'  => '<p>Tidak lengkap rasanya berkunjung ke Kepulauan Seribu tanpa mencicipi ikan bakar segar di tepi pantai. Berbagai jenis ikan laut seperti kerapu, kakap, dan cumi bakar tersedia dengan harga yang sangat bersahabat.</p><p>Nikmati makan siang dengan pemandangan laut biru yang memukau — pengalaman yang tidak akan mudah dilupakan.</p>',
            ],
            [
                'type'  => 'info_wisata',
                'title' => 'Cara Menuju Pulau Pramuka dari Jakarta',
                'body'  => '<p>Untuk menuju Pulau Pramuka dari Jakarta, kamu bisa naik kapal dari Dermaga Muara Angke. Perjalanan memakan waktu sekitar 2-3 jam dengan kapal reguler, atau 45-60 menit dengan kapal cepat.</p><p><strong>Tips:</strong> Berangkatlah pagi hari (sekitar pukul 07.00-08.00) untuk mendapatkan cuaca terbaik dan pemandangan yang indah di perjalanan.</p>',
            ],
            [
                'type'  => 'info_wisata',
                'title' => 'Musim Terbaik untuk Mengunjungi Kepulauan Seribu',
                'body'  => '<p>Waktu terbaik untuk berkunjung ke Kepulauan Seribu adalah antara bulan April-Oktober (musim panas). Pada periode ini, laut relatif tenang dan visibilitas bawah air sangat baik untuk snorkeling dan diving.</p><p>Hindari kunjungan pada musim hujan (November-Maret) karena gelombang laut bisa cukup tinggi dan mengganggu aktivitas wisata.</p>',
            ],
        ];

        foreach ($contents as $data) {
            Content::firstOrCreate(
                ['slug' => Str::slug($data['title']) . '-' . Str::random(5)],
                [
                    ...$data,
                    'slug'         => Str::slug($data['title']) . '-' . Str::random(5),
                    'is_published' => true,
                    'published_at' => now(),
                ]
            );
        }

        $this->command->info('✅ Contents seeded: ' . count($contents) . ' articles');
    }
}
