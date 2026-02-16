<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'service_id',
        'bundle_id',
        'promo_id',
        'customer_name',
        'customer_phone',
        'address',
        'floor_count',
        'room_size',
        'special_conditions',
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
        'start_time' => 'string',
        'end_time' => 'string',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    /**
     * Accessors
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getFormattedDiscountAttribute(): string
    {
        return 'Rp ' . number_format($this->discount, 0, ',', '.');
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'pending' => ['bg-yellow-100', 'text-yellow-600', 'Menunggu Konfirmasi'],
            'confirmed' => ['bg-blue-100', 'text-blue-600', 'Dikonfirmasi'],
            'on_progress' => ['bg-green-100', 'text-green-600', 'Sedang Diproses'],
            'completed' => ['bg-gray-100', 'text-gray-600', 'Selesai'],
            'cancelled' => ['bg-red-100', 'text-red-600', 'Dibatalkan'],
            default => ['bg-gray-100', 'text-gray-600', $this->status],
        };
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'on_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Boot method untuk generate order number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        });
    }
}