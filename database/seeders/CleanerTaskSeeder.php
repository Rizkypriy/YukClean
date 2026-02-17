<?php
// database/seeders/CleanerTaskSeeder.php

namespace Database\Seeders;

use App\Models\CleanerTask;
use App\Models\Order;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CleanerTaskSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::where('status', 'confirmed')->get();
        
        foreach ($orders as $order) {
            CleanerTask::create([
                'order_id' => $order->id,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'address' => $order->address,
                'service_name' => $order->service->name ?? 'Paket Bundling',
                'service_type' => $order->service->type ?? 'regular',
                'task_date' => $order->order_date,
                'start_time' => $order->start_time,
                'end_time' => $order->end_time,
                'status' => 'available',
            ]);
        }

        $this->command->info('✅ CleanerTaskSeeder: ' . count($orders) . ' tugas berhasil dibuat!');
    }
}