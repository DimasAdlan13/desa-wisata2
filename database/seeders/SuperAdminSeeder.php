<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'superadmin@dewisata.id'],
            [
                'name'        => 'Super Admin',
                'password'    => bcrypt('password123'),
                'role'        => User::ROLE_SUPER_ADMIN,
                'phone'       => '081234567890',
                'is_approved' => true,
            ]
        );

        $this->command->info('✅ Super Admin created: superadmin@dewisata.id / password123');
    }
}
