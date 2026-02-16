<?php

namespace Database\Seeders;

use App\Models\Bundle;
use Illuminate\Database\Seeder;

class BundleSeeder extends Seeder
{
    public function run(): void
    {
        $bundles = [
            [
                'name' => 'Paket Hemat Mingguan',
                'description' => 'Dapatkan layanan kebersihan lengkap',
                'price' => 250000,
                'original_price' => 312500,
                'discount_percent' => 20,
                'badge_color' => 'bg-red-500',
                'is_active' => true,
            ],
            [
                'name' => 'Paket Deep Cleaning',
                'description' => 'Pembersihan mendalam seluruh rumah',
                'price' => 375000,
                'original_price' => 500000,
                'discount_percent' => 25,
                'badge_color' => 'bg-red-500',
                'is_active' => true,
            ],
            [
                'name' => 'Paket Keluarga',
                'description' => 'Untuk 4-5 ruangan',
                'price' => 450000,
                'original_price' => 529000,
                'discount_percent' => 15,
                'badge_color' => 'bg-red-500',
                'is_active' => true,
            ],
            [
                'name' => 'Paket Bulanan',
                'description' => 'Unlimited cleaning 30 hari',
                'price' => 850000,
                'original_price' => 1214000,
                'discount_percent' => 30,
                'badge_color' => 'bg-red-500',
                'is_active' => true,
            ],
        ];

        foreach ($bundles as $bundle) {
            Bundle::create($bundle);
        }

        $this->command->info('✅ BundleSeeder: ' . count($bundles) . ' paket bundling berhasil ditambahkan!');
    }
}