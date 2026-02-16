<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah service_id menjadi nullable
        DB::statement('ALTER TABLE orders MODIFY service_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        // Kembalikan ke NOT NULL
        DB::statement('ALTER TABLE orders MODIFY service_id BIGINT UNSIGNED NOT NULL');
    }
};