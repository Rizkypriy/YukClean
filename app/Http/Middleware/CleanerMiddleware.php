<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CleanerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('cleaner')->check()) {
            return redirect()->route('cleaner.login')->with('error', 'Silakan login sebagai petugas terlebih dahulu');
        }

        return $next($request);
    }
}