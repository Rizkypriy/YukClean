{{-- resources/views/cleaner/auth/register.blade.php --}}
@extends('cleaner.layouts.app')

@section('title', 'Daftar Petugas')

@section('content')
<div class="min-h-screen bg-white p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-green-600">Daftar<br>Yuk Clean Petugas</h1>
        <p class="text-gray-600 mt-2">Buat akun baru untuk mulai bekerja</p>
    </div>

    {{-- Tampilkan error validasi --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('cleaner.register.submit') }}">
        @csrf
        
        {{-- Nama Lengkap --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-2">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" 
                class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-300 {{ $errors->has('name') ? 'border-red-500' : 'border-gray-200' }}"
                placeholder="Masukkan nama lengkap" required>
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" 
                class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-300 {{ $errors->has('email') ? 'border-red-500' : 'border-gray-200' }}"
                placeholder="petugas@email.com" required>
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nomor Telepon --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-2">Nomor Telepon</label>
            <input type="tel" name="phone" value="{{ old('phone') }}" 
                class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-300 {{ $errors->has('phone') ? 'border-red-500' : 'border-gray-200' }}"
                placeholder="08xxxxxxxxxx" required>
            @error('phone')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Jenis Kelamin --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-2">Jenis Kelamin</label>
            <select name="gender" class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-300 {{ $errors->has('gender') ? 'border-red-500' : 'border-gray-200' }}" required>
                <option value="">Pilih jenis kelamin</option>
                <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
            </select>
            @error('gender')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-2">Password</label>
            <input type="password" name="password" 
                class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-300 {{ $errors->has('password') ? 'border-red-500' : 'border-gray-200' }}"
                placeholder="Minimal 8 karakter" required>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-medium mb-2">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" 
                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-300"
                placeholder="Ulangi password" required>
        </div>

        {{-- Button Register --}}
        <button type="submit" 
            class="w-full bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700 transition">
            Daftar
        </button>

        {{-- Link Login --}}
        <p class="text-center mt-4 text-gray-600">
            Sudah punya akun? 
            <a href="{{ route('cleaner.login') }}" class="text-green-600 font-medium hover:underline">Masuk</a>
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