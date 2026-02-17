<?php
// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'cleaner_id', // <-- TAMBAHKAN
        'service_id',
        'bundle_id',
        'promo_id',
        'customer_name',
        'customer_phone',
        'address',
        'order_date',
        'start_time',
        'end_time',
        'subtotal',
        'discount',
        'total',
        'status',
        'notes',
        'cancellation_reason',
    ];

    protected $casts = [
        'order_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cleaner(): BelongsTo
    {
        return $this->belongsTo(Cleaner::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class);
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    public function cleanerTask()
    {
        return $this->hasOne(CleanerTask::class);
    }

    /**
     * Accessors
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending'      => ['bg-yellow-100', 'text-yellow-600', 'Menunggu'],
            'confirmed'    => ['bg-blue-100', 'text-blue-600', 'Dikonfirmasi'],
            'on_progress'  => ['bg-green-100', 'text-green-600', 'Diproses'],
            'completed'    => ['bg-gray-100', 'text-gray-600', 'Selesai'],
            'cancelled'    => ['bg-red-100', 'text-red-600', 'Dibatalkan'],
            default        => ['bg-gray-100', 'text-gray-600', $this->status],
        };
    }

    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }
}