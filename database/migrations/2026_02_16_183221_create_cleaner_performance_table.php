<?php
// database/migrations/[timestamp]_create_cleaner_performance_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cleaner_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cleaner_id')->constrained()->onDelete('cascade');
            $table->integer('month');
            $table->integer('year');
            $table->integer('tasks_completed')->default(0);
            $table->integer('active_days')->default(0);
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->integer('satisfaction_rate')->default(0);
            $table->timestamps();
            
            $table->unique(['cleaner_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cleaner_performance');
    }
};