<?php
// database/migrations/2026_02_21_xxxxxx_add_missing_columns_final.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 🔥 CEK KOLOM DI TABEL ORDERS
        Schema::table('orders', function (Blueprint $table) {
            // cancellation_reason (mungkin sudah ada)
            if (!Schema::hasColumn('orders', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('notes');
            }
            
            // cleaner_id (sudah ada dari migration sebelumnya)
            // TIDAK PERLU DITAMBAH LAGI
            
            // rating (sudah ada)
            // TIDAK PERLU DITAMBAH LAGI
            
            // review (sudah ada)
            // TIDAK PERLU DITAMBAH LAGI
        });

        // 🔥 CEK KOLOM DI TABEL CLEANERS
        Schema::table('cleaners', function (Blueprint $table) {
            // rating (sudah ada dari migration sebelumnya)
            // TIDAK PERLU DITAMBAH LAGI
            
            // satisfaction_rate (mungkin belum ada)
            if (!Schema::hasColumn('cleaners', 'satisfaction_rate')) {
                $table->integer('satisfaction_rate')->default(0)->after('rating');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'cancellation_reason')) {
                $table->dropColumn('cancellation_reason');
            }
        });

        Schema::table('cleaners', function (Blueprint $table) {
            if (Schema::hasColumn('cleaners', 'satisfaction_rate')) {
                $table->dropColumn('satisfaction_rate');
            }
        });
    }
};