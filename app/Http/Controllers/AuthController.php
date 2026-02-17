<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show login form for user
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request for user
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Email harus diisi',
            'email.email'   => 'Format email tidak valid',
            'password.required' => 'Password harus diisi'
        ]);

        // Coba login dengan guard 'web' (default)
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Regenerasi session untuk keamanan
            $request->session()->regenerate();
            
            // Dapatkan nama user
            $userName = Auth::user()->name;
            
            // Redirect ke dashboard user
            return redirect()->intended(route('user.dashboard'))
                ->with('success', "Selamat datang kembali, $userName! 👋");
        }

        // Jika login gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Show register form for user
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle register request for user
     */
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'phone'    => 'required|string|max:20',
            'address'  => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required'     => 'Nama lengkap harus diisi',
            'email.required'    => 'Email harus diisi',
            'email.email'       => 'Format email tidak valid',
            'email.unique'      => 'Email sudah terdaftar',
            'phone.required'    => 'Nomor telepon harus diisi',
            'address.required'  => 'Alamat harus diisi',
            'password.required' => 'Password harus diisi',
            'password.min'      => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Buat user baru
        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'address'       => $request->address,
            'password'      => Hash::make($request->password),
            'member_level'  => 'Regular',
            'total_orders'  => 0,
            'role'          => 'user', // Default role
        ]);

        // Login otomatis setelah register
        Auth::login($user);

        // Redirect ke dashboard user
        return redirect()->route('user.dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang di Yuk Clean. 🎉');
    }

    /**
     * Handle logout for user
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        // Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login.landing')
            ->with('success', 'Anda telah berhasil logout. Sampai jumpa! 👋');
    }
}