<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        // Cari wisatawan dummy, jika tidak ada, buat 1
        $wisatawan = User::where('role', 'wisatawan')->first();
        if (!$wisatawan) {
            $wisatawan = User::create([
                'name'              => 'Wisatawan Demo',
                'email'             => 'wisatawan.demo@dewisata.id',
                'password'          => bcrypt('password123'),
                'role'              => 'wisatawan',
                'email_verified_at' => now(),
            ]);
        }

        $services = Service::where('is_active', true)->where('is_approved', true)->get();
        
        if ($services->isEmpty()) {
            $this->command->warn('Tidak ada layanan aktif, skip BookingSeeder.');
            return;
        }

        $statuses = [
            Booking::STATUS_PENDING,
            Booking::STATUS_CONFIRMED,
            Booking::STATUS_COMPLETED,
            Booking::STATUS_CANCELLED,
            Booking::STATUS_REJECTED,
        ];

        // Buat 100 booking secara acak dalam 6 bulan terakhir
        $bookingCount = 0;

        for ($i = 0; $i < 100; $i++) {
            $service = $services->random();
            $status  = $statuses[array_rand($statuses)];
            
            // Random date between 6 months ago and today
            $createdAt = Carbon::now()->subDays(rand(0, 180));
            $bookingDate = (clone $createdAt)->addDays(rand(1, 14));
            
            $pax = rand(1, 5);
            $totalPrice = $pax * $service->price;

            $bookingData = [
                // Gunakan Str::random untuk menghindari duplicate constraint jika generateCode bertabrakan sepeser mili detik
                'booking_code'    => 'DW-DUMMY-' . Carbon::now()->format('His') . '-' . Str::random(4),
                'user_id'         => $wisatawan->id,
                'service_id'      => $service->id,
                'booking_date'    => $bookingDate->toDateString(),
                'pax'             => $pax,
                'total_price'     => $totalPrice,
                'status'          => $status,
                'booking_details' => [
                    'contact_name'  => $wisatawan->name,
                    'contact_phone' => '08123456789',
                    'special_request'=> 'Generated via Seeder',
                ],
                // Manipulasi created_at untuk keperluan chart
                'created_at'      => $createdAt,
                'updated_at'      => $createdAt,
            ];

            if ($status === Booking::STATUS_COMPLETED || $status === Booking::STATUS_CONFIRMED) {
                $bookingData['payment_proof'] = 'dummy/proof.jpg';
                $bookingData['payment_confirmed_at'] = (clone $createdAt)->addHours(rand(1, 24));
                $bookingData['payment_confirmed_by'] = User::where('role', 'super_admin')->first()?->id;
            }

            Booking::create($bookingData);
            $bookingCount++;
        }

        $this->command->info("✅ Berhasil generate {$bookingCount} bookings dalam 6 bulan terakhir.");
    }
}
