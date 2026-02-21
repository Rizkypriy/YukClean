<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Tambah kolom cleaner_id (nullable karena order baru belum punya cleaner)
            $table->foreignId('cleaner_id')
                  ->nullable()
                  ->after('user_id') // Letakkan setelah user_id
                  ->constrained('cleaners') // Referensi ke tabel cleaners
                  ->nullOnDelete(); // Jika cleaner dihapus, set jadi null
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Hapus foreign key dulu baru hapus kolom
            $table->dropForeign(['cleaner_id']);
            $table->dropColumn('cleaner_id');
        });
    }
};