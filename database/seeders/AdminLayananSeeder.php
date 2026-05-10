<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminLayananSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();

        $admins = [
            // ── 4 Akun Gmail Asli ────────────────────────────────────────────
            [
                'email'            => 'dimasadlanwiyanto@gmail.com',
                'name'             => 'Mazu Divers',
                'phone'            => '085107145488',
                'business_name'    => 'Mazu Divers',
                'business_address' => 'Pulau Pramuka, Kepulauan Seribu, Jakarta Utara',
                'business_description' => 'Penyedia layanan scuba diving profesional di Pulau Pramuka dengan instruktur bersertifikasi internasional.',
            ],
            [
                'email'            => 'dimasadlan08@gmail.com',
                'name'             => 'Admin Raja Nyelam',
                'phone'            => '081298947986',
                'business_name'    => 'Raja Nyelam Dive Center',
                'business_address' => 'Pulau Pramuka, Kepulauan Seribu, Jakarta Utara',
                'business_description' => 'Dive center spesialis paket diving dan snorkeling untuk pemula maupun berpengalaman di Kepulauan Seribu.',
            ],
            [
                'email'            => 'serenok321@gmail.com',
                'name'             => 'Ibu Rohada',
                'phone'            => '081572240499',
                'business_name'    => 'Dolphin & RR Homestay',
                'business_address' => 'Pulau Pramuka, Kepulauan Seribu, Jakarta Utara',
                'business_description' => 'Penginapan keluarga yang nyaman dengan suasana khas Pulau Pramuka.',
            ],
            [
                'email'            => 'romanbis29@gmail.com',
                'name'             => 'Mbo Puput',
                'phone'            => '081316925840',
                'business_name'    => 'Agil & Satu Putri Homestay',
                'business_address' => 'Pulau Pramuka, Kepulauan Seribu, Jakarta Utara',
                'business_description' => 'Homestay terpercaya milik warga lokal di Pulau Pramuka dengan harga terjangkau.',
            ],

            // ── Akun Dummy ────────────────────────────────────────────────────
            ['email' => 'pakuntung@dewisata.test',     'name' => 'Pak Untung',          'phone' => '081319551955', 'business_name' => 'Villa Delima',              'business_address' => 'Pulau Pramuka, Kepulauan Seribu', 'business_description' => 'Villa dan homestay nyaman di Pulau Pramuka.'],
            ['email' => 'mbomang@dewisata.test',       'name' => 'Mbo Mang',            'phone' => '085881865535', 'business_name' => '3 Dara Homestay',           'business_address' => 'Pulau Pramuka, Kepulauan Seribu', 'business_description' => 'Homestay keluarga ramah wisatawan di Pulau Pramuka.'],
            ['email' => 'omjoel@dewisata.test',        'name' => 'Om Joel',             'phone' => '081282098880', 'business_name' => "B'Joels Homestay",          'business_address' => 'Pulau Pramuka, Kepulauan Seribu', 'business_description' => 'Homestay santai dengan konsep kekeluargaan di Pulau Pramuka.'],
            ['email' => 'mbahayati@dewisata.test',     'name' => 'Mba Hayati',          'phone' => '085159951101', 'business_name' => 'Harris Homestay',           'business_address' => 'Pulau Pramuka, Kepulauan Seribu', 'business_description' => 'Penginapan bersih dan terjangkau di jantung Pulau Pramuka.'],
            ['email' => 'bundaelsa@dewisata.test',     'name' => 'Bunda Elsa',          'phone' => '087776983999', 'business_name' => 'Kedaton Homestay',          'business_address' => 'Pulau Pramuka, Kepulauan Seribu', 'business_description' => 'Homestay bersih dengan pelayanan hangat ala keluarga lokal Pulau Pramuka.'],
            ['email' => 'mbopipit@dewisata.test',      'name' => 'Mbo Pipit',           'phone' => '085941172931', 'business_name' => 'Lagunda Homestay',          'business_address' => 'Pulau Pramuka, Kepulauan Seribu', 'business_description' => 'Nikmati keindahan alam Pulau Pramuka dari Lagunda Homestay yang asri.'],
            ['email' => 'waondoy@dewisata.test',       'name' => 'Wa Ondoy',            'phone' => '085219492880', 'business_name' => 'Nuraini Homestay',          'business_address' => 'Pulau Pramuka, Kepulauan Seribu', 'business_description' => 'Homestay pilihan keluarga di Pulau Pramuka dengan fasilitas lengkap.'],
            ['email' => 'royalmermaid@dewisata.test',  'name' => 'Admin Royal Mermaid', 'phone' => '087889906755', 'business_name' => 'Royal Mermaid Homestay',    'business_address' => 'Pulau Pramuka, Kepulauan Seribu', 'business_description' => 'Homestay premium dengan konsep resort di Pulau Pramuka.'],
            ['email' => 'idatibi@dewisata.test',       'name' => 'Ida Tibi',            'phone' => '087886466312', 'business_name' => 'Sefty Homestay',            'business_address' => 'Pulau Pramuka, Kepulauan Seribu', 'business_description' => 'Penginapan nyaman dan bersih di kawasan wisata Pulau Pramuka.'],
            ['email' => 'villashafir@dewisata.test',   'name' => 'Admin Villa Shafir',  'phone' => '082123683228', 'business_name' => 'Shafir Villa Homestay',     'business_address' => 'Pulau Pramuka, Kepulauan Seribu', 'business_description' => 'Villa mewah dengan fasilitas lengkap untuk pengalaman wisata terbaik di Pulau Pramuka.'],
            ['email' => 'paksuryadi@dewisata.test',    'name' => 'Pak Suryadi',         'phone' => '085614705340', 'business_name' => 'Tiara Syair Homestay',      'business_address' => 'Pulau Pramuka, Kepulauan Seribu', 'business_description' => 'Homestay dengan sentuhan seni dan kenyamanan di Pulau Pramuka.'],
            ['email' => 'pakrusdi@dewisata.test',      'name' => 'Pak Rusdi',           'phone' => '081188364100', 'business_name' => 'Dermaga Resort',            'business_address' => 'Pulau Pramuka, Kepulauan Seribu', 'business_description' => 'Resort mewah dengan pemandangan langsung ke dermaga dan laut Kepulauan Seribu.'],
            ['email' => 'seribuResort@dewisata.test',  'name' => 'Admin Seribu Resort', 'phone' => '085715722121', 'business_name' => 'Seribu Resort',             'business_address' => 'Pulau Pramuka, Kepulauan Seribu', 'business_description' => 'Resort terkemuka di Kepulauan Seribu dengan fasilitas bintang di tengah alam tropis.'],
            ['email' => 'pakhasbullah@dewisata.test',  'name' => 'Pak Hasbullah',       'phone' => '081298667376', 'business_name' => 'Penginapan Apung Aquarius', 'business_address' => 'Pulau Pramuka, Kepulauan Seribu', 'business_description' => 'Penginapan unik di atas air dengan konsep rumah apung yang autentik.'],
            ['email' => 'sultanwisata@dewisata.test',  'name' => 'Admin Sultan Wisata', 'phone' => '081200000001', 'business_name' => 'Sultan Wisata',             'business_address' => 'Jakarta - Pulau Pramuka, Kepulauan Seribu', 'business_description' => 'Operator wisata terpercaya dengan paket lengkap 2 Hari 1 Malam ke Pulau Pramuka.'],
            ['email' => 'sadewa@dewisata.test',        'name' => 'Admin Sadewa Tour',   'phone' => '081200000002', 'business_name' => 'Sadewa Eco Tour',           'business_address' => 'Jakarta - Pulau Pramuka, Kepulauan Seribu', 'business_description' => 'Paket wisata ramah lingkungan dengan pengalaman alam autentik di Kepulauan Seribu.'],
            ['email' => 'rafauli@dewisata.test',       'name' => 'Triono Rafauli',      'phone' => '083807527652', 'business_name' => 'Rafauli Dive',              'business_address' => 'Pulau Pramuka, Kepulauan Seribu', 'business_description' => 'Spesialis paket diving private trip ke Pulau Pramuka dengan speedboat eksklusif.'],
            ['email' => 'hobbydive@dewisata.test',     'name' => 'Admin Hobby Dive',    'phone' => '081182133100', 'business_name' => 'Hobby Dive',                'business_address' => 'Pulau Pramuka, Kepulauan Seribu', 'business_description' => 'Penyedia paket one day diving trip ke Pulau Pramuka untuk penyelam bersertifikat.'],
            ['email' => 'rentalsepeda@dewisata.test',  'name' => 'Admin Rental Sepeda', 'phone' => '085777715368', 'business_name' => 'Rental Sepeda Listrik Z3', 'business_address' => 'Pulau Pramuka, Kepulauan Seribu', 'business_description' => 'Penyewaan sepeda listrik untuk berkeliling Pulau Pramuka dengan nyaman dan ramah lingkungan.'],
        ];

        foreach ($admins as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'                 => $data['name'],
                    'password'             => Hash::make('password'),
                    'phone'                => $data['phone'],
                    'role'                 => User::ROLE_ADMIN_LAYANAN,
                    'is_approved'          => true,
                    'approved_by'          => $superAdmin?->id,
                    'approved_at'          => now(),
                    'business_name'        => $data['business_name'],
                    'business_address'     => $data['business_address'],
                    'business_description' => $data['business_description'],
                ]
            );
        }

        $this->command->info('✅ Admin Layanan created: ' . count($admins) . ' accounts (4 real Gmail + ' . (count($admins) - 4) . ' dummy)');
    }
}
