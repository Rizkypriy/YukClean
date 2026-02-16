<?php
// database/migrations/[timestamp]_create_cleaner_tasks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cleaner_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cleaner_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->text('address');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('service_type');
            $table->enum('task_type', ['regular', 'deep_cleaning', 'bathroom', 'window']);
            $table->date('task_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('distance_km', 5, 2)->nullable();
            $table->enum('status', ['assigned', 'on_the_way', 'in_progress', 'completed', 'cancelled'])->default('assigned');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cleaner_tasks');
    }
};