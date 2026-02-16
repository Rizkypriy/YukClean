<?php
// app/Http/Controllers/Cleaner/AuthController.php

namespace App\Http\Controllers\Cleaner;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('cleaner.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::guard('cleaner')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // TYPE HINT - BERI TAHU IDE BAHWA INI ADALAH MODEL CLEANER
            /** @var Cleaner $cleaner */
            $cleaner = Auth::guard('cleaner')->user();
            
            // Update status to available - SEKARANG DIKENALI IDE
            $cleaner->update(['status' => 'available']);
            
            return redirect()->intended(route('cleaner.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('cleaner.auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:cleaners',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:Laki-laki,Perempuan',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $cleaner = Cleaner::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'gender' => $request->gender,
            'status' => 'offline',
        ]);

        Auth::guard('cleaner')->login($cleaner);

        return redirect()->route('cleaner.dashboard')
            ->with('success', 'Registrasi berhasil! Selamat bekerja.');
    }

    public function logout(Request $request)
    {
        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();
        
        if ($cleaner) {
            $cleaner->update(['status' => 'offline']);
        }

        Auth::guard('cleaner')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('cleaner.login');
    }
}