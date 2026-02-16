{{-- resources/views/orders/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="pb-24" style="background-color: #e8fdf3;">
    {{-- Header dengan background gradient --}}
    <div class="rounded-b-2xl p-6 text-white shadow-lg relative overflow-hidden"
         style="background: linear-gradient(135deg, #00bda2 0%, #00c85f 100%);">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">Yuk Clean</h1>
        </div>
        <h2 class="text-xl font-semibold">Pesanan Saya</h2>
        <p class="text-sm opacity-90">Dapatkan penawaran terbaik untuk layanan kebersihan</p>
    </div>

    <div class="px-6 mt-6 space-y-4">
        {{-- Tab Navigasi dengan Alpine.js --}}
        <div x-data="{ activeTab: 'aktif' }" class="relative -mt-3">
            {{-- Tab Buttons --}}
            <div class="flex bg-white p-0.5 rounded-full mx-4 shadow-lg">
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
            <div x-show="activeTab === 'aktif'" x-transition class="p-4 space-y-4">
                @forelse($activeOrders as $order)
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
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
                        <span class="text-xl font-bold text-green-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        
                        <div class="flex gap-2">
                            {{-- Tombol Hubungi --}}
                            <a href="tel:{{ $order->customer_phone }}" 
                               class="flex items-center gap-1 border border-blue-500 text-blue-500 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                Hubungi
                            </a>

                            {{-- Tombol Lacak --}}
                            <a href="{{ route('orders.track', $order) }}" 
                               class="flex items-center gap-1 border border-green-600 text-green-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                                Lacak Pesanan
                            </a>

                            {{-- Tombol Batalkan (untuk status tertentu) --}}
                            @if(in_array($order->status, ['pending', 'confirmed']))
                            <button onclick="showCancelModal('{{ $order->id }}')"
                                class="border border-red-500 text-red-500 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-50 transition">
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
                    <a href="{{ route('home') }}" class="inline-block mt-4 text-green-600 font-medium">Mulai pesan sekarang</a>
                </div>
                @endforelse
            </div>

            {{-- Konten Tab Riwayat --}}
            <div x-show="activeTab === 'riwayat'" x-transition class="p-4 space-y-4">
                @forelse($historyOrders as $order)
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm opacity-75">
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
                        <span class="text-xl font-bold text-green-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        
                        <div class="flex gap-2">
                            @if($order->status === 'completed')
                                <a href="{{ route('orders.completed', $order) }}" 
                                   class="border border-green-600 text-green-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-50 transition">
                                    Lihat Ucapan Terima Kasih
                                </a>
                            @else
                                <a href="{{ route('orders.show', $order) }}" 
                                   class="border border-green-600 text-green-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-50 transition">
                                    Detail
                                </a>
                            @endif
                            
                            <button class="border border-blue-500 text-blue-500 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-50 transition">
                                Pesan Lagi
                            </button>
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

{{-- Script untuk Alpine.js --}}
<script src="//unpkg.com/alpinejs" defer></script>

@push('scripts')
<script>
    function showCancelModal(orderId) {
        const form = document.getElementById('cancelForm');
        form.action = `/orders/${orderId}/cancel`;
        document.getElementById('cancelModal').classList.remove('hidden');
    }
    
    function hideCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('cancelModal');
        if (event.target === modal) {
            hideCancelModal();
        }
    });
</script>
@endpush
@endsection