<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            
            // Foreign keys
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('bundle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('promo_id')->nullable()->constrained()->nullOnDelete();
            
            // Customer information
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('address');
            
            // Property details
            $table->integer('floor_count')->nullable()->comment('Jumlah lantai');
            $table->string('room_size')->nullable()->comment('Ukuran ruangan');
            $table->text('special_conditions')->nullable()->comment('Kondisi khusus');
            
            // Booking schedule
            $table->date('order_date');
            $table->time('start_time');
            $table->time('end_time');
            
            // Pricing
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            
            // Status
            $table->enum('status', [
                'pending', 
                'confirmed', 
                'on_progress', 
                'completed', 
                'cancelled'
            ])->default('pending');
            
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('order_number');
            $table->index('user_id');
            $table->index('status');
            $table->index('order_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};