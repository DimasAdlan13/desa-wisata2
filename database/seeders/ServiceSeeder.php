<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();

        // ── Pastikan Kategori Ada ─────────────────────────────────────────────
        $catHomestay  = ServiceCategory::firstOrCreate(['slug' => 'homestay'],     ['name' => 'Homestay',     'icon' => '🏠', 'description' => 'Menginap di rumah warga lokal Pulau Pramuka dengan suasana autentik.',         'is_active' => true]);
        $catDiving    = ServiceCategory::firstOrCreate(['slug' => 'diving'],       ['name' => 'Diving',       'icon' => '🥽', 'description' => 'Scuba diving menjelajahi kedalaman laut Kepulauan Seribu.',                    'is_active' => true]);
        $catPaket     = ServiceCategory::firstOrCreate(['slug' => 'paket-wisata'], ['name' => 'Paket Wisata', 'icon' => '🏝️', 'description' => 'Paket perjalanan wisata lengkap 2D1N ke Pulau Pramuka.',                      'is_active' => true]);
        $catRental    = ServiceCategory::firstOrCreate(['slug' => 'rental'],       ['name' => 'Rental',       'icon' => '🛵', 'description' => 'Penyewaan kendaraan dan perlengkapan wisata di Pulau Pramuka.',              'is_active' => true]);

        // ── Helper ────────────────────────────────────────────────────────────
        $admin = fn(string $email) => User::where('email', $email)->first();
        $create = function (array $d) use ($superAdmin): void {
            Service::firstOrCreate(
                ['name' => $d['name'], 'user_id' => $d['user']->id],
                [
                    'user_id'        => $d['user']->id,
                    'category_id'    => $d['cat']->id,
                    'name'           => $d['name'],
                    'description'    => $d['desc'],
                    'price'          => $d['price'],
                    'pricing_type'   => $d['pricing_type'] ?? 'per_pax',
                    'unit_name'      => $d['unit'] ?? 'Orang',
                    'quota_per_day'  => $d['quota'] ?? 10,
                    'location'       => 'Pulau Pramuka, Kepulauan Seribu, Jakarta Utara',
                    'contact_person' => $d['contact'],
                    'is_approved'    => true,
                    'approved_by'    => $superAdmin?->id,
                    'approved_at'    => now(),
                    'is_active'      => true,
                ]
            );
        };

        // ════════════════════════════════════════════════════════════════════
        // PENGINAPAN / HOMESTAY (19 layanan)
        // ════════════════════════════════════════════════════════════════════

        $create(['user' => $admin('serenok321@gmail.com'), 'cat' => $catHomestay,
            'name'    => 'Dolphin Homestay',
            'price'   => 304312, // Harga tetap
            'contact' => 'Ibu Rohada | 0815-7224-0499',
            'quota'   => 8,
            'desc'    => 'Rasakan pengalaman menginap yang nyaman di Dolphin Homestay, salah satu penginapan terpercaya di Pulau Pramuka. Kamar dilengkapi fasilitas AC dan kamar mandi dalam. Berlokasi strategis dekat dermaga, cocok untuk keluarga maupun pasangan yang ingin menikmati keindahan Kepulauan Seribu.',
        ]);

        $create(['user' => $admin('romanbis29@gmail.com'), 'cat' => $catHomestay,
            'name'    => 'Agil Homestay',
            'price'   => 500000, // Midpoint 250k-750k
            'contact' => 'Mbo Puput | 0813-1692-5840',
            'quota'   => 10,
            'desc'    => 'Agil Homestay menawarkan penginapan yang bersih, nyaman, dan terjangkau di tengah suasana khas Pulau Pramuka. Dikelola langsung oleh warga lokal, Anda akan merasakan kehangatan sambutan tuan rumah yang ramah. Tersedia pilihan kamar standar dan AC.',
        ]);

        $create(['user' => $admin('pakuntung@dewisata.test'), 'cat' => $catHomestay,
            'name'    => 'Villa Delima Pulau Pramuka',
            'price'   => 500000,
            'contact' => 'Pak Untung | 0813-1955-1955',
            'quota'   => 6,
            'desc'    => 'Villa Delima menghadirkan konsep penginapan villa di Pulau Pramuka dengan suasana hijau dan tenang. Ideal untuk keluarga kecil yang menginginkan privasi lebih. Fasilitas lengkap termasuk AC, TV, dan area bersantai yang nyaman.',
        ]);

        $create(['user' => $admin('mbomang@dewisata.test'), 'cat' => $catHomestay,
            'name'    => '3 Dara Homestay',
            'price'   => 500000, // Midpoint 250k-750k
            'contact' => 'Mbo Mang | 0858-8186-5535',
            'quota'   => 10,
            'desc'    => '3 Dara Homestay adalah penginapan khas warga lokal Pulau Pramuka yang menyediakan berbagai pilihan kamar dengan harga fleksibel. Nikmati suasana hangat bersama keluarga di lingkungan yang asri dan dekat dengan pusat aktivitas wisata pulau.',
        ]);

        $create(['user' => $admin('omjoel@dewisata.test'), 'cat' => $catHomestay,
            'name'    => "B'Joels Homestay",
            'price'   => 500000,
            'contact' => 'Om Joel | 0812-8209-888',
            'quota'   => 8,
            'desc'    => "B'Joels Homestay menghadirkan penginapan santai dengan gaya kasual di Pulau Pramuka. Cocok untuk wisatawan yang ingin merasakan nuansa pulau yang sesungguhnya. Kamar bersih, nyaman, dan pelayanan yang bersahabat dari tuan rumah.",
        ]);

        $create(['user' => $admin('mbahayati@dewisata.test'), 'cat' => $catHomestay,
            'name'    => 'Harris Homestay',
            'price'   => 500000, // Midpoint 250k-750k
            'contact' => 'Mba Hayati | 0851-5995-1101',
            'quota'   => 10,
            'desc'    => 'Harris Homestay menyediakan pilihan kamar yang beragam dengan harga terjangkau di Pulau Pramuka. Dikelola oleh Mba Hayati dengan pelayanan penuh perhatian, homestay ini cocok untuk wisatawan solo, pasangan, maupun keluarga kecil.',
        ]);

        $create(['user' => $admin('bundaelsa@dewisata.test'), 'cat' => $catHomestay,
            'name'    => 'Kedaton Homestay',
            'price'   => 400000,
            'contact' => 'Bunda Elsa | 0877-7698-3999',
            'quota'   => 8,
            'desc'    => 'Kedaton Homestay menawarkan suasana menginap yang hangat dan menyenangkan di Pulau Pramuka. Dikelola Bunda Elsa yang dikenal ramah dan perhatian kepada tamu. Fasilitas AC, kamar mandi bersih, dan sarapan tersedia sesuai permintaan.',
        ]);

        $create(['user' => $admin('mbopipit@dewisata.test'), 'cat' => $catHomestay,
            'name'    => 'Lagunda Homestay',
            'price'   => 325000, // Midpoint 300k-350k
            'contact' => 'Mbo Pipit | 0859-4117-2931',
            'quota'   => 10,
            'desc'    => 'Lagunda Homestay adalah pilihan tepat untuk wisatawan yang mencari penginapan nyaman dengan harga bersahabat di Pulau Pramuka. Berlokasi tidak jauh dari pantai, Anda bisa menikmati angin laut dan suasana pulau yang tenang sepanjang malam.',
        ]);

        $create(['user' => $admin('waondoy@dewisata.test'), 'cat' => $catHomestay,
            'name'    => 'Nuraini Homestay',
            'price'   => 325000, // Midpoint 300k-350k
            'contact' => 'Wa Ondoy | 0852-1949-2880',
            'quota'   => 10,
            'desc'    => 'Nuraini Homestay merupakan penginapan sederhana namun bersih dan nyaman di Pulau Pramuka. Cocok untuk wisatawan dengan anggaran terbatas yang tetap ingin menikmati pengalaman bermalam di Kepulauan Seribu. Pengelola lokal siap membantu kebutuhan selama kunjungan Anda.',
        ]);

        $create(['user' => $admin('royalmermaid@dewisata.test'), 'cat' => $catHomestay,
            'name'    => 'Royal Mermaid Homestay',
            'price'   => 525000, // Midpoint 450k-600k
            'contact' => 'Admin Royal Mermaid | 0878-8990-6755',
            'quota'   => 8,
            'desc'    => 'Royal Mermaid Homestay menghadirkan konsep penginapan semi-premium di Pulau Pramuka. Dengan fasilitas yang lebih lengkap dibanding homestay biasa, Anda akan menikmati kamar ber-AC, dekorasi bernuansa laut, serta akses mudah ke berbagai spot wisata terbaik di sekitar pulau.',
        ]);

        $create(['user' => $admin('serenok321@gmail.com'), 'cat' => $catHomestay,
            'name'    => 'RR Homestay',
            'price'   => 625000, // Midpoint 500k-750k
            'contact' => 'Ibu Rohada | 0877-7661-4046',
            'quota'   => 8,
            'desc'    => 'RR Homestay adalah penginapan pilihan di Pulau Pramuka yang menawarkan kenyamanan lebih dengan kamar-kamar yang luas dan bersih. Dikelola langsung oleh Ibu Rohada yang berpengalaman, tamu dijamin mendapatkan pelayanan terbaik selama menginap.',
        ]);

        $create(['user' => $admin('romanbis29@gmail.com'), 'cat' => $catHomestay,
            'name'    => 'Satu Putri Homestay',
            'price'   => 400000, // Midpoint 350k-450k
            'contact' => 'Mbo Puput | 0813-1692-5840',
            'quota'   => 10,
            'desc'    => 'Satu Putri Homestay menawarkan pengalaman menginap yang hangat dan personal di Pulau Pramuka. Dipercaya oleh ratusan wisatawan, homestay ini dikenal dengan kebersihan kamar dan keramahan Mbo Puput dalam melayani tamu.',
        ]);

        $create(['user' => $admin('idatibi@dewisata.test'), 'cat' => $catHomestay,
            'name'    => 'Sefty Homestay',
            'price'   => 600000, // Midpoint 400k-800k
            'contact' => 'Ida Tibi | 0878-8646-6312',
            'quota'   => 8,
            'desc'    => 'Sefty Homestay menghadirkan pilihan kamar yang beragam dengan fasilitas AC dan kamar mandi dalam di Pulau Pramuka. Cocok untuk grup wisatawan yang ingin kenyamanan lebih dengan harga yang masih terjangkau.',
        ]);

        $create(['user' => $admin('villashafir@dewisata.test'), 'cat' => $catHomestay,
            'name'    => 'Shafir Villa Homestay',
            'price'   => 600000, // Midpoint 400k-800k
            'contact' => 'Admin Villa Shafir | 0821-2368-3228',
            'quota'   => 6,
            'desc'    => 'Shafir Villa Homestay menggabungkan kenyamanan villa dengan keautentikan homestay lokal di Pulau Pramuka. Fasilitas modern tersedia di setiap kamar, termasuk AC, air panas, dan TV. Suasana villa yang tenang dan privat menjadi daya tarik utama.',
        ]);

        $create(['user' => $admin('paksuryadi@dewisata.test'), 'cat' => $catHomestay,
            'name'    => 'Tiara Syair Homestay',
            'price'   => 550000,
            'contact' => 'Pak Suryadi | 0856-1470-534',
            'quota'   => 8,
            'desc'    => 'Tiara Syair Homestay merupakan penginapan dengan nuansa seni dan budaya lokal yang kental di Pulau Pramuka. Dekorasi interior yang unik menjadikan homestay ini pilihan menarik bagi wisatawan yang ingin pengalaman berbeda dari penginapan biasa.',
        ]);

        $create(['user' => $admin('pakuntung@dewisata.test'), 'cat' => $catHomestay,
            'name'    => "Villa De'Lima Homestay",
            'price'   => 500000,
            'contact' => 'Pak Untung | 0813-1955-1955',
            'quota'   => 6,
            'desc'    => "Villa De'Lima Homestay adalah alternatif villa yang lebih terjangkau dari Pak Untung di Pulau Pramuka. Menyediakan kamar luas ber-AC dengan fasilitas dapur mini dan ruang santai, cocok untuk keluarga yang ingin liburan lebih privat.",
        ]);

        $create(['user' => $admin('pakrusdi@dewisata.test'), 'cat' => $catHomestay,
            'name'    => 'Dermaga Resort',
            'price'   => 1350000,
            'contact' => 'Pak Rusdi | 0811-8836-41',
            'quota'   => 5,
            'desc'    => 'Dermaga Resort adalah pilihan akomodasi premium di Pulau Pramuka dengan lokasi tepat di tepi dermaga. Nikmati pemandangan laut yang menakjubkan langsung dari kamar Anda. Fasilitas resort bintang dengan sentuhan kearifan lokal Kepulauan Seribu.',
        ]);

        $create(['user' => $admin('seribuResort@dewisata.test'), 'cat' => $catHomestay,
            'name'    => 'Seribu Resort',
            'price'   => 1025000, // Midpoint 850k-1.200k
            'contact' => 'Admin Seribu Resort | 0857-1572-2121',
            'quota'   => 5,
            'desc'    => 'Seribu Resort adalah resort terkemuka di Kepulauan Seribu yang menawarkan pengalaman menginap mewah di tengah alam tropis yang memesona. Dilengkapi dengan kolam renang, restoran seafood, dan akses langsung ke pantai pribadi.',
        ]);

        $create(['user' => $admin('pakhasbullah@dewisata.test'), 'cat' => $catHomestay,
            'name'    => 'Penginapan Apung Aquarius',
            'price'   => 350000,
            'contact' => 'Pak Hasbullah | 0812-9866-7376',
            'quota'   => 6,
            'desc'    => 'Rasakan sensasi unik bermalam di atas air di Penginapan Apung Aquarius, Pulau Pramuka. Rumah apung dengan pemandangan laut 360 derajat ini menjadi pengalaman tak terlupakan bagi setiap wisatawan. Nikmati deburan ombak dan suara alam langsung dari kamar Anda.',
        ]);

        // ════════════════════════════════════════════════════════════════════
        // PAKET WISATA (2 layanan)
        // ════════════════════════════════════════════════════════════════════

        $create(['user' => $admin('sultanwisata@dewisata.test'), 'cat' => $catPaket,
            'name'    => 'Sultan Wisata Pulau Pramuka 2D1N',
            'price'   => 1335000,
            'contact' => 'Admin Sultan Wisata | 081200000001',
            'quota'   => 20,
            'desc'    => "Paket wisata lengkap 2 Hari 1 Malam ke Pulau Pramuka. Termasuk: Transportasi kapal FERI PP dari Muara Angke, penginapan/homestay AC, makan 3x, alat snorkeling lengkap, kapal snorkeling ke spot terbaik, pemandu wisata darat & laut, BBQ malam, welcome drink, kamera underwater, dokumentasi foto snorkeling, wisata penangkaran penyu & hiu, serta jelajah Pulau Semak Daun, Pulau Air, dan Gusung Petrik. Biaya tidak termasuk: banana boat, tip guide, tiket penangkaran penyu WNA.",
        ]);

        $create(['user' => $admin('sadewa@dewisata.test'), 'cat' => $catPaket,
            'name'    => 'Sadewa Eco Tour 2D1N',
            'price'   => 860000,
            'contact' => 'Admin Sadewa Tour | 081200000002',
            'quota'   => 15,
            'desc'    => "Paket wisata ramah lingkungan 2 Hari 1 Malam ke Pulau Pramuka. Termasuk: Transportasi dari Muara Angke PP, asuransi perjalanan, penginapan AC, makan 3x prasmanan, alat snorkeling lengkap (1 set/peserta), kapal snorkeling eksklusif tidak digabung grup lain, wisata penangkaran penyu dan Resto Nusa Karamba, wisata Pulau Air dan Pulau Tanjung Cina, tour guide, BBQ ikan bakar di tepi pantai, kamera underwater gratis, dan air mineral selama snorkeling.",
        ]);

        // ════════════════════════════════════════════════════════════════════
        // AKTIVITAS AIR — DIVING (9 layanan)
        // ════════════════════════════════════════════════════════════════════

        $create(['user' => $admin('dimasadlanwiyanto@gmail.com'), 'cat' => $catDiving,
            'name'    => 'Fun Dive Experience — Mazu Divers',
            'price'   => 2200000,
            'contact' => 'Mazu Divers | 0851-0714-5488 | mazudivers.net',
            'quota'   => 6,
            'desc'    => 'Bagi Anda yang sudah bersertifikasi, nikmati wisata menyelam santai di beberapa titik penyelaman terbaik di Pulau Pramuka bersama Mazu Divers. Paket ini mencakup 2 kali dive dengan pemandu bersertifikasi internasional. Kesempatan sempurna untuk merasakan sensasi menyelam di spot terbaik Kepulauan Seribu.',
        ]);

        $create(['user' => $admin('dimasadlanwiyanto@gmail.com'), 'cat' => $catDiving,
            'name'    => 'Family Scuba Diving Package — Mazu Divers',
            'price'   => 10000000,
            'contact' => 'Mazu Divers | 0851-0714-5488 | mazudivers.net',
            'quota'   => 2, // per keluarga (maks 4 orang)
            'desc'    => 'Paket diving eksklusif untuk keluarga yang ingin merasakan petualangan bawah laut bersama di Pulau Pramuka. Instruktur Mazu Divers akan memberikan pelatihan dasar kepada seluruh anggota keluarga agar bisa menyelam dengan aman. Paket untuk maksimal 4 orang dalam satu keluarga.',
        ]);

        $create(['user' => $admin('dimasadlanwiyanto@gmail.com'), 'cat' => $catDiving,
            'name'    => 'Private Diving Tour — Mazu Divers',
            'price'   => 7000000,
            'contact' => 'Mazu Divers | 0851-0714-5488 | mazudivers.net',
            'quota'   => 4,
            'desc'    => 'Paket diving eksklusif private tour untuk Anda yang menginginkan pengalaman menyelam secara personal tanpa gangguan kelompok lain. Lokasi penyelaman disesuaikan dengan preferensi Anda di Kepulauan Seribu. Harga per orang, minimum 2 orang.',
        ]);

        $create(['user' => $admin('rafauli@dewisata.test'), 'cat' => $catDiving,
            'name'    => 'Diving Trip Pramuka 2D1N Private Trip — Rafauli Dive',
            'price'   => 3100000, // Data asli 3.100.000.000 diduga typo → dikoreksi 3.100.000
            'contact' => 'Triono | 0838-0752-7652 | rafauli.co.id',
            'quota'   => 5,
            'desc'    => 'Paket diving private trip 2 Hari 1 Malam ke Pulau Pramuka khusus penyelam bersertifikat. Termasuk: speedboat PP Marina–Pramuka, makan 4x, 5 kali log dive (tangki, weight belt), akomodasi 1 malam di pulau, boat diving 2 hari, dan pemandu dive. Tidak termasuk: sewa perlengkapan dive Rp 250.000/hari, tiket Ancol, dan tip guide.',
        ]);

        $create(['user' => $admin('rafauli@dewisata.test'), 'cat' => $catDiving,
            'name'    => 'Diving Trip Pramuka One Day Private Trip — Rafauli Dive',
            'price'   => 2200000,
            'contact' => 'Triono | 0838-0752-7652 | rafauli.co.id',
            'quota'   => 6,
            'desc'    => 'Paket one day diving trip ke Pulau Pramuka untuk penyelam bersertifikat. Termasuk: speedboat PP, makan siang dan snack, 2 kali log dive (tangki, weight belt), boat diving, panduan dive, dan tiket Ancol. Tidak termasuk: sewa perlengkapan dive Rp 250.000/hari dan tip guide.',
        ]);

        $create(['user' => $admin('hobbydive@dewisata.test'), 'cat' => $catDiving,
            'name'    => 'One Day Trip Diving — Hobby Dive',
            'price'   => 1750000,
            'contact' => 'Admin Hobby Dive | 0811-8213-31',
            'quota'   => 8,
            'desc'    => 'Paket one day diving trip ke Pulau Pramuka dari Marina Ancol. Termasuk: 2 kali log dive, makan siang + minuman + snack, speedboat PP Marina–Pramuka–Marina, tangki & weight belt, serta local guide/dive master. Tidak termasuk: transport ke Marina Ancol, pengeluaran pribadi, perlengkapan personal (masker, fin, regulator, BCD), dan tip guide.',
        ]);

        $create(['user' => $admin('dimasadlan08@gmail.com'), 'cat' => $catDiving,
            'name'    => 'Dive Trip Pramuka Bersertifikat 2D1N — Raja Nyelam',
            'price'   => 1750000,
            'contact' => 'Admin Raja Nyelam | 0812-9894-7986',
            'quota'   => 8,
            'desc'    => 'Paket diving 2 Hari 1 Malam di Pulau Pramuka khusus penyelam bersertifikat. Termasuk: tiket speedboat PP Marina–Pramuka, akomodasi 1 malam (AC, twin share), 2 kali dive trip lengkap dengan peralatan dan guide, makan 3x, transport lokal di pulau, kunjungan konservasi penyu, dan dokumentasi underwater & darat. Tidak termasuk: tiket Ancol dan pengeluaran pribadi.',
        ]);

        $create(['user' => $admin('dimasadlan08@gmail.com'), 'cat' => $catDiving,
            'name'    => 'Dive Trip Pramuka Non-Sertifikat 2D1N — Raja Nyelam',
            'price'   => 1950000,
            'contact' => 'Admin Raja Nyelam | 0812-9894-7986',
            'quota'   => 8,
            'desc'    => 'Paket diving 2 Hari 1 Malam di Pulau Pramuka untuk peserta yang belum memiliki sertifikat diving. Instruktur Raja Nyelam akan membimbing Anda dari nol hingga bisa merasakan sensasi menyelam di bawah laut Kepulauan Seribu. Termasuk sertifikat Discover Diving, akomodasi, makan 3x, dan dokumentasi. Tidak termasuk: tiket Ancol dan pengeluaran pribadi.',
        ]);

        $create(['user' => $admin('dimasadlan08@gmail.com'), 'cat' => $catDiving,
            'name'    => 'Snorkeling Trip Pramuka 2D1N — Raja Nyelam',
            'price'   => 900000,
            'contact' => 'Admin Raja Nyelam | 0812-9894-7986',
            'quota'   => 12,
            'desc'    => 'Paket wisata 2 Hari 1 Malam di Pulau Pramuka khusus snorkeling untuk non-diver. Jelajahi keindahan terumbu karang dan ikan-ikan tropis Kepulauan Seribu dengan peralatan snorkeling lengkap. Termasuk: speedboat PP, akomodasi 1 malam, makan 3x, kunjungan konservasi penyu, dan dokumentasi. Harga termurah untuk paket menginap di Pulau Pramuka!',
        ]);

        // ════════════════════════════════════════════════════════════════════
        // RENTAL (1 layanan)
        // ════════════════════════════════════════════════════════════════════

        $create(['user' => $admin('rentalsepeda@dewisata.test'), 'cat' => $catRental,
            'name'     => 'Rental Sepeda Listrik Z3 Harian',
            'price'    => 150000,
            'contact'  => 'Admin Rental Sepeda | 0857-7771-5368',
            'quota'    => 10,
            'pricing_type' => 'per_unit',
            'unit'     => 'Unit',
            'desc'     => 'Jelajahi seluruh sudut Pulau Pramuka dengan nyaman menggunakan Sepeda Listrik Z3! Tanpa polusi, tanpa keringat berlebih — cukup nikmati angin laut sambil berkeliling pulau dengan kecepatan santai. Tersedia untuk sewa harian, cocok untuk wisatawan yang ingin eksplorasi mandiri di Pulau Pramuka.',
        ]);

        $total = Service::count();
        $this->command->info("✅ ServiceSeeder selesai! Total layanan di database: {$total}");
    }
}
