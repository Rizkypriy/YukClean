<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Memulai proses seeding database...');
        $this->command->newLine();

        // Nonaktifkan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Truncate tables
        DB::table('users')->truncate();
        DB::table('services')->truncate();
        DB::table('promos')->truncate();
        DB::table('bundles')->truncate();
        DB::table('orders')->truncate();

        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Jalankan seeder
        $this->call(UserSeeder::class);
        $this->command->newLine();
        
        $this->call(ServiceSeeder::class);
        $this->command->newLine();
        
        $this->call(PromoSeeder::class);
        $this->command->newLine();
        
        $this->call(BundleSeeder::class);
        $this->command->newLine();

        $this->command->info('✨ Semua seeder berhasil dijalankan!');
    }
}