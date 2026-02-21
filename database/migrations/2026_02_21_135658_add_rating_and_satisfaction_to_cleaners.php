<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('cleaners', function (Blueprint $table) {
        // Cek jika kolom rating BELUM ada, maka buat baru
        if (!Schema::hasColumn('cleaners', 'rating')) {
            $table->decimal('rating', 3, 2)->default(0)->after('status');
        }
        
        // Cek jika kolom satisfaction_rate BELUM ada, maka buat baru
        if (!Schema::hasColumn('cleaners', 'satisfaction_rate')) {
            $table->integer('satisfaction_rate')->default(0)->after('rating');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cleaners', function (Blueprint $table) {
            //
        });
    }
};
