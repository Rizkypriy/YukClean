<?php
// database/seeders/AdminSeeder.php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Super Admin',
                'email' => 'super.admin@yukclean.com',
                'password' => Hash::make('admin123'),
                'phone' => '081111111111',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Admin Yuk Clean',
                'email' => 'admin@yukclean.com',
                'password' => Hash::make('admin123'),
                'phone' => '082222222222',
                'role' => 'admin',
            ],
        ];

        foreach ($admins as $admin) {
            Admin::create($admin);
        }

        $this->command->info('✅ AdminSeeder: ' . count($admins) . ' admin berhasil ditambahkan!');
    }
}