@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
<div class="min-h-screen bg-white pb-24">
    {{-- Header --}}
    <div class="bg-linear-to-r from-green-500 to-green-600 p-6 text-white">
        <a href="{{ route('orders.index') }}" class="inline-flex items-center text-white mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Pesanan
        </a>
        <h1 class="text-2xl font-bold">Detail Pesanan</h1>
        <p class="text-sm opacity-90 mt-1">{{ $order->order_number }}</p>
    </div>

    <div class="p-5 space-y-4">
        {{-- Status Card --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Status Pesanan</p>
                    @php
                        $badge = $order->status_badge;
                    @endphp
                    <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-medium {{ $badge[0] }} {{ $badge[1] }}">
                        {{ $badge[2] }}
                    </span>
                </div>
                @if(in_array($order->status, ['pending', 'confirmed']))
                <button onclick="showCancelModal()" 
                    class="text-red-600 border border-red-200 px-4 py-2 rounded-lg text-sm hover:bg-red-50 transition">
                    Batalkan Pesanan
                </button>
                @endif
            </div>
        </div>

        {{-- Layanan Card --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-700 mb-3">🔧 Layanan</h2>
            <div class="flex items-center gap-4">
                @if($order->service)
                <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $order->service->icon_path }}" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">{{ $order->service->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $order->service->description }}</p>
                </div>
                @else
                <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">{{ $order->bundle->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $order->bundle->description }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Alamat Card --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-700 mb-3">📍 Alamat</h2>
            <p class="text-gray-700">{{ $order->address }}</p>
        </div>

        {{-- Detail Rumah Card --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-700 mb-3">🏠 Detail Rumah</h2>
            <div class="grid grid-cols-2 gap-3 text-sm">
                @if($order->floor_count)
                <div>
                    <span class="text-gray-500">Jumlah Lantai:</span>
                    <p class="font-medium text-gray-800">{{ $order->floor_count }}</p>
                </div>
                @endif
                @if($order->room_size)
                <div>
                    <span class="text-gray-500">Ukuran Ruangan:</span>
                    <p class="font-medium text-gray-800">{{ $order->room_size }}</p>
                </div>
                @endif
                @if($order->special_conditions)
                <div class="col-span-2">
                    <span class="text-gray-500">Kondisi Khusus:</span>
                    <p class="font-medium text-gray-800">{{ $order->special_conditions }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Informasi Pemesan Card --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-700 mb-3">👤 Informasi Pemesan</h2>
            <div class="space-y-2 text-sm">
                <div>
                    <span class="text-gray-500">Nama:</span>
                    <p class="font-medium text-gray-800">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <span class="text-gray-500">No. HP:</span>
                    <p class="font-medium text-gray-800">{{ $order->customer_phone }}</p>
                </div>
            </div>
        </div>

        {{-- Jadwal Card --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-700 mb-3">📅 Jadwal</h2>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <span class="text-gray-500">Tanggal:</span>
                    <p class="font-medium text-gray-800">
                        @if($order->order_date)
                            {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
                        @else
                            -
                        @endif
                    </p>
                </div>
                <div>
                    <span class="text-gray-500">Jam:</span>
                    <p class="font-medium text-gray-800">
                        @if($order->start_time && $order->end_time)
                            {{ substr($order->start_time, 0, 5) }} - {{ substr($order->end_time, 0, 5) }}
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Harga Card --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-700 mb-3">💰 Rincian Harga</h2>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-medium">{{ $order->formatted_subtotal }}</span>
                </div>
                @if($order->discount > 0)
                <div class="flex justify-between text-sm text-green-600">
                    <span>Diskon</span>
                    <span>-{{ $order->formatted_discount }}</span>
                </div>
                @endif
                @if($order->promo)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Kode Promo</span>
                    <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $order->promo->code }}</span>
                </div>
                @endif
                <div class="border-t border-gray-200 pt-2 mt-2">
                    <div class="flex justify-between font-bold">
                        <span>Total</span>
                        <span class="text-green-600">{{ $order->formatted_total }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Catatan Card --}}
        @if($order->notes)
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-700 mb-3">📝 Catatan</h2>
            <p class="text-gray-700">{{ $order->notes }}</p>
        </div>
        @endif

        @if($order->status === 'cancelled' && $order->cancellation_reason)
        <div class="bg-red-50 rounded-xl border border-red-200 p-5">
            <h2 class="font-semibold text-red-700 mb-2">⛔ Alasan Pembatalan</h2>
            <p class="text-red-600">{{ $order->cancellation_reason }}</p>
        </div>
        @endif
    </div>
</div>

{{-- Modal Cancel --}}
<div id="cancelModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Batalkan Pesanan</h3>
        <p class="text-gray-600 mb-4">Apakah Anda yakin ingin membatalkan pesanan ini?</p>
        <form action="{{ route('orders.cancel', $order) }}" method="POST">
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

@push('scripts')
<script>
    function showCancelModal() {
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