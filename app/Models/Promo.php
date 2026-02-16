<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Promo extends Model
{
    protected $fillable = [
        'code',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'min_purchase',      // <-- Sesuai database (bukan min_transaction)
        'start_date',         // <-- Sesuai database (bukan valid_from)
        'end_date',           // <-- Sesuai database (bukan valid_until)
        'background_color',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
    ];

    /**
     * Scope untuk promo yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    /**
     * Cek apakah promo valid
     */
    public function isValid()
    {
        return $this->is_active && 
               Carbon::now()->between($this->start_date, $this->end_date);
    }

    /**
     * Hitung diskon berdasarkan subtotal
     */
    public function calculateDiscount($subtotal)
    {
        if ($subtotal < $this->min_purchase) {  // <-- Sesuai database
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            return $subtotal * $this->discount_value / 100;
        }

        return min($this->discount_value, $subtotal);
    }

    /**
     * Format discount untuk display
     */
    public function getFormattedDiscountAttribute()
    {
        if ($this->discount_type === 'percentage') {
            return $this->discount_value . '%';
        }
        return 'Rp ' . number_format($this->discount_value, 0, ',', '.');
    }

    /**
     * Format minimal transaksi
     */
    public function getFormattedMinPurchaseAttribute()  // <-- Sesuai database
    {
        return 'Rp ' . number_format($this->min_purchase, 0, ',', '.');
    }
}