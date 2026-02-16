<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_number',
        'amount',
        'admin_fee',
        'discount',
        'total',
        'payment_method',
        'provider', 
        'payment_proof',
        'payment_status',
        'transaction_id',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'amount' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

   public function getStatusBadgeAttribute(): array
{
    return match($this->payment_status) {
        'pending' => ['bg-yellow-100', 'text-yellow-600', 'Menunggu Pembayaran'],
        'paid' => ['bg-green-100', 'text-green-600', 'Lunas'],
        'refunded' => ['bg-purple-100', 'text-purple-600', 'Telah Direfund'],
        'failed' => ['bg-red-100', 'text-red-600', 'Gagal'],
        'expired' => ['bg-gray-100', 'text-gray-600', 'Kadaluarsa'],
        default => ['bg-gray-100', 'text-gray-600', $this->payment_status],
    };
}
}