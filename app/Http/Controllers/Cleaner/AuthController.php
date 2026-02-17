<?php
// app/Http/Controllers/Cleaner/AuthController.php

namespace App\Http\Controllers\Cleaner;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show login form for cleaner
     */
    public function showLoginForm()
    {
        // Debug log
        Log::info('========== CLEANER LOGIN PAGE ==========');
        Log::info('Session ID: ' . session()->getId());
        Log::info('CSRF Token: ' . csrf_token());
        
        return view('cleaner.auth.login');
    }

    /**
     * Handle login request for cleaner
     */
    public function login(Request $request)
    {
        // Debug log
        Log::info('========== CLEANER LOGIN ATTEMPT ==========');
        Log::info('Email: ' . $request->email);
        
        // Validasi input
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ], [
            'email.required'    => 'Email harus diisi',
            'email.email'       => 'Format email tidak valid',
            'password.required' => 'Password harus diisi'
        ]);

        // Coba login dengan guard cleaner
        if (Auth::guard('cleaner')->attempt($credentials, $request->filled('remember'))) {
            // Regenerasi session
            $request->session()->regenerate();
            
            // Debug log sukses
            Log::info('LOGIN SUCCESS for: ' . $request->email);
            
            /** @var Cleaner $cleaner */
            $cleaner = Auth::guard('cleaner')->user();
            
            // Update status menjadi available
            $cleaner->update(['status' => 'available']);
            
            // Dapatkan nama cleaner
            $cleanerName = $cleaner->name;
            
            // Redirect ke dashboard cleaner
            return redirect()->intended(route('cleaner.dashboard'))
                ->with('success', "Selamat datang kembali, $cleanerName! 👋");
        }

        // Debug log gagal
        Log::info('LOGIN FAILED for: ' . $request->email);
        
        // Jika login gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Show register form for cleaner
     */
    public function showRegisterForm()
    {
        return view('cleaner.auth.register');
    }

    /**
     * Handle register request for cleaner
     */
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:cleaners',
            'password' => 'required|string|min:8|confirmed',
            'phone'    => 'required|string|max:20',
            'gender'   => 'required|in:Laki-laki,Perempuan',
        ], [
            'name.required'     => 'Nama lengkap harus diisi',
            'email.required'    => 'Email harus diisi',
            'email.email'       => 'Format email tidak valid',
            'email.unique'      => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min'      => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'phone.required'    => 'Nomor telepon harus diisi',
            'gender.required'   => 'Jenis kelamin harus dipilih',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Buat cleaner baru
        $cleaner = Cleaner::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'gender'   => $request->gender,
            'status'   => 'offline',
            'radius_km' => 5, // Default radius
            'total_tasks' => 0,
            'rating' => 0,
            'satisfaction_rate' => 0,
            'active_days' => 0,
        ]);

        // Login otomatis setelah register
        Auth::guard('cleaner')->login($cleaner);

        // Redirect ke dashboard cleaner
        return redirect()->route('cleaner.dashboard')
            ->with('success', 'Registrasi berhasil! Selamat bekerja. 🎉');
    }

    /**
     * Handle logout for cleaner
     */
    public function logout(Request $request)
    {
        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();
        
        if ($cleaner) {
            // Update status menjadi offline
            $cleaner->update(['status' => 'offline']);
            
            Log::info('CLEANER LOGOUT: ' . $cleaner->email);
        }

        Auth::guard('cleaner')->logout();
        
        // Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login.landing')
            ->with('success', 'Anda telah berhasil logout. Sampai jumpa! 👋');
    }
}