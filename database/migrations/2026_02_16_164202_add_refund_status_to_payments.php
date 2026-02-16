<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum payment_status untuk menambah 'refunded'
        DB::statement("ALTER TABLE payments MODIFY payment_status ENUM('pending', 'paid', 'failed', 'expired', 'refunded') DEFAULT 'pending'");
        
        // Tambah kolom refunded_at
        Schema::table('payments', function (Blueprint $table) {
            $table->timestamp('refunded_at')->nullable()->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('refunded_at');
        });
        DB::statement("ALTER TABLE payments MODIFY payment_status ENUM('pending', 'paid', 'failed', 'expired') DEFAULT 'pending'");
    }
};