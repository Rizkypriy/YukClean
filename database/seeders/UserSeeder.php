<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Ahmad Fajar',
                'email' => 'ahmad.fajar@email.com',
                'phone' => '+62 812-3456-7890',
                'address' => 'Jl. Sudirman No. 123, Jakarta Selatan',
                'member_level' => 'Gold',
                'total_orders' => 23,
                'role' => 'user',
                'is_active' => true, // <-- TAMBAHKAN ROLE
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@email.com',
                'phone' => '+62 813-9876-5432',
                'address' => 'Jl. Gatot Subroto No. 45, Jakarta Pusat',
                'member_level' => 'Regular',
                'total_orders' => 5,
                'role' => 'user', // <-- TAMBAHKAN ROLE
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Citra Dewi',
                'email' => 'citra.dewi@email.com',
                'phone' => '+62 856-2345-6789',
                'address' => 'Jl. Thamrin No. 67, Jakarta Pusat',
                'member_level' => 'Regular',
                'total_orders' => 2,
                'role' => 'user', // <-- TAMBAHKAN ROLE
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Dian Pratama',
                'email' => 'dian.pratama@email.com',
                'phone' => '+62 877-8901-2345',
                'address' => 'Jl. Rasuna Said No. 89, Jakarta Selatan',
                'member_level' => 'Platinum',
                'total_orders' => 45,
                'role' => 'user', // <-- TAMBAHKAN ROLE
                'password' => Hash::make('password123'),
            ],
        ];

        // Tambahkan user admin (opsional)
        $users[] = [
            'name' => 'Admin Yuk Clean',
            'email' => 'admin@yukclean.com',
            'phone' => '+62 811-2222-3333',
            'address' => 'Jl. Kebersihan No. 1, Jakarta Pusat',
            'member_level' => 'Platinum',
            'total_orders' => 0,
            'role' => 'admin', // <-- ROLE ADMIN
            'password' => Hash::make('admin123'),
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info('✅ UserSeeder: ' . count($users) . ' user berhasil ditambahkan!');
    }
}