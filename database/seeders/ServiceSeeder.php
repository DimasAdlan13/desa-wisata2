<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // ── Langkah 1: Cari atau buat akun Mitra (admin_layanan) ──────────────
        // Setiap layanan di sistem ini HARUS dimiliki oleh seorang Mitra.
        // Kita cari dulu apakah sudah ada akun admin_layanan di database.
        // Kalau sudah ada → pakai yang pertama ditemukan.
        // Kalau belum ada sama sekali → buatkan satu akun demo khusus seeder ini.
        $mitra = User::where('role', 'admin_layanan')->first();

        if (!$mitra) {
            $mitra = User::create([
                'name'              => 'Pengelola Demo',
                'email'             => 'mitra.demo@dewisata.id',
                'password'          => bcrypt('password123'),
                'role'              => 'admin_layanan',
                'business_name'     => 'Wisata Pramuka',
                'is_approved'       => true,
                'email_verified_at' => now(),
            ]);
            $this->command->info('  → Akun mitra demo dibuat: mitra.demo@dewisata.id');
        } else {
            $this->command->info('  → Menggunakan mitra yang sudah ada: ' . $mitra->email);
        }

        // ── Langkah 2: Ambil semua kategori dari database ────────────────────
        // Kita map slug → id supaya bisa reference dengan mudah di bawah.
        // Contoh: $categories['snorkeling'] → id = 1
        $categories = ServiceCategory::pluck('id', 'slug');

        // ── Langkah 3: Temukan Super Admin sebagai approver ──────────────────
        // Di sistem nyata, Super Admin yang menyetujui tiap layanan Mitra.
        // Seeder ini bypass proses approval manual dengan langsung set is_approved=true.
        $superAdmin = User::where('role', 'super_admin')->first();

        // ── Langkah 4: Data layanan yang akan di-seed ────────────────────────
        $services = [
            // --- Snorkeling (3 layanan) ---
            [
                'category_slug'  => 'snorkeling',
                'name'           => 'Snorkeling Spot Pulau Semak Daun',
                'description'    => '<p>Nikmati pengalaman snorkeling di salah satu spot terbaik Kepulauan Seribu. Terumbu karang yang masih terjaga dengan ikan-ikan berwarna warni menjadi daya tarik utama. Paket sudah termasuk peralatan snorkeling lengkap, pemandu berpengalaman, dan transportasi boat.</p>',
                'price'          => 150000,
                'quota_per_day'  => 15,
                'location'       => 'Pulau Semak Daun, Kepulauan Seribu',
                'contact_person' => '085812345601',
            ],
            [
                'category_slug'  => 'snorkeling',
                'name'           => 'Snorkeling Malam (Night Snorkeling)',
                'description'    => '<p>Sensasi unik snorkeling di malam hari! Saksikan kehidupan laut yang berbeda saat malam tiba. Plankton bercahaya dan ikan malam menjadi pemandangan tak terlupakan. Paket termasuk senter bawah air, baju wet suit, dan pemandu profesional bersertifikat.</p>',
                'price'          => 250000,
                'quota_per_day'  => 10,
                'location'       => 'Pulau Pramuka, Kepulauan Seribu',
                'contact_person' => '085812345602',
            ],
            [
                'category_slug'  => 'snorkeling',
                'name'           => 'Paket Snorkeling 3 Spot Terbaik',
                'description'    => '<p>Jelajahi tiga spot snorkeling terpopuler dalam satu hari! Spot Gosong Pandan, Batu Kodok, dan Karang Besar menanti Anda. Cocok untuk keluarga dan rombongan. Termasuk makan siang seafood di atas boat dan dokumentasi foto.</p>',
                'price'          => 350000,
                'quota_per_day'  => 12,
                'location'       => 'Pulau Pramuka, Kepulauan Seribu',
                'contact_person' => '085812345603',
            ],

            // --- Diving (2 layanan) ---
            [
                'category_slug'  => 'diving',
                'name'           => 'Fun Dive untuk Pemula',
                'description'    => '<p>Pertama kali menyelam? Paket Fun Dive kami dirancang khusus untuk pemula. Instruktur bersertifikat PADI akan mendampingi Anda dari latihan di permukaan hingga penyelaman pertama di kedalaman 5-10 meter. Terumbu karang yang indah siap menyambut Anda!</p>',
                'price'          => 450000,
                'quota_per_day'  => 8,
                'location'       => 'Pulau Pramuka, Kepulauan Seribu',
                'contact_person' => '085812345604',
            ],
            [
                'category_slug'  => 'diving',
                'name'           => 'Advanced Dive — Kapal Karam WWII',
                'description'    => '<p>Pengalaman diving tak terlupakan ke bangkai kapal era Perang Dunia II. Spot legendaris ini kini menjadi rumah bagi ratusan spesies ikan dan karang yang memukau. Khusus penyelam berpengalaman dengan sertifikat minimal Open Water.</p>',
                'price'          => 650000,
                'quota_per_day'  => 6,
                'location'       => 'Perairan Pulau Pramuka, Kepulauan Seribu',
                'contact_person' => '085812345605',
            ],

            // --- Homestay (2 layanan) ---
            [
                'category_slug'  => 'homestay',
                'name'           => 'Homestay Keluarga Pak Haji Udin',
                'description'    => '<p>Rasakan kehangatan tinggal bersama keluarga nelayan lokal. Kamar bersih dengan AC, sarapan masakan rumahan, dan akses langsung ke pantai private. Lokasi strategis di jantung Pulau Pramuka, dekat dengan berbagai spot wisata.</p>',
                'price'          => 200000,
                'quota_per_day'  => 4,
                'location'       => 'Pulau Pramuka, RT 03, Kepulauan Seribu',
                'contact_person' => '085812345606',
            ],
            [
                'category_slug'  => 'homestay',
                'name'           => 'Villa Tepi Pantai Sunrise View',
                'description'    => '<p>Nikmati keindahan sunrise langsung dari villa private Anda! Fasilitas lengkap: AC, WiFi, dapur kecil, kamar mandi dalam, dan beranda menghadap laut. Paket sudah termasuk sarapan dan makan malam seafood bakar.</p>',
                'price'          => 450000,
                'quota_per_day'  => 2,
                'location'       => 'Pantai Timur, Pulau Pramuka, Kepulauan Seribu',
                'contact_person' => '085812345607',
            ],

            // --- Kuliner Tour (2 layanan) ---
            [
                'category_slug'  => 'kuliner-tour',
                'name'           => 'Wisata Kuliner Seafood Bakar Pramuka',
                'description'    => '<p>Jelajahi warung-warung seafood terbaik di Pulau Pramuka! Tur dimulai dari pasar ikan lokal, kemudian kunjungi 3 warung kuliner ikonik dengan menu ikan bakar, kepiting saus padang, dan cumi goreng tepung.</p>',
                'price'          => 125000,
                'quota_per_day'  => 20,
                'location'       => 'Pasar Ikan Pulau Pramuka, Kepulauan Seribu',
                'contact_person' => '085812345608',
            ],
            [
                'category_slug'  => 'kuliner-tour',
                'name'           => 'Kelas Masak Olahan Ikan Laut',
                'description'    => '<p>Belajar memasak hidangan khas Kepulauan Seribu langsung dari ibu-ibu nelayan! Menu yang dipelajari: ikan asar (asap tradisional), pepes ikan kembung, dan keripik ikan tenggiri. Semua bahan segar dari laut.</p>',
                'price'          => 175000,
                'quota_per_day'  => 10,
                'location'       => 'Balai Warga, Pulau Pramuka, Kepulauan Seribu',
                'contact_person' => '085812345609',
            ],

            // --- Island Tour (1 layanan) ---
            [
                'category_slug'  => 'island-tour',
                'name'           => 'Island Hopping 5 Pulau (Full Day)',
                'description'    => '<p>Petualangan sehari penuh mengunjungi 5 pulau cantik di Kepulauan Seribu: Pulau Pari, Pulau Tidung, Pulau Air, Pulau Lancang, dan Pulau Bokor. Termasuk transportasi speedboat, pemandu wisata, makan siang, dan snorkeling di satu spot pilihan.</p>',
                'price'          => 400000,
                'quota_per_day'  => 20,
                'location'       => 'Dermaga Pulau Pramuka, Kepulauan Seribu',
                'contact_person' => '085812345610',
            ],

            // --- Fotografi (2 layanan) ---
            [
                'category_slug'  => 'fotografi',
                'name'           => 'Sesi Foto Prewedding Tepi Pantai',
                'description'    => '<p>Abadikan momen spesial Anda dengan latar pantai berpasir putih dan lautan biru Kepulauan Seribu. Paket termasuk fotografer profesional (4 jam), 50 foto edit terpilih, akses ke 3 lokasi pemotretan, dan boat transport.</p>',
                'price'          => 750000,
                'quota_per_day'  => 3,
                'location'       => 'Pantai Pulau Pramuka, Kepulauan Seribu',
                'contact_person' => '085812345611',
            ],
            [
                'category_slug'  => 'fotografi',
                'name'           => 'Dokumentasi Wisata Underwater',
                'description'    => '<p>Abadikan petualangan bawah laut Anda dengan kamera underwater profesional. Paket termasuk fotografer selama snorkeling/diving, 30 foto edit terpilih, dan 1 video highlight berdurasi 2 menit.</p>',
                'price'          => 300000,
                'quota_per_day'  => 5,
                'location'       => 'Spot Snorkeling Pulau Pramuka, Kepulauan Seribu',
                'contact_person' => '085812345612',
            ],
        ];

        // ── Langkah 5: Masukkan satu per satu ke database ───────────────────
        // firstOrCreate: cek dulu berdasarkan 'name'. Kalau sudah ada → skip.
        // Kalau belum ada → buat baru dengan semua kolom yang didefinisikan.
        $count = 0;
        foreach ($services as $data) {
            $categoryId = $categories[$data['category_slug']] ?? null;
            if (!$categoryId) {
                $this->command->warn('  ⚠ Kategori tidak ditemukan: ' . $data['category_slug']);
                continue;
            }

            $created = Service::firstOrCreate(
                ['name' => $data['name']],
                [
                    'user_id'        => $mitra->id,
                    'category_id'    => $categoryId,
                    'slug'           => Str::slug($data['name']) . '-' . Str::random(5),
                    'description'    => $data['description'],
                    'price'          => $data['price'],
                    'quota_per_day'  => $data['quota_per_day'],
                    'location'       => $data['location'],
                    'contact_person' => $data['contact_person'],
                    'is_approved'    => true,       // bypass manual approval
                    'approved_by'    => $superAdmin?->id,
                    'approved_at'    => now(),
                    'is_active'      => true,
                ]
            );

            if ($created->wasRecentlyCreated) $count++;
        }

        $this->command->info("✅ ServiceSeeder selesai: {$count} layanan baru ditambahkan!");
    }
}
