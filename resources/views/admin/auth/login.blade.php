{{-- resources/views/admin/auth/login.blade.php --}}
@extends('layouts.app')

@section('title', 'Login Admin')

@section('content')
<div class="min-h-screen bg-white flex flex-col justify-center p-6">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-purple-600">Login Admin</h1>
        <p class="text-gray-600 mt-2">Masuk ke dashboard administrator</p>
    </div>

    <form method="POST" action="{{ route('login.admin.submit') }}" class="max-w-md mx-auto w-full">
        @csrf
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" 
                class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-purple-300 {{ $errors->has('email') ? 'border-red-500' : 'border-gray-200' }}"
                placeholder="admin@email.com" required>
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-medium mb-2">Password</label>
            <input type="password" name="password" 
                class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-purple-300 {{ $errors->has('password') ? 'border-red-500' : 'border-gray-200' }}"
                placeholder="********" required>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center mb-6">
            <input type="checkbox" name="remember" id="remember" class="mr-2">
            <label for="remember" class="text-sm text-gray-600">Ingat saya</label>
        </div>

        <button type="submit" 
            class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium hover:bg-purple-700 transition">
            Login
        </button>

        <p class="text-center mt-4">
            <a href="{{ route('login.landing') }}" class="text-sm text-purple-600 hover:underline">
                ← Kembali ke pilihan role
            </a>
        </p>
    </form>
</div>
@endsection