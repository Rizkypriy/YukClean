@extends('layouts.user')

@section('content')
<div class="container mx-auto p-4">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Edit Profil</h1>
            <a href="{{ url('/user/profile') }}" class="text-gray-600 hover:text-gray-800">
                Kembali
            </a>
        </div>
        
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('user.profile.update') }}">
            @csrf
            @method('PATCH')
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 font-medium">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                       class="w-full border rounded-lg px-3 py-2 @error('name') border-red-500 @enderror"
                       placeholder="Masukkan nama lengkap">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                       class="w-full border rounded-lg px-3 py-2 @error('email') border-red-500 @enderror"
                       placeholder="Masukkan email">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 font-medium">No. Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" 
                       class="w-full border rounded-lg px-3 py-2 @error('phone') border-red-500 @enderror"
                       placeholder="Masukkan nomor telepon">
                @error('phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 font-medium">Alamat</label>
                <textarea name="address" rows="3" 
                          class="w-full border rounded-lg px-3 py-2 @error('address') border-red-500 @enderror"
                          placeholder="Masukkan alamat lengkap">{{ old('address', $user->address ?? '') }}</textarea>
                @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 font-medium">Kota</label>
                <input type="text" name="city" value="{{ old('city', $user->city ?? '') }}" 
                       class="w-full border rounded-lg px-3 py-2"
                       placeholder="Masukkan kota">
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="window.history.back()" 
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                    Batal
                </button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection