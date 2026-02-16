<?php
// app/Http/Middleware/CleanerMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CleanerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('cleaner')->check()) {
            return redirect()->route('cleaner.login');
        }

        return $next($request);
    }
}