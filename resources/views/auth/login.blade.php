{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('title', 'Login User')

@section('content')
<div class="min-h-screen bg-white flex flex-col justify-center p-6">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-green-600">Masuk ke<br>Yuk Clean</h1>
        <p class="text-gray-600 mt-2">Selamat datang kembali! Silakan masuk</p>
    </div>

    {{-- Form Login --}}
    <form method="POST" action="{{ url('/user/login') }}"> {{-- PERBAIKAN: dari 'login' ke 'user.login.submit' --}}
        @csrf
        
        {{-- Email --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" 
                class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-300 {{ $errors->has('email') ? 'border-red-500' : 'border-gray-200' }}"
                placeholder="contoh@email.com" required>
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-medium mb-2">Password</label>
            <input type="password" name="password" 
                class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-300 {{ $errors->has('password') ? 'border-red-500' : 'border-gray-200' }}"
                placeholder="********" required>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center mb-6">
            <input type="checkbox" name="remember" id="remember" class="mr-2">
            <label for="remember" class="text-sm text-gray-600">Ingat saya</label>
        </div>

        {{-- Button Login --}}
        <button type="submit" 
            class="w-full bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700 transition">
            Login
        </button>

        {{-- Link Register --}}
        <p class="text-center mt-4 text-gray-600">
            Belum punya akun? 
            <a href="{{ route('user.register') }}" class="text-green-600 font-medium hover:underline">Daftar</a>  {{-- PERBAIKAN: dari 'register' ke 'user.register' --}}
        </p>

        {{-- Link kembali ke landing page --}}
        <p class="text-center mt-2">
            <a href="{{ route('login.landing') }}" class="text-sm text-gray-500 hover:underline">
                ← Kembali ke pilihan role
            </a>
        </p>
    </form>
</div>
@endsection