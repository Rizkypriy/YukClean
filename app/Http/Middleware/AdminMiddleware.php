<?php
// app/Http/Middleware/AdminMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah admin sudah login menggunakan guard 'admin'
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'Silakan login sebagai admin terlebih dahulu');
        }

        // OPTIONAL: Cek role admin jika ada (super_admin atau admin biasa)
        $admin = Auth::guard('admin')->user();
        
        // Jika Anda ingin membedakan akses berdasarkan role
        // Misalnya: hanya super_admin yang bisa akses某些 halaman
        // if ($admin->role !== 'super_admin' && $request->routeIs('admin.super.*')) {
        //     return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        // }

        return $next($request);
    }
}