<?php
// database/seeders/PromoSeeder.php

namespace Database\Seeders;

use App\Models\Promo;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PromoSeeder extends Seeder
{
    public function run(): void
    {
        $promos = [
    [
        'code' => 'NEWUSER20',
        'title' => 'Diskon 20% Pengguna Baru!!!',
        'description' => 'Nikmati potongan harga untuk pemesanan pertama Anda',
        'background_color' => 'linear-gradient(135deg, #ff8429 0%, #f7349a 100%)', // Oranye ke Pink
        'discount_type' => 'percentage',
        'discount_value' => 20,
        'min_transaction' => 50000,
        'valid_from' => Carbon::now(),
        'valid_until' => Carbon::now()->addMonths(3),
        'is_active' => true,
    ],
    [
        'code' => 'HEMAT30',
        'title' => 'Promo Bundling Rumah!',
        'description' => 'Hemat hingga 30% untuk paket lengkap kebersihan rumah',
        'background_color' => 'linear-gradient(135deg, #be79ff 0%, #645fff 100%)', // Ungu ke Biru
        'discount_type' => 'percentage',
        'discount_value' => 30,
        'min_transaction' => 150000,
        'valid_from' => Carbon::now(),
        'valid_until' => Carbon::now()->addMonths(2),
        'is_active' => true,
    ],
    [
        'code' => 'CLEAN10',
        'title' => 'Diskon 10% Semua Layanan',
        'description' => 'Potongan 10% untuk semua layanan kebersihan',
        'background_color' => 'linear-gradient(135deg, #00bda2 0%, #00c85f 100%)', // Hijau
        'discount_type' => 'percentage',
        'discount_value' => 10,
        'min_transaction' => 75000,
        'valid_from' => Carbon::now(),
        'valid_until' => Carbon::now()->addWeeks(2),
        'is_active' => true,
    ],
    [
        'code' => 'WEEKEND25',
        'title' => 'Weekend Special 25%',
        'description' => 'Nikmati diskon 25% khusus pemesanan di akhir pekan',
        'background_color' => 'linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%)', // Pink pastel
        'discount_type' => 'percentage',
        'discount_value' => 25,
        'min_transaction' => 100000,
        'valid_from' => Carbon::now(),
        'valid_until' => Carbon::now()->endOfMonth(),
        'is_active' => true,
    ],
];

        foreach ($promos as $promo) {
            Promo::create($promo);
        }

        $this->command->info('✅ PromoSeeder: ' . count($promos) . ' promo berhasil ditambahkan!');
    }
}