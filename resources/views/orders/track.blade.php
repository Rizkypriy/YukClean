@extends('layouts.app')

@section('title', 'Lacak Pesanan')

@section('content')
<div class="min-h-screen bg-white pb-24">
    {{-- Header --}}
    <div class="bg-linear-to-r from-green-500 to-green-600 p-6 text-white">
        <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center text-white mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold">Lacak Pesanan</h1>
        <p class="text-sm opacity-90 mt-1">{{ $order->order_number }}</p>
    </div>

    <div class="p-5 space-y-4">
        {{-- Status Pesanan --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-700 mb-2">📦 Status Pesanan Anda</h2>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    @php
                        $statusBadge = match($order->status) {
                            'pending' => ['bg-yellow-100', 'text-yellow-600', 'Menunggu Konfirmasi'],
                            'confirmed' => ['bg-blue-100', 'text-blue-600', 'Dikonfirmasi'],
                            'on_progress' => ['bg-green-100', 'text-green-600', 'Sedang Diproses'],
                            'completed' => ['bg-gray-100', 'text-gray-600', 'Selesai'],
                            'cancelled' => ['bg-red-100', 'text-red-600', 'Dibatalkan'],
                            default => ['bg-gray-100', 'text-gray-600', $order->status],
                        };
                    @endphp
                    <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-medium {{ $statusBadge[0] }} {{ $statusBadge[1] }}">
                        {{ $statusBadge[2] }}
                    </span>
                </div>
                
                {{-- Estimasi Kedatangan (hanya untuk on_progress) --}}
                @if($order->status === 'on_progress')
                <div class="text-right">
                    <p class="text-sm text-gray-500">Estimasi Kedatangan</p>
                    <p class="text-lg font-bold text-green-600">15 menit</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Tracking Steps --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-700 mb-4">📍 Tracking Pesanan</h2>
            
            <div class="space-y-4">
                {{-- Step 1: Petugas menuju lokasi --}}
                <div class="flex items-start gap-3">
                    <div class="shrink-0">
                        @if(in_array($order->status, ['on_progress', 'completed']))
                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        @else
                            <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-gray-400 text-xs">1</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">Petugas menuju lokasi</p>
                        <p class="text-sm text-gray-500">
                            @if($order->status === 'on_progress')
                                Sedang berlangsung...
                            @elseif($order->status === 'completed')
                                Selesai
                            @else
                                Menunggu
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Step 2: Sedang bekerja --}}
                <div class="flex items-start gap-3">
                    <div class="shrink-0">
                        @if($order->status === 'completed')
                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        @else
                            <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-gray-400 text-xs">2</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">Sedang bekerja</p>
                        <p class="text-sm text-gray-500">
                            @if($order->status === 'completed')
                                Selesai
                            @else
                                Menunggu
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Step 3: Selesai --}}
                <div class="flex items-start gap-3">
                    <div class="shrink-0">
                        @if($order->status === 'completed')
                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        @else
                            <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-gray-400 text-xs">3</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">Selesai</p>
                        <p class="text-sm text-gray-500">
                            @if($order->status === 'completed')
                                Selesai
                            @else
                                Menunggu
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informasi Petugas (hanya untuk on_progress dan completed) --}}
        @if(in_array($order->status, ['on_progress', 'completed']))
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-700 mb-4">👤 Informasi Petugas</h2>
            
            <div class="flex items-center gap-4">
                {{-- Avatar Petugas --}}
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                
                {{-- Detail Petugas --}}
                <div class="flex-1">
                    <h3 class="font-bold text-lg text-gray-800">Ardy Septia Rizky</h3>
                    <p class="text-sm text-gray-600">Petugas Profesional</p>
                    
                    {{-- Kontak Petugas --}}
                    <div class="flex gap-3 mt-2">
                        <a href="tel:08123456789" class="text-blue-600 text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            Hubungi
                        </a>
                        <a href="#" class="text-green-600 text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Chat
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Informasi Tambahan --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-700 mb-3">📋 Detail Pesanan</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Layanan</span>
                    <span class="font-medium text-gray-800">{{ $order->service->name ?? $order->bundle->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal</span>
                    <span class="font-medium text-gray-800">
                        @if($order->order_date)
                            {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Jam</span>
                    <span class="font-medium text-gray-800">{{ substr($order->start_time, 0, 5) }} - {{ substr($order->end_time, 0, 5) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Alamat</span>
                    <span class="font-medium text-gray-800 text-right">{{ $order->address }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection