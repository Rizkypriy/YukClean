<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Redirect berdasarkan guard yang digunakan
                if ($guard === 'admin') {
                    return redirect()->route('admin.dashboard');
                } elseif ($guard === 'cleaner') {
                    return redirect()->route('cleaner.dashboard');
                } else {
                    // Default untuk user biasa
                    return redirect()->route('user.dashboard');
                }
            }
        }

        return $next($request);
    }
}