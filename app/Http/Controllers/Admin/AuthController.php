<?php
// app/Http/Controllers/Admin/AuthController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Show login form for admin
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle login request for admin
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ], [
            'email.required'    => 'Email harus diisi',
            'email.email'       => 'Format email tidak valid',
            'password.required' => 'Password harus diisi'
        ]);

        // Coba login dengan guard admin
        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            // Regenerasi session untuk keamanan
            $request->session()->regenerate();
            
            /** @var Admin $admin */
            $admin = Auth::guard('admin')->user();
            
            // Log activity
            Log::info('ADMIN LOGIN SUCCESS', [
                'email' => $admin->email,
                'name' => $admin->name,
                'ip' => $request->ip()
            ]);
            
            // Dapatkan nama admin
            $adminName = $admin->name;
            
            // Redirect ke dashboard admin
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', "Selamat datang kembali, Admin $adminName! 👋");
        }

        // Log failed attempt
        Log::warning('ADMIN LOGIN FAILED', [
            'email' => $request->email,
            'ip' => $request->ip()
        ]);

        // Jika login gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Show forgot password form (optional)
     */
    public function showForgotForm()
    {
        return view('admin.auth.forgot');
    }

    /**
     * Handle logout for admin
     */
    public function logout(Request $request)
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();
        
        if ($admin) {
            Log::info('ADMIN LOGOUT', [
                'email' => $admin->email,
                'name' => $admin->name,
                'ip' => $request->ip()
            ]);
        }

        Auth::guard('admin')->logout();
        
        // Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login.landing')
            ->with('success', 'Anda telah berhasil logout. Sampai jumpa! 👋');
    }
}