<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Cara 1: Menggunakan statement
            DB::statement("ALTER TABLE orders MODIFY order_date DATE NOT NULL DEFAULT '2024-01-01'");
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            DB::statement("ALTER TABLE orders MODIFY order_date DATE NOT NULL");
        });
    }
};