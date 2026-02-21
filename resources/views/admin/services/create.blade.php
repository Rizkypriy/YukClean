{{-- resources/views/admin/services/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Tambah Layanan - Admin YukClean')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Tambah Layanan Baru</h1>
        <p class="text-gray-500 mt-1">Tambahkan layanan kebersihan baru</p>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 max-w-2xl">
        <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Nama Layanan --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Layanan <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition"
                       placeholder="Contoh: Pembersihan Rumah"
                       required>
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kategori --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select name="category" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition"
                        required>
                    <option value="">Pilih Kategori</option>
                    <option value="Residential" {{ old('category') == 'Residential' ? 'selected' : '' }}>Residential</option>
                    <option value="Commercial" {{ old('category') == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                    <option value="Specialized" {{ old('category') == 'Specialized' ? 'selected' : '' }}>Specialized</option>
                </select>
                @error('category')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" 
                          rows="3"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition"
                          placeholder="Deskripsi layanan...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Harga Dasar --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Harga Dasar (Rp) <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="base_price" 
                       value="{{ old('base_price') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition"
                       placeholder="Contoh: 500000"
                       min="0"
                       required>
                @error('base_price')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Harga per Jam --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Harga per Jam (Rp) <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="price_per_hour" 
                       value="{{ old('price_per_hour') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition"
                       placeholder="Contoh: 75000"
                       min="0"
                       required>
                @error('price_per_hour')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Minimal Jam --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Minimal Jam <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="min_hours" 
                       value="{{ old('min_hours', 1) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition"
                       min="1"
                       required>
                @error('min_hours')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Upload Gambar --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Gambar Layanan</label>
                <input type="file" 
                       name="image" 
                       accept="image/*"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maks: 2MB</p>
                @error('image')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status Aktif --}}
            <div class="mb-6">
                <label class="flex items-center gap-2">
                    <input type="checkbox" 
                           name="is_active" 
                           value="1" 
                           {{ old('is_active', 1) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                    <span class="text-sm font-medium text-gray-700">Aktifkan layanan</span>
                </label>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.services.index') }}" 
                   class="px-6 py-2.5 rounded-lg border border-gray-300 font-semibold text-gray-600 hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-lg bg-teal-700 text-white font-semibold hover:bg-teal-800 transition shadow-md">
                    Simpan Layanan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection