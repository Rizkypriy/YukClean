{{-- resources/views/orders/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="min-h-screen py-4 md:py-8" style="background-color: #e8fdf3;">
    {{-- Container untuk desktop dengan card --}}
    <div class="desktop-container mx-auto" style="max-width: 100%;">
        {{-- Card Utama untuk Desktop --}}
        <div class="bg-[#e8fdf3] md:rounded-2xl md:shadow-xl overflow-hidden">
            
            {{-- Header dengan background gradient --}}
            <div class="rounded-b-2xl md:rounded-none p-5 text-white shadow-lg relative overflow-hidden"
                 style="background: linear-gradient(135deg, #00bda2 0%, #00c85f 100%);">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2">
                        {{-- Logo --}}
                        <img src="{{ asset('img/logo.png') }}" alt="Yuk Clean Logo" class="w-8 h-8">
                        <h1 class="text-xl font-bold">Yuk Clean</h1>
                    </div>
                </div>
                <div class="mb-6 text-left md:text-center">
                    <h2 class="text-xl font-semibold">Pesanan Saya</h2>
                    <p class="text-sm opacity-90 mt-1">Dapatkan penawaran terbaik untuk layanan kebersihan</p>
                </div>
            </div>

            {{-- Konten dalam Card --}}
            <div class="p-5 md:p-8 mt-4" style="background-color: #e8fdf3;">
                <div class="space-y-4">
                    {{-- Tab Navigasi dengan Alpine.js --}}
                    <div x-data="{ activeTab: 'aktif' }" class="relative">
                        {{-- Tab Buttons --}}
                        <div class="flex bg-white p-0.5 rounded-full shadow-lg">
                            <button 
                                @click="activeTab = 'aktif'"
                                :class="activeTab === 'aktif' ? 'bg-[#00bba7] text-white' : 'text-gray-600'"
                                class="flex-1 py-1.5 text-center text-xs font-medium rounded-full transition">
                                Aktif ({{ $activeOrders->count() }})
                            </button>
                            
                            <button 
                                @click="activeTab = 'riwayat'"
                                :class="activeTab === 'riwayat' ? 'bg-[#00bba7] text-white' : 'text-gray-600'"
                                class="flex-1 py-1.5 text-center text-xs font-medium rounded-full transition">
                                Riwayat ({{ $historyOrders->count() }})
                            </button>
                        </div>

                        {{-- Konten Tab Aktif --}}
                        <div x-show="activeTab === 'aktif'" x-transition class="mt-4 space-y-4 mb-24">
                            @forelse($activeOrders as $order)
                            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm service-card">
                                {{-- Header Order --}}
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-sm font-medium text-gray-500">{{ $order->order_number }}</span>
                                    @php
                                        $statusBadge = match($order->status) {
                                            'pending' => ['bg-yellow-100', 'text-yellow-600', 'Pending'],
                                            'confirmed' => ['bg-blue-100', 'text-blue-600', 'Dikonfirmasi'],
                                            'on_progress' => ['bg-green-100', 'text-green-600', 'Dalam Proses'],
                                            'completed' => ['bg-gray-100', 'text-gray-600', 'Selesai'],
                                            'cancelled' => ['bg-red-100', 'text-red-600', 'Dibatalkan'],
                                            default => ['bg-gray-100', 'text-gray-600', $order->status],
                                        };
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusBadge[0] }} {{ $statusBadge[1] }}">
                                        {{ $statusBadge[2] }}
                                    </span>
                                </div>

                                {{-- Nama Layanan --}}
                                <h3 class="font-bold text-lg text-gray-800 mb-3">
                                    {{ $order->service->name ?? $order->bundle->name }}
                                </h3>

                                {{-- Detail Order --}}
                                <div class="space-y-2 mb-4">
                                    {{-- Nama Customer --}}
                                    <div class="flex items-start gap-3">
                                        <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="text-sm text-gray-700">{{ $order->customer_name }}</span>
                                    </div>
                                    
                                    {{-- Tanggal dan Jam --}}
                                    <div class="flex items-start gap-3">
                                        <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-sm text-gray-700">
                                            @if($order->order_date)
                                                {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
                                            @else
                                                {{ __('Tanggal tidak tersedia') }}
                                            @endif
                                            • {{ substr($order->start_time, 0, 5) }} - {{ substr($order->end_time, 0, 5) }}
                                        </span>
                                    </div>
                                    
                                    {{-- Alamat --}}
                                    <div class="flex items-start gap-3">
                                        <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="text-sm text-gray-700">{{ $order->address }}</span>
                                    </div>
                                </div>

                                {{-- Harga dan Tombol Aksi --}}
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <span class="text-sm font-bold text-[#12968a]">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                                    
                                    <div class="flex gap-2">
                                        {{-- Tombol Hubungi --}}
                                        <a href="tel:{{ $order->customer_phone }}" 
                                           class="flex items-center gap-1 text center border border-[#00bda2] text-[#00bda2] px-3 py-2 rounded-xl text-xs font-medium hover:text-[black] transition" >
                                            Hubungi
                                        </a>

                                        {{-- Tombol Lacak --}}
                                        <a href="{{ route('user.orders.track', $order) }}" 
                                         class="flex items-center text-center gap-1 text-white px-3 py-2 rounded-xl text-xs font-medium hover:opacity-80 transition" 
                                            style="background: linear-gradient(135deg, #00bda2 0%, #00c85f 100%);">
                                            Lacak Pesanan
                                        </a>

                                        {{-- Tombol Batalkan (untuk status tertentu) --}}
                                        @if(in_array($order->status, ['pending', 'confirmed']))
                                        <button onclick="showCancelModal('{{ $order->id }}')"
                                            class="flex items-center gap-1 text center border border-[#bd0000] text-[#bd0000] px-3 py-2 rounded-xl text-xs font-medium hover:text-[#800000] transition">
                                            Batalkan
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-12">
                                <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="text-gray-500">Belum ada pesanan aktif</p>
                                <a href="{{ route('user.dashboard') }}" class="inline-block mt-4 text-green-600 font-medium">Mulai pesan sekarang</a>
                            </div>
                            @endforelse
                        </div>

                        {{-- Konten Tab Riwayat --}}
                        <div x-show="activeTab === 'riwayat'" x-transition class="mt-4 space-y-4 mb-24">
                            @forelse($historyOrders as $order)
                            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm service-card">
                                {{-- Header Order --}}
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-sm font-medium text-gray-500">{{ $order->order_number }}</span>
                                    @php
                                        $statusBadge = match($order->status) {
                                            'completed' => ['bg-gray-100', 'text-gray-600', 'Selesai'],
                                            'cancelled' => ['bg-red-100', 'text-red-600', 'Dibatalkan'],
                                            default => ['bg-gray-100', 'text-gray-600', $order->status],
                                        };
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusBadge[0] }} {{ $statusBadge[1] }}">
                                        {{ $statusBadge[2] }}
                                    </span>
                                </div>

                                {{-- Nama Layanan --}}
                                <h3 class="font-bold text-lg text-gray-800 mb-3">
                                    {{ $order->service->name ?? $order->bundle->name }}
                                </h3>

                                {{-- Detail Order --}}
                                <div class="space-y-2 mb-4">
                                    {{-- Nama Customer --}}
                                    <div class="flex items-start gap-3">
                                        <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="text-sm text-gray-700">{{ $order->customer_name }}</span>
                                    </div>
                                    
                                    {{-- Tanggal --}}
                                    <div class="flex items-start gap-3">
                                        <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-sm text-gray-700">
                                            @if($order->order_date)
                                                {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
                                            @else
                                                {{ __('Tanggal tidak tersedia') }}
                                            @endif
                                        </span>
                                    </div>
                                    
                                    {{-- Alamat --}}
                                    <div class="flex items-start gap-3">
                                        <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="text-sm text-gray-700">{{ $order->address }}</span>
                                    </div>
                                </div>

                                {{-- Harga dan Tombol Aksi --}}
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <span class="text-sm font-bold text-[#12968a]">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                                    
                                    <div class="flex gap-2">
                                        <a href="{{ route('user.dashboard', $order) }}" 
                                               class="border border-[#12968a] text-[#12968a] px-4 py-2 rounded-lg text-sm font-medium hover: opacity-80 transition">
                                                Pesan Lagi
                                            </a>
                                            @if($order->status === 'completed')
                                            <a href="{{ route('user.orders.completed', $order) }}" 
                                               class="border border-gray-500 text-gray-500 px-2 py-2 rounded-lg text-sm font-medium hover: opacity-80 transition">
                                                Ulasan  
                                            </a>
                                        @else
                                            <a href="{{ route('user.orders.show', $order) }}" 
                                               class="flex items-center gap-1 text center border border-[#00bda2] text-[#00bda2] px-3 py-2 rounded-xl text-xs font-medium hover:text-[black] transition">
                                                Detail
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-12">
                                <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-gray-500">Belum ada riwayat pesanan</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Cancel --}}
<div id="cancelModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Batalkan Pesanan</h3>
        <p class="text-gray-600 mb-4">Apakah Anda yakin ingin membatalkan pesanan ini?</p>
        <form id="cancelForm" method="POST">
            @csrf
            <textarea name="cancellation_reason" rows="3" 
                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-300 mb-4"
                placeholder="Alasan pembatalan" required></textarea>
            <div class="flex gap-3">
                <button type="button" onclick="hideCancelModal()"
                    class="flex-1 border border-gray-300 text-gray-700 py-3 rounded-lg font-medium hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-red-600 text-white py-3 rounded-lg font-medium hover:bg-red-700 transition">
                    Ya, Batalkan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Mobile styles */
    @media (max-width: 767px) {
        .min-h-screen {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
        
        .desktop-container {
            max-width: 100% !important;
            padding: 0 !important;
        }

        .desktop-container > div {
            background-color: transparent !important;
            box-shadow: none !important;
        }

        .desktop-container > div > div:first-child {
            border-radius: 0 0 1rem 1rem !important;
            padding: 1.25rem !important;
            margin-top: 0 !important;
            border-top-left-radius: 0 !important;
            border-top-right-radius: 0 !important;
        }

        .desktop-container > div > div:last-child {
            padding: 0 1.25rem 1.25rem 1.25rem !important;
        }
    }

    /* Desktop Styles */
    @media (min-width: 768px) {
        .desktop-container {
            max-width: 1200px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            padding-left: 24px !important;
            padding-right: 24px !important;
        }

        .desktop-container > div {
            border-radius: 24px !important;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1), 0 10px 20px -5px rgba(0, 0, 0, 0.05) !important;
        }

        .desktop-container > div > div:first-child {
            border-radius: 24px 24px 0 0 !important;
        }
    }

    /* Service card hover effect */
    .service-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease !important;
    }

    .service-card:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 5px 10px -5px rgba(0, 0, 0, 0.05) !important;
    }
