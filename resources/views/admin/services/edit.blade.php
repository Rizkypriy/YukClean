{{-- resources/views/admin/services/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Edit Layanan - Admin YukClean')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Layanan</h1>
        <p class="text-gray-500 mt-1">Ubah data layanan {{ $service->name }}</p>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 max-w-2xl">
        <form action="{{ route('admin.services.update', $service) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Nama Layanan --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Layanan <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name', $service->name) }}"
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
                    <option value="Residential" {{ old('category', $service->category) == 'Residential' ? 'selected' : '' }}>Residential</option>
                    <option value="Commercial" {{ old('category', $service->category) == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                    <option value="Specialized" {{ old('category', $service->category) == 'Specialized' ? 'selected' : '' }}>Specialized</option>
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
                          placeholder="Deskripsi layanan...">{{ old('description', $service->description) }}</textarea>
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
                       value="{{ old('base_price', $service->base_price) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition"
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
                       value="{{ old('price_per_hour', $service->price_per_hour) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition"
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
                       value="{{ old('min_hours', $service->min_hours) }}"
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
                @if($service->image)
                <div class="mb-2">
                    <img src="{{ asset('storage/'.$service->image) }}" alt="{{ $service->name }}" class="w-32 h-32 object-cover rounded-lg">
                </div>
                @endif
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
                           {{ old('is_active', $service->is_active) ? 'checked' : '' }}
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
                    Update Layanan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection