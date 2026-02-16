<?php
// app/Models/Cleaner.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;
use App\Models\CleanerTask;
use App\Models\CleanerPerformance;
class Cleaner extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'cleaners';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'gender',
        'avatar',
        'address',
        'latitude',
        'longitude',
        'radius_km',
        'status',
        'total_tasks',
        'rating',
        'satisfaction_rate',
        'active_days',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rating' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function tasks()
    {
        return $this->hasMany(CleanerTask::class);
    }

    public function performance()
    {
        return $this->hasMany(CleanerPerformance::class);
    }

    public function currentTask()
    {
        return $this->hasOne(CleanerTask::class)->whereIn('status', ['assigned', 'on_the_way', 'in_progress']);
    }

    /**
     * Accessors
     */
    public function getAvatarUrlAttribute()
    {
        return $this->avatar 
            ? asset('storage/' . $this->avatar) 
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=00bca4&color=fff';
    }

    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return substr($initials, 0, 2);
    }

    public function getFormattedRatingAttribute()
    {
        return number_format($this->rating, 1);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'available' => ['bg-green-100', 'text-green-600', 'Available'],
            'on_task' => ['bg-yellow-100', 'text-yellow-600', 'On Task'],
            'offline' => ['bg-gray-100', 'text-gray-600', 'Offline'],
            default => ['bg-gray-100', 'text-gray-600', $this->status],
        };
    }

    /**
     * Methods
     */
    public function isAvailable()
    {
        return $this->status === 'available';
    }

    public function updatePerformance()
    {
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        $performance = $this->performance()->firstOrCreate(
            ['month' => $month, 'year' => $year],
            [
                'tasks_completed' => 0,
                'active_days' => 0,
                'avg_rating' => 0,
                'satisfaction_rate' => 0,
            ]
        );

        $completedTasks = $this->tasks()
            ->where('status', 'completed')
            ->whereMonth('completed_at', $month)
            ->whereYear('completed_at', $year)
            ->count();

        $activeDays = $this->tasks()
            ->where('status', 'completed')
            ->whereMonth('completed_at', $month)
            ->whereYear('completed_at', $year)
            ->distinct('date')
            ->count('date');

        $avgRating = $this->rating;

        $performance->update([
            'tasks_completed' => $completedTasks,
            'active_days' => $activeDays,
            'avg_rating' => $avgRating,
            'satisfaction_rate' => $this->satisfaction_rate,
        ]);
    }
}