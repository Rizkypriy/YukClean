<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 
        'email', 
        'phone', 
        'address', 
        'avatar',
        'member_level', 
        'total_orders', 
        'password', 
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password', 
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */
    
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Order::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
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

    public function getMemberLevelBadgeAttribute(): array
    {
        return match($this->member_level) {
            'Regular' => ['bg-gray-100', 'text-gray-600', 'Regular'],
            'Gold' => ['bg-yellow-100', 'text-yellow-600', 'Gold'],
            'Platinum' => ['bg-purple-100', 'text-purple-600', 'Platinum'],
            default => ['bg-gray-100', 'text-gray-600', $this->member_level],
        };
    }

    public function getRoleBadgeAttribute(): array
    {
        return match($this->role) {
            'admin' => ['bg-red-100', 'text-red-600', 'Admin'],
            'user' => ['bg-blue-100', 'text-blue-600', 'User'],
            default => ['bg-gray-100', 'text-gray-600', $this->role],
        };
    }

    public function getFormattedPhoneAttribute(): string
    {
        return $this->phone;
    }

    /*
    |--------------------------------------------------------------------------
    | METHODS
    |--------------------------------------------------------------------------
    */
    
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isActive(): bool
    {
        return $this->is_active ?? true;
    }

    public function getTotalSpendingAttribute()
    {
        return $this->orders()
            ->where('status', 'completed')
            ->sum('total');
    }

    public function getFormattedTotalSpendingAttribute()
    {
        return 'Rp ' . number_format($this->total_spending, 0, ',', '.');
    }
}