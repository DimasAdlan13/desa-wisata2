<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Rating;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * WisatawanRatingSeeder
 *
 * Alur: User Wisatawan → Booking (status: completed) → Rating
 *
 * Dirancang khusus untuk layanan yang dipakai dalam
 * perhitungan SAW manual di skripsi (Skenario: Kedaton Homestay).
 *
 * Layanan yang diberi rating:
 *   HOMESTAY (untuk uji SAW & Precision@K):
 *   - Kedaton Homestay           → referensi SAW
 *   - Satu Putri Homestay        → kandidat #1 (harga mirip)
 *   - Royal Mermaid Homestay     → kandidat #2
 *   - Dolphin Homestay           → kandidat #3
 *   - Lagunda Homestay           → kandidat #4
 *   - Seribu Resort              → kandidat #5 (harga mahal → skor rendah)
 *   - Villa Delima               → kandidat #6
 *   - Dermaga Resort             → kandidat #7
 *
 *   DIVING (untuk skenario Precision@K ke-2):
 *   - Fun Dive Exp. Mazu Divers  → referensi SAW skenario 2
 *   - One Day Trip Diving        → kandidat diving
 *   - Dive Trip Bersertifikat    → kandidat diving
 *   - Dive Trip Non-Sertifikat   → kandidat diving
 */
class WisatawanRatingSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();

        // ── 1. Buat 3 User Wisatawan ──────────────────────────────────────
        $wisatawan = [
            User::firstOrCreate(['email' => 'budi.santoso@gmail.com'], [
                'name'     => 'Budi Santoso',
                'password' => Hash::make('password'),
                'phone'    => '081234567001',
                'role'     => User::ROLE_WISATAWAN,
                'province' => 'DKI Jakarta',
                'city'     => 'Jakarta Selatan',
            ]),
            User::firstOrCreate(['email' => 'sari.dewi@gmail.com'], [
                'name'     => 'Sari Dewi',
                'password' => Hash::make('password'),
                'phone'    => '081234567002',
                'role'     => User::ROLE_WISATAWAN,
                'province' => 'Jawa Barat',
                'city'     => 'Bandung',
            ]),
            User::firstOrCreate(['email' => 'andi.pratama@gmail.com'], [
                'name'     => 'Andi Pratama',
                'password' => Hash::make('password'),
                'phone'    => '081234567003',
                'role'     => User::ROLE_WISATAWAN,
                'province' => 'Banten',
                'city'     => 'Tangerang',
            ]),
        ];

        // ── 2. Data Layanan + Rating per Wisatawan ────────────────────────
        // Format: ['nama_service' => rating_dari_user]
        // Setiap wisatawan memberi rating berbeda → rata-rata lebih natural

        $ratingData = [
            // ── HOMESTAY ─────────────────────────────────────────────────
            // Skenario SAW: Referensi = Kedaton Homestay (Rp 400.000)
            'Kedaton Homestay'           => [4, 5, 4],   // avg: 4.3
            'Satu Putri Homestay'        => [5, 4, 5],   // avg: 4.7 ← harga sama, skor SAW tertinggi
            'Royal Mermaid Homestay'     => [4, 4, 5],   // avg: 4.3
            'Dolphin Homestay'           => [4, 4, 3],   // avg: 3.7
            'Lagunda Homestay'           => [3, 4, 4],   // avg: 3.7
            'Seribu Resort'              => [5, 5, 4],   // avg: 4.7 ← rating tinggi tapi harga mahal → skor SAW rendah (nilai tambah untuk laporan!)
            'Villa Delima Pulau Pramuka' => [4, 3, 4],   // avg: 3.7
            'Dermaga Resort'             => [4, 5, 5],   // avg: 4.7
            'RR Homestay'                => [3, 4, 4],   // avg: 3.7
            'Agil Homestay'              => [4, 3, 4],   // avg: 3.7
            'Harris Homestay'            => [4, 4, 4],   // avg: 4.0

            // ── DIVING ───────────────────────────────────────────────────
            // Skenario Precision@K ke-2
            'Fun Dive Experience — Mazu Divers'                     => [5, 5, 4],   // avg: 4.7
            'One Day Trip Diving — Hobby Dive'                      => [4, 4, 4],   // avg: 4.0
            'Dive Trip Pramuka Bersertifikat 2D1N — Raja Nyelam'    => [4, 5, 4],   // avg: 4.3
            'Dive Trip Pramuka Non-Sertifikat 2D1N — Raja Nyelam'   => [4, 4, 5],   // avg: 4.3
            'Snorkeling Trip Pramuka 2D1N — Raja Nyelam'            => [5, 4, 4],   // avg: 4.3
        ];

        // ── 3. Buat Booking + Rating ──────────────────────────────────────
        $totalBooking = 0;
        $totalRating  = 0;
        $bookingDate  = Carbon::now()->subDays(30); // 30 hari lalu (sudah pasti completed)

        foreach ($ratingData as $serviceName => $ratings) {
            // Cari service berdasarkan nama (partial match)
            $service = Service::where('name', 'like', '%' . $serviceName . '%')->first();

            if (!$service) {
                $this->command->warn("⚠️  Service tidak ditemukan: {$serviceName} — skip.");
                continue;
            }

            // Setiap wisatawan membuat 1 booking untuk service ini
            foreach ($wisatawan as $index => $user) {
                $starRating = $ratings[$index];

                // Cek apakah booking sudah ada (hindari duplikat)
                $existingBooking = Booking::where('user_id', $user->id)
                    ->where('service_id', $service->id)
                    ->first();

                if (!$existingBooking) {
                    $booking = Booking::create([
                        'booking_code'           => 'DW-SEED-' . strtoupper(substr(md5($service->id . $user->id), 0, 8)),
                        'user_id'                => $user->id,
                        'service_id'             => $service->id,
                        'booking_date'           => $bookingDate->toDateString(),
                        'pax'                    => 2,
                        'total_price'            => $service->price * 2,
                        'status'                 => Booking::STATUS_COMPLETED,
                        'payment_proof'          => 'seed/dummy-proof.jpg',
                        'payment_confirmed_at'   => $bookingDate->copy()->subDays(2),
                        'payment_confirmed_by'   => $superAdmin?->id,
                        'booking_details'        => [
                            'contact_name'  => $user->name,
                            'contact_phone' => $user->phone,
                            'note'          => 'Generated via WisatawanRatingSeeder',
                        ],
                        'created_at' => $bookingDate->copy()->subDays(35),
                        'updated_at' => $bookingDate->copy()->subDays(35),
                    ]);

                    $totalBooking++;
                } else {
                    $booking = $existingBooking;
                }

                // Buat rating hanya jika belum ada
                if (!Rating::where('booking_id', $booking->id)->exists()) {
                    $reviews = [
                        "Pengalaman yang sangat menyenangkan! Sangat direkomendasikan.",
                        "Pelayanannya ramah dan tempatnya bersih. Puas!",
                        "Lokasi strategis dan fasilitas sesuai ekspektasi.",
                        "Cukup baik, tapi ada beberapa hal yang bisa ditingkatkan.",
                        "Luar biasa! Akan kembali lagi ke sini.",
                    ];

                    Rating::create([
                        'booking_id' => $booking->id,
                        'user_id'    => $user->id,
                        'service_id' => $service->id,
                        'rating'     => $starRating,
                        'review'     => $reviews[$starRating - 1],
                    ]);

                    $totalRating++;
                }
            }
        }

        $this->command->info("✅ Selesai! Dibuat: {$totalBooking} booking + {$totalRating} rating");
        $this->command->info("📊 Layanan yang diberi rating: " . count($ratingData));
        $this->command->newLine();
        $this->command->info("=== Rata-Rata Rating (untuk perhitungan SAW) ===");
        foreach ($ratingData as $name => $r) {
            $avg = round(array_sum($r) / count($r), 1);
            $stars = str_repeat('⭐', (int)$avg);
            $this->command->line("  {$stars} {$avg} — {$name}");
        }
    }
}
