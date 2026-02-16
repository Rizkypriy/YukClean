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
                'title' => 'Diskon 20% Pengguna Baru',
                'description' => 'Nikmati potongan harga untuk pemesanan pertama Anda',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'min_purchase' => 50000,
                'start_date' => Carbon::now(), // <-- GANTI valid_from → start_date
                'end_date' => Carbon::now()->addMonths(3), // <-- GANTI valid_until → end_date
                'is_active' => true,
            ],
            [
                'code' => 'GRATISADMIN',
                'title' => 'Gratis Biaya Admin',
                'description' => 'Tanpa biaya tambahan untuk semua transaksi',
                'discount_type' => 'fixed',
                'discount_value' => 5000,
                'min_purchase' => 0,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(1),
                'is_active' => true,
            ],
            [
                'code' => 'HEMAT30',
                'title' => 'Promo Bundling Rumah!',
                'description' => 'Hemat hingga 30% untuk paket lengkap kebersihan rumah',
                'discount_type' => 'percentage',
                'discount_value' => 30,
                'min_purchase' => 150000,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(2),
                'is_active' => true,
            ],
            [
                'code' => 'CLEAN10',
                'title' => 'Diskon 10% Semua Layanan',
                'description' => 'Potongan 10% untuk semua layanan kebersihan',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'min_purchase' => 75000,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addWeeks(2),
                'is_active' => true,
            ],
            [
                'code' => 'WEEKEND25',
                'title' => 'Weekend Special 25%',
                'description' => 'Nikmati diskon 25% khusus pemesanan di akhir pekan',
                'discount_type' => 'percentage',
                'discount_value' => 25,
                'min_purchase' => 100000,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->endOfMonth(),
                'is_active' => true,
            ],
        ];

        foreach ($promos as $promo) {
            Promo::create($promo);
        }

        $this->command->info('✅ PromoSeeder: ' . count($promos) . ' promo berhasil ditambahkan!');
    }
}