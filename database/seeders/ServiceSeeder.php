<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Cleaning Ruangan',
                'slug' => Str::slug('Cleaning Ruangan'),
                'description' => 'Bersihkan ruangan dengan sempurna',
                'price' => 75000,
                'icon_name' => 'ruangan',
                'color' => 'green',
                'duration' => 60,
                'is_popular' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Cleaning Kamar',
                'slug' => Str::slug('Cleaning Kamar'),
                'description' => 'Kamar tidur bersih dan nyaman',
                'price' => 65000,
                'icon_name' => 'kamar',
                'color' => 'green',
                'duration' => 45,
                'is_popular' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Cleaning Ruang Tamu',
                'slug' => Str::slug('Cleaning Ruang Tamu'),
                'description' => 'Ruang tamu rapi dan fresh',
                'price' => 80000,
                'icon_name' => 'ruang tamu',
                'color' => 'green',
                'duration' => 60,
                'is_popular' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Cleaning Toilet',
                'slug' => Str::slug('Cleaning Toilet'),
                'description' => 'Toilet higienis dan wangi',
                'price' => 50000,
                'icon_name' => 'toilet',
                'color' => 'green',
                'duration' => 30,
                'is_popular' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Cleaning Dapur',
                'slug' => Str::slug('Cleaning Dapur'),
                'description' => 'Dapur bersih bebas noda minyak',
                'price' => 85000,
                'icon_name' => 'dapur',
                'color' => 'green',
                'duration' => 75,
                'is_popular' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Cleaning Semua Ruangan',
                'slug' => Str::slug('Cleaning Semua Ruangan'),
                'description' => 'Pembersihan menyeluruh perlantai',
                'price' => 250000,
                'icon_name' => 'ruangan',
                'color' => 'green',
                'duration' => 180,
                'is_popular' => false,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        $this->command->info('✅ ServiceSeeder: ' . count($services) . ' layanan berhasil ditambahkan!');
    }
}