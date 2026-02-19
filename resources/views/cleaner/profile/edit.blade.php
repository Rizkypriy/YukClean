{{-- resources/views/cleaner/profile/edit.blade.php --}}
@extends('cleaner.layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#f0fdf5] to-[#d3fcf2] pb-24">
    {{-- Header dengan Back Button --}}
    <div class="bg-white shadow-lg px-7 py-6 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('cleaner.profile.index') }}" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition">
                <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-xl font-semibold text-black">Edit Profil</h1>
        </div>
    </div>

    <div class="px-5 max-w-md mx-auto">
        {{-- Form Edit Profil --}}
        <form method="POST" action="{{ route('cleaner.profile.update') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Foto Profil --}}
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="text-xl">📸</span> Foto Profil
                </h3>
                
                <div class="flex flex-col items-center">
                    {{-- Preview Foto --}}
                    <div class="relative mb-4">
                        <div class="w-24 h-24 bg-gradient-to-br from-green-400 to-green-500 rounded-full flex items-center justify-center text-white text-3xl font-bold overflow-hidden" id="avatarPreviewContainer">
                            @if($cleaner->avatar)
                                <img src="{{ asset('storage/'.$cleaner->avatar) }}" alt="Profile" class="w-full h-full object-cover" id="avatarPreview">
                            @else
                                <span id="avatarInitial">{{ substr($cleaner->name, 0, 1) }}{{ substr(strstr($cleaner->name, ' ', true) ?: $cleaner->name, 1, 1) ?? '' }}</span>
                                <img src="#" alt="Preview" class="w-full h-full object-cover hidden" id="avatarPreview">
                            @endif
                        </div>
                        <label for="avatar" class="absolute bottom-0 right-0 w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white cursor-pointer hover:bg-green-700 transition border-2 border-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </label>
                    </div>
                    <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden">
                    <p class="text-xs text-gray-500">Klik ikon kamera untuk mengubah foto</p>
                </div>
            </div>

            {{-- Informasi Pribadi --}}
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="text-xl">👤</span> Informasi Pribadi
                </h3>
                
                <div class="space-y-4">
                    {{-- Nama Lengkap --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $cleaner->name) }}" 
                               class="w-full px-4 py-3 rounded-xl border-0 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-300 {{ $errors->has('name') ? 'border-red-500' : '' }}"
                               required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $cleaner->email) }}" 
                               class="w-full px-4 py-3 rounded-xl border-0 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-300 {{ $errors->has('email') ? 'border-red-500' : '' }}"
                               required>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nomor Telepon --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="tel" name="phone" value="{{ old('phone', $cleaner->phone) }}" 
                               class="w-full px-4 py-3 rounded-xl border-0 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-300 {{ $errors->has('phone') ? 'border-red-500' : '' }}"
                               required>
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jenis Kelamin --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <select name="gender" class="w-full px-4 py-3 rounded-xl border-0 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-300">
                            <option value="Laki-laki" {{ old('gender', $cleaner->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('gender', $cleaner->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Alamat --}}
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="text-xl">📍</span> Alamat
                </h3>
                
                <div class="space-y-4">
                    {{-- Alamat Lengkap --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea name="address" rows="3" 
                                  class="w-full px-4 py-3 rounded-xl border-0 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-300">{{ old('address', $cleaner->address) }}</textarea>
                    </div>

                    {{-- Kota --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                        <input type="text" name="city" value="{{ old('city', $cleaner->city) }}" 
                               class="w-full px-4 py-3 rounded-xl border-0 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-300">
                    </div>

                    {{-- Radius Layanan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Radius Layanan (km)</label>
                        <input type="number" name="radius_km" value="{{ old('radius_km', $cleaner->radius_km ?? 5) }}" 
                               class="w-full px-4 py-3 rounded-xl border-0 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-300"
                               min="1" max="50">
                        <p class="text-xs text-gray-500 mt-1">Jarak maksimal dari lokasi Anda</p>
                    </div>
                </div>
            </div>

            {{-- Ubah Password --}}
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="text-xl">🔒</span> Ubah Password
                </h3>
                <p class="text-xs text-gray-500 mb-4">Kosongkan jika tidak ingin mengubah password</p>
                
                <div class="space-y-4">
                    {{-- Password Baru --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input type="password" name="password" 
                               class="w-full px-4 py-3 rounded-xl border-0 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-300 {{ $errors->has('password') ? 'border-red-500' : '' }}">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" 
                               class="w-full px-4 py-3 rounded-xl border-0 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-300">
                    </div>
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="flex gap-3 pt-4">
                <a href="{{ route('cleaner.profile.index') }}" 
                   class="flex-1 bg-gray-100 text-gray-700 py-4 rounded-xl font-medium hover:bg-gray-200 transition text-center">
                    Batal
                </a>
                <button type="submit" 
                        class="flex-1 text-white py-4 rounded-xl font-medium transition-all duration-300 shadow-md hover:shadow-lg"
                        style="background: linear-gradient(135deg, #00bda2 0%, #00c85f 100%);">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Preview foto sebelum upload
    document.getElementById('avatar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatarPreview');
                const initial = document.getElementById('avatarInitial');
                
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (initial) initial.classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection