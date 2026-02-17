<?php
// database/migrations/[timestamp]_add_missing_columns_to_cleaner_tasks.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cleaner_tasks', function (Blueprint $table) {
            // Tambahkan kolom yang hilang
            if (!Schema::hasColumn('cleaner_tasks', 'service_name')) {
                $table->string('service_name')->nullable()->after('address');
            }
            
            if (!Schema::hasColumn('cleaner_tasks', 'service_type')) {
                $table->string('service_type')->default('regular')->after('service_name');
            }
            
            if (!Schema::hasColumn('cleaner_tasks', 'task_date')) {
                $table->date('task_date')->nullable()->after('service_type');
            }
            
            if (!Schema::hasColumn('cleaner_tasks', 'start_time')) {
                $table->time('start_time')->nullable()->after('task_date');
            }
            
            if (!Schema::hasColumn('cleaner_tasks', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
            
            if (!Schema::hasColumn('cleaner_tasks', 'status')) {
                $table->enum('status', ['available', 'assigned', 'on_the_way', 'in_progress', 'completed', 'cancelled'])
                    ->default('available')->after('end_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cleaner_tasks', function (Blueprint $table) {
            $table->dropColumn([
                'service_name',
                'service_type',
                'task_date',
                'start_time',
                'end_time',
                'status'
            ]);
        });
    }
};