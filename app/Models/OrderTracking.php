<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{
    use HasFactory;

    protected $table = 'order_tracking'; // sesuaikan dengan nama tabel

    protected $fillable = [
        'order_id',
        'cleaner_id',
        'latitude',
        'longitude'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function cleaner()
    {
        return $this->belongsTo(Cleaner::class);
    }
}