<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Semua user bisa lihat daftar order mereka
    }

    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->user_id;
    }

    public function create(User $user): bool
    {
        return true; // Semua user bisa membuat order
    }

    public function update(User $user, Order $order): bool
    {
        return $user->id === $order->user_id && 
               in_array($order->status, ['pending', 'confirmed']);
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->id === $order->user_id && 
               $order->status === 'pending';
    }

    public function cancel(User $user, Order $order): bool
    {
        return $user->id === $order->user_id && 
               in_array($order->status, ['pending', 'confirmed']);
    }
}