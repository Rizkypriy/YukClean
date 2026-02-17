{{-- resources/views/auth/landing.blade.php --}}
@extends('layouts.app')

@section('title', 'Pilih Role')

@section('content')
<div class="min-h-screen bg-white flex flex-col justify-center p-6">
    {{-- Header dengan teks hitam --}}
    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-black mb-2">Yuk Clean</h1> {{-- Diubah dari text-green-600 ke text-black --}}
        <p class="text-gray-600">Pilih role untuk melanjutkan</p>
    </div>

    {{-- Pilihan Role --}}
    <div class="space-y-4 max-w-md mx-auto w-full">
        {{-- Login sebagai User --}}
        <a href="{{ route('user.login') }}"  
           class="block w-full bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold">Login sebagai User</h2>
                    <p class="text-sm opacity-90">Untuk pelanggan yang ingin memesan layanan</p>
                </div>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>

        {{-- Login sebagai Cleaner --}}
        <a href="{{ route('cleaner.login') }}"  
           class="block w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold">Login sebagai Cleaner</h2>
                    <p class="text-sm opacity-90">Untuk petugas kebersihan</p>
                </div>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>

        {{-- Login sebagai Admin --}}
        <a href="{{ route('admin.login') }}"  
           class="block w-full bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold">Login sebagai Admin</h2>
                    <p class="text-sm opacity-90">Untuk administrator sistem</p>
                </div>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>
    </div>

    {{-- Footer dengan teks hitam --}}
    <p class="text-center text-sm text-black mt-8"> {{-- Diubah dari text-gray-500 ke text-black --}}
        &copy; {{ date('Y') }} Yuk Clean. All rights reserved.
    </p>
</div>
@endsection