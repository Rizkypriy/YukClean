<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberLevelMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $level)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $allowedLevels = explode('|', $level);
        
        if (!in_array($user->member_level, $allowedLevels)) {
            return redirect()->route('home')
                ->with('error', 'Fitur ini hanya untuk member ' . implode(' atau ', $allowedLevels));
        }

        return $next($request);
    }
}