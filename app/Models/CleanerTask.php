<?php
// app/Models/CleanerTask.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CleanerTask extends Model
{
    protected $table = 'cleaner_tasks';

    protected $fillable = [
        'cleaner_id',
        'order_id',
        'customer_name',
        'customer_phone',
        'address',
        'latitude',
        'longitude',
        'service_type',
        'task_type',
        'task_date',
        'start_time',
        'end_time',
        'distance_km',
        'status',
        'started_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'task_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'distance_km' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function cleaner(): BelongsTo
    {
        return $this->belongsTo(Cleaner::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Accessors
     */
    public function getFormattedDistanceAttribute()
    {
        return number_format($this->distance_km, 1) . ' km';
    }

    public function getFormattedDateAttribute()
    {
        return $this->task_date->format('d M Y');
    }

    public function getFormattedTimeAttribute()
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'assigned' => ['bg-blue-100', 'text-blue-600', 'Ditugaskan'],
            'on_the_way' => ['bg-yellow-100', 'text-yellow-600', 'Menuju Lokasi'],
            'in_progress' => ['bg-green-100', 'text-green-600', 'Sedang Bekerja'],
            'completed' => ['bg-gray-100', 'text-gray-600', 'Selesai'],
            'cancelled' => ['bg-red-100', 'text-red-600', 'Dibatalkan'],
            default => ['bg-gray-100', 'text-gray-600', $this->status],
        };
    }

    public function getTaskTypeIconAttribute()
    {
        return match($this->task_type) {
            'regular' => '🏠',
            'deep_cleaning' => '🧹',
            'bathroom' => '🚽',
            'window' => '🪟',
            default => '📋',
        };
    }
}