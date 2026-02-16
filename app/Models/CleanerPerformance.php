<?php
// app/Models/CleanerPerformance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CleanerPerformance extends Model
{
    protected $table = 'cleaner_performance';

    protected $fillable = [
        'cleaner_id',
        'month',
        'year',
        'tasks_completed',
        'active_days',
        'avg_rating',
        'satisfaction_rate',
    ];

    protected $casts = [
        'avg_rating' => 'decimal:2',
    ];

    public function cleaner(): BelongsTo
    {
        return $this->belongsTo(Cleaner::class);
    }

    public function getMonthNameAttribute()
    {
        return \Carbon\Carbon::create()->month($this->month)->format('F');
    }
}