</style>

{{-- Script untuk Alpine.js --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

@push('scripts')
<script>
    // Fungsi untuk menampilkan modal cancel
    function showCancelModal(orderId) {
        const form = document.getElementById('cancelForm');
        const modal = document.getElementById('cancelModal');
        
        if (form && modal) {
            // Set action form dengan prefix user/
            form.action = `/user/orders/${orderId}/cancel`;
            
            // Tampilkan modal
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            
            // Optional: Log untuk debugging
            console.log('Cancel modal shown for order:', orderId);
        } else {
            console.error('Form or modal not found');
        }
    }
    
    // Fungsi untuk menyembunyikan modal cancel
    function hideCancelModal() {
        const modal = document.getElementById('cancelModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    }
    
    // Fungsi untuk handle klik di luar modal
    function handleClickOutside(event) {
        const modal = document.getElementById('cancelModal');
        if (modal && event.target === modal) {
            hideCancelModal();
        }
    }
    
    // Pastikan DOM sudah siap sebelum menambahkan event listener
    document.addEventListener('DOMContentLoaded', function() {
        // Hapus event listener lama jika ada (untuk menghindari duplikasi)
        window.removeEventListener('click', handleClickOutside);
        
        // Tambahkan event listener baru
        window.addEventListener('click', handleClickOutside);
        
        console.log('Cancel modal script initialized');
    });
    
    // Cleanup event listener saat halaman di-unload (opsional)
    window.addEventListener('beforeunload', function() {
        window.removeEventListener('click', handleClickOutside);
    });
</script>
@endpush
@endsection