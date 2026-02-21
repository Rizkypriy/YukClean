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
        <form action="{{ route('admin.services.update', $service) }}" method="POST">
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

            {{-- Harga --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Harga (Rp) <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="price" 
                       value="{{ old('price', $service->price) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition"
                       min="0"
                       required>
                @error('price')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Durasi Minimal --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Durasi Minimal (Jam) <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="duration" 
                       value="{{ old('duration', $service->duration) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition"
                       min="1"
                       required>
                @error('duration')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Icon Name --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Icon Layanan</label>
                <select name="icon_name" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                    <option value="ruangan" {{ old('icon_name', $service->icon_name) == 'ruangan' ? 'selected' : '' }}>Ruangan</option>
                    <option value="kamar" {{ old('icon_name', $service->icon_name) == 'kamar' ? 'selected' : '' }}>Kamar</option>
                    <option value="ruang tamu" {{ old('icon_name', $service->icon_name) == 'ruang tamu' ? 'selected' : '' }}>Ruang Tamu</option>
                    <option value="toilet" {{ old('icon_name', $service->icon_name) == 'toilet' ? 'selected' : '' }}>Toilet</option>
                    <option value="dapur" {{ old('icon_name', $service->icon_name) == 'dapur' ? 'selected' : '' }}>Dapur</option>
                </select>
                @error('icon_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Is Popular --}}
            <div class="mb-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" 
                           name="is_popular" 
                           value="1" 
                           {{ old('is_popular', $service->is_popular) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                    <span class="text-sm font-medium text-gray-700">Jadikan layanan populer</span>
                </label>
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

<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Inisialisasi ikon Lucide
    lucide.createIcons();
</script>
@endsection