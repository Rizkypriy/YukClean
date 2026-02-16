{{-- resources/views/profile/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="pb-24 bg-white">
    {{-- Header dengan background gradient --}}
    <div class="rounded-b-2xl p-6 text-white shadow-lg relative overflow-hidden"
         style="background: linear-gradient(135deg, #00bda2 0%, #00c85f 100%);">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">Profil Saya</h1>
            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center relative">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                @if($notificationCount > 0)
                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs flex items-center justify-center rounded-full">
                    {{ $notificationCount }}
                </span>
                @endif
            </div>
        </div>
        <div class="pb-8"></div>
    </div>

    {{-- Profile Info (menimpa header) --}}
    <div class="px-5 -mt-16 relative z-10">
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            {{-- Bagian Atas: Avatar dan Info --}}
            <div class="flex items-center gap-4 mb-4">
                {{-- Avatar --}}
                <div class="w-20 h-20 bg-linear-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center text-white text-3xl font-bold shadow-md overflow-hidden">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        {{ $user->initials }}
                    @endif
                </div>
                
                {{-- User Info --}}
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-gray-800">{{ $user->name }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        @php $badge = $user->member_level_badge; @endphp
                        <span class="text-xs {{ $badge[0] }} {{ $badge[1] }} px-2 py-0.5 rounded-full">
                            Member {{ $badge[2] }}
                        </span>
                        <span class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full">
                            {{ $orderCount }} Pesanan
                        </span>
                    </div>
                </div>
                
                {{-- Edit Icon --}}
                <a href="{{ route('profile.edit') }}" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </a>
            </div>

            {{-- Bagian Bawah: Kontak dan Alamat --}}
            <div class="border-t border-gray-100 pt-4 space-y-3">
                {{-- Telepon --}}
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-green-50 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <span class="text-sm text-gray-700">{{ $user->phone }}</span>
                </div>

                {{-- Email --}}
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-green-50 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="text-sm text-gray-700">{{ $user->email }}</span>
                </div>

                {{-- Alamat --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-green-50 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <span class="text-sm text-gray-700 flex-1">{{ $user->address }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="px-5 mt-6 grid grid-cols-3 gap-3">
        <div class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm">
            <span class="block text-2xl font-bold text-green-600">{{ $activeOrdersCount }}</span>
            <span class="text-xs text-gray-500">Aktif</span>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm">
            <span class="block text-2xl font-bold text-green-600">{{ $completedOrdersCount }}</span>
            <span class="text-xs text-gray-500">Selesai</span>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm">
            <span class="block text-2xl font-bold text-green-600">{{ $voucherCount }}</span>
            <span class="text-xs text-gray-500">Voucher</span>
        </div>
    </div>

    {{-- Menu Sections --}}
    <div class="px-5 mt-6 space-y-4">
        {{-- Akun & Keamanan --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                <h3 class="font-semibold text-gray-700">Akun & Keamanan</h3>
            </div>
            <div class="divide-y divide-gray-100">
                <a href="{{ route('profile.edit') }}" class="flex items-center justify-between px-4 py-3.5 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="text-sm text-gray-700">Edit Profil</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                
                <a href="{{ route('profile.security') }}" class="flex items-center justify-between px-4 py-3.5 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span class="text-sm text-gray-700">Keamanan Akun</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                
                <a href="#" class="flex items-center justify-between px-4 py-3.5 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span class="text-sm text-gray-700">Metode Pembayaran</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- Pengaturan --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                <h3 class="font-semibold text-gray-700">Pengaturan</h3>
            </div>
            <div class="divide-y divide-gray-100">
                <a href="{{ route('profile.notifications') }}" class="flex items-center justify-between px-4 py-3.5 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="text-sm text-gray-700">Notifikasi</span>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($notificationCount > 0)
                        <span class="text-xs bg-red-500 text-white px-1.5 py-0.5 rounded-full">{{ $notificationCount }}</span>
                        @endif
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
                
                <a href="#" class="flex items-center justify-between px-4 py-3.5 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-sm text-gray-700">Alamat Tersimpan</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                
                <a href="#" class="flex items-center justify-between px-4 py-3.5 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        <span class="text-sm text-gray-700">Layanan Favorit</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- Bantuan & Informasi --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                <h3 class="font-semibold text-gray-700">Bantuan & Informasi</h3>
            </div>
            <div class="divide-y divide-gray-100">
                <a href="#" class="flex items-center justify-between px-4 py-3.5 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm text-gray-700">Pusat Bantuan</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                
                <a href="#" class="flex items-center justify-between px-4 py-3.5 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-sm text-gray-700">Syarat & Ketentuan</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                
                <a href="#" class="flex items-center justify-between px-4 py-3.5 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        <span class="text-sm text-gray-700">Beri Rating Aplikasi</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- Tombol Keluar --}}
        <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin keluar?');">
            @csrf
            <button type="submit" class="w-full bg-red-50 text-red-600 py-3.5 rounded-xl font-medium hover:bg-red-100 transition border border-red-100 mt-6">
                Keluar
            </button>
        </form>

        {{-- App Version --}}
        <p class="text-center text-xs text-gray-400 py-4">Yuk Clean v1.0.0</p>
    </div>
</div>
@endsection