<?php
// database/seeders/CleanerSeeder.php

namespace Database\Seeders;

use App\Models\Cleaner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CleanerSeeder extends Seeder
{
    public function run(): void
    {
        $cleaners = [
            [
                'name' => 'Ummu Hanny',
                'email' => 'ummu.hanny@yukclean.com',
                'password' => Hash::make('password123'),
                'phone' => '081234567890',
                'gender' => 'Perempuan',
                'radius_km' => 5,
                'status' => 'available',
                'total_tasks' => 120,
                'rating' => 5.0,
                'satisfaction_rate' => 98,
                'active_days' => 87,
            ],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@yukclean.com',
                'password' => Hash::make('password123'),
                'phone' => '081234567891',
                'gender' => 'Laki-laki',
                'radius_km' => 7,
                'status' => 'on_task',
                'total_tasks' => 95,
                'rating' => 4.9,
                'satisfaction_rate' => 97,
                'active_days' => 70,
            ],
        ];

        foreach ($cleaners as $cleaner) {
            Cleaner::create($cleaner);
        }

        $this->command->info('✅ CleanerSeeder: ' . count($cleaners) . ' petugas berhasil ditambahkan!');
    }
}