{{-- resources/views/user/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="pb-24" style="background-color: #e8fdf3;">
    {{-- Header dengan gradient --}}
    <div class="rounded-b-2xl p-6 text-white shadow-lg relative overflow-hidden"
         style="background: linear-gradient(135deg, #00bda2 0%, #00c85f 100%);">
        {{-- Header dengan Logo dan Judul --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                {{-- Logo --}}
                <img src="{{ asset('img/logo.png') }}" alt="Yuk Clean Logo" class="w-8 h-8">
                <h1 class="text-xl font-bold">Yuk Clean</h1>
            </div>
            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
        </div>

        {{-- Welcome Message --}}
        <div class="mb-2">
            <h2 class="text-xl font-semibold">Edit Profil</h2>
            <p class="text-sm opacity-90 mt-1">Perbarui informasi pribadi Anda</p>
        </div>
    </div>

    <div class="px-5 mt-6">
        {{-- Notifikasi Success --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Notifikasi Error --}}
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Edit Profil --}}
        <form method="POST" action="{{ route('user.profile.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Nama Lengkap Card --}}
            <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xl">👤</span>
                    <h3 class="font-semibold text-gray-800">Nama Lengkap</h3>
                </div>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                       class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-300 @error('name') border-red-500 @enderror"
                       placeholder="Masukkan nama lengkap">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email Card --}}
            <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xl">📧</span>
                    <h3 class="font-semibold text-gray-800">Email</h3>
                </div>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                       class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-300 @error('email') border-red-500 @enderror"
                       placeholder="Masukkan email">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nomor Telepon Card --}}
            <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xl">📞</span>
                    <h3 class="font-semibold text-gray-800">Nomor Telepon</h3>
                </div>
                <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" 
                       class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-300 @error('phone') border-red-500 @enderror"
                       placeholder="Masukkan nomor telepon">
                @error('phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat Card --}}
            <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xl">📍</span>
                    <h3 class="font-semibold text-gray-800">Alamat</h3>
                </div>
                <textarea name="address" rows="3" 
                          class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-300 @error('address') border-red-500 @enderror"
                          placeholder="Masukkan alamat lengkap">{{ old('address', $user->address ?? '') }}</textarea>
                @error('address')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kota Card --}}
            <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xl">🏙️</span>
                    <h3 class="font-semibold text-gray-800">Kota</h3>
                </div>
                <input type="text" name="city" value="{{ old('city', $user->city ?? '') }}" 
                       class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-300"
                       placeholder="Masukkan kota">
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex gap-3 pt-4">
                <a href="{{ route('user.profile.index') }}" 
                   class="flex-1 bg-gray-100 text-gray-700 py-4 rounded-lg font-medium text-center hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="flex-1 text-white py-4 rounded-lg font-medium transition-all duration-300 shadow-md hover:shadow-lg"
                        style="background: linear-gradient(135deg, #00bca4 0%, #00c954 100%);"
                        onmouseover="this.style.background='linear-gradient(135deg, #00a08b 0%, #00b045 100%)'"
                        onmouseout="this.style.background='linear-gradient(135deg, #00bca4 0%, #00c954 100%)'">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Animasi dan efek hover */
    button, a {
        transition: all 0.2s ease-in-out;
    }

    button:active, a:active {
        transform: scale(0.98);
    }
</style>
@endsection