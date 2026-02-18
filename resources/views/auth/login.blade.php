{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('title', 'Login User')

@section('content')
<div class="min-h-screen flex flex-col justify-center p-6 max-w-md mx-auto" style="background: linear-gradient(135deg, #f0fdf5 50%, #d3fcf2 100%);">
    {{-- Header --}}
    <div class="mb-8 text-center">
         <img src="{{ asset('img/logo.png') }}" alt="Yuk Clean Logo" 
         class="w-24 h-24 mx-auto mb-4">
        <h3 class="text-xl font-bold text-black mb-2.5">Masuk ke Yuk Clean</h3>
        <p class="text-black">Selamat datang kembali! Silakan masuk</p>
    </div>

    {{-- Form Login --}}
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md border border-[#cfcfcf]">
    <form method="POST" action="{{ route('user.login.submit') }}">
        @csrf
        
        {{-- Email --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" 
                class="w-full px-4 py-3 rounded-lg border bg-[#f3f3f5] {{ $errors->has('email') ? 'border-red-500' : 'border-gray-200' }} focus:outline-none focus:ring-2 focus:ring-[#cfcfcf]   "
                placeholder="contoh@email.com" required autofocus>
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-medium mb-2">Password</label>
            <input type="password" name="password" 
                class="w-full px-4 py-3 rounded-lg border bg-[#f3f3f5] {{ $errors->has('password') ? 'border-red-500' : 'border-gray-200' }} focus:outline-none focus:ring-2 focus:ring-[#cfcfcf]"
                placeholder="Masukkan password" required>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Button Login --}}
            <button type="submit" 
    class="w-full text-white py-3 rounded-lg font-medium transition-all duration-150"
    style="background: linear-gradient(135deg, #00bda2 0%, #00c85f 100%);"
    onmouseover="this.style.background='linear-gradient(135deg, #00a58c 0%, #00b04a 100%)'"
    onmouseout="this.style.background='linear-gradient(135deg, #00bda2 0%, #00c85f 100%)'">
    Login
</button>
    </form>
</div>
        {{-- Link Register --}}
        <p class="text-center mt-4 text-gray-600">
            Belum punya akun? 
            <a href="{{ route('user.register') }}" class="text-[#00bda2] font-medium hover:underline">
                Daftar
            </a>
        </p>
    </form>
</div>
@endsection