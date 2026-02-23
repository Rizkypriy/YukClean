<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class OrderLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $orderId;
    public $latitude;
    public $longitude;
    public $timestamp;

    public function __construct($orderId, $latitude, $longitude)
    {
        $this->orderId = $orderId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->timestamp = now();
    }

    // Broadcast ke channel privat yang spesifik untuk order ini
    public function broadcastOn(): array
    {
        return [
            new Channel('order.' . $this->orderId),
        ];
    }
}