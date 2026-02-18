{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.app')

@section('title', 'Daftar User')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#f0fdf5] to-[#d3fcf2] flex flex-col justify-center items-center p-6">
     <a href="{{ route('user.login') }}" 
       class="absolute top-6 left-6 flex items-center text-gray-600 hover:text-green-600 transition duration-200 ">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
    </a>
    {{-- Header dengan Logo --}}
    <div class="mb-8 text-center pt-12">
        {{-- Logo --}}
        <img src="{{ asset('img/logo.png') }}" alt="Yuk Clean Logo" 
             class="w-20 h-20 mx-auto mb-3">
        
        <h3 class="text-2xl font-bold text-black mb-1">Buat Akun Baru</h3>
        <p class="text-gray-600">Mulai pengalaman kebersihan rumah Anda</p>
    </div>

    {{-- Alert Error --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 w-full max-w-md">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Register --}}
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md border border-[#cfcfcf]">
        
        {{-- Nama Lengkap --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-2">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" 
                class="w-full px-4 py-3 rounded-lg border bg-white {{ $errors->has('name') ? 'border-red-500' : 'border-gray-200' }} focus:outline-none focus:ring-2 focus:ring-[#cfcfcf]"
                placeholder="Masukkan nama lengkap" required autofocus>
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" 
                class="w-full px-4 py-3 rounded-lg border bg-white {{ $errors->has('email') ? 'border-red-500' : 'border-gray-200' }} focus:outline-none focus:ring-2 focus:ring-[#cfcfcf]"
                placeholder="contoh@email.com" required>
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nomor HP --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-2">Nomor HP</label>
            <input type="tel" name="phone" value="{{ old('phone') }}" 
                class="w-full px-4 py-3 rounded-lg border bg-white {{ $errors->has('phone') ? 'border-red-500' : 'border-gray-200' }} focus:outline-none focus:ring-2 focus:ring-[#cfcfcf]"
                placeholder="08xxxxxxxxxx" required>
            @error('phone')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>


        {{-- Password --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-2">Password</label>
            <input type="password" name="password" 
                class="w-full px-4 py-3 rounded-lg border bg-white {{ $errors->has('password') ? 'border-red-500' : 'border-gray-200' }} focus:outline-none focus:ring-2 focus:ring-[#cfcfcf]"
                placeholder="Minimal 8 karakter" required>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-medium mb-2">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" 
                class="w-full px-4 py-3 rounded-lg border bg-white border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#cfcfcf]"
                placeholder="Ulangi password" required>
        </div>
        {{-- Button Register dengan Gradient --}}
        <button type="submit" 
            class="w-full text-white py-3 rounded-lg font-medium transition-all duration-150"
            style="background: linear-gradient(135deg, #00bda2 0%, #00c85f 100%);"
            onmouseover="this.style.background='linear-gradient(135deg, #00a58c 0%, #00b04a 100%)'"
            onmouseout="this.style.background='linear-gradient(135deg, #00bda2 0%, #00c85f 100%)'">
            Daftar
        </button>
    </div>

        

        {{-- Link Login --}}
        <p class="text-center mt-4 text-gray-600">
            Sudah punya akun? 
            <a href="{{ route('user.register') }}" class="text-[#00bda2] font-medium hover:underline">
                Login
            </a>
        </p>
    </form>
</div>
@endsection