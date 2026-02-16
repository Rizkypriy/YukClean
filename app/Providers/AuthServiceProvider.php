<?php

namespace App\Providers;

use App\Models\Order;
use App\Policies\OrderPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Order::class => OrderPolicy::class,
        // Tambahkan policy lain di sini
        // User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Definisikan Gate di sini (opsional)
        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('member-gold', function ($user) {
            return $user->member_level === 'Gold' || $user->member_level === 'Platinum';
        });
    }
}