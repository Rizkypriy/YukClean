@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@section('content')
<div class="min-h-screen bg-white pb-24">
    {{-- Header --}}
    <div class="bg-linear-to-r from-green-500 to-green-600 p-6 text-white">
        <a href="{{ route('orders.show', $payment->order) }}" class="inline-flex items-center text-white mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Pesanan
        </a>
        <h1 class="text-2xl font-bold">Detail Pembayaran</h1>
        <p class="text-sm opacity-90 mt-1">{{ $payment->payment_number }}</p>
    </div>

    <div class="p-5">
        {{-- Status Pembayaran --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Status Pembayaran</p>
                    @php
                        $badge = $payment->status_badge;
                    @endphp
                    <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-medium {{ $badge[0] }} {{ $badge[1] }}">
                        {{ $badge[2] }}
                    </span>
                </div>
                @if($payment->payment_status === 'pending')
                <button onclick="showPaymentModal()" 
                    class="bg-green-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition">
                    Konfirmasi Pembayaran
                </button>
                @endif
            </div>
        </div>

        {{-- Informasi Pembayaran --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm mb-4">
            <h2 class="font-semibold text-gray-700 mb-4">💳 Informasi Pembayaran</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Metode Pembayaran</span>
                    <span class="font-medium">
                        @if($payment->payment_method === 'e-wallet')
                            E-Wallet
                        @elseif($payment->payment_method === 'virtual_account')
                            Virtual Account
                        @elseif($payment->payment_method === 'qris')
                            QRIS
                        @else
                            {{ $payment->payment_method }}
                        @endif
                    </span>
                </div>

                @if($payment->provider)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Provider</span>
                    <span class="font-medium capitalize">{{ $payment->provider }}</span>
                </div>
                @endif

                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Nomor Pembayaran</span>
                    <span class="font-medium">{{ $payment->payment_number }}</span>
                </div>

                @if($payment->paid_at)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Waktu Pembayaran</span>
                    <span class="font-medium">{{ $payment->paid_at->format('d M Y H:i') }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Detail Pesanan --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm mb-4">
            <h2 class="font-semibold text-gray-700 mb-4">📋 Detail Pesanan</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Nomor Pesanan</span>
                    <span class="font-medium">{{ $payment->order->order_number }}</span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Layanan</span>
                    <span class="font-medium">{{ $payment->order->service->name ?? $payment->order->bundle->name }}</span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Tanggal</span>
                    <span class="font-medium">{{ \Carbon\Carbon::parse($payment->order->order_date)->format('d M Y') }}</span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Jam</span>
                    <span class="font-medium">{{ substr($payment->order->start_time, 0, 5) }} - {{ substr($payment->order->end_time, 0, 5) }}</span>
                </div>
            </div>
        </div>

        {{-- Rincian Biaya --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-700 mb-4">💰 Rincian Biaya</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Biaya Layanan</span>
                    <span class="font-medium">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Biaya Admin</span>
                    <span class="font-medium">Rp {{ number_format($payment->admin_fee, 0, ',', '.') }}</span>
                </div>

                @if($payment->discount > 0)
                <div class="flex justify-between text-sm text-green-600">
                    <span>Diskon</span>
                    <span>- Rp {{ number_format($payment->discount, 0, ',', '.') }}</span>
                </div>
                @endif

                <div class="border-t border-gray-200 pt-3 mt-3">
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total Dibayar</span>
                        <span class="text-green-600">Rp {{ number_format($payment->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Pembayaran (untuk simulasi) --}}
<div id="paymentModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Konfirmasi Pembayaran</h3>
        <p class="text-gray-600 mb-4">Apakah Anda sudah melakukan pembayaran?</p>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-yellow-700">
                <strong>Petunjuk:</strong> Ini adalah simulasi pembayaran. Klik "Ya, Sudah Bayar" untuk mengubah status pembayaran menjadi Lunas.
            </p>
        </div>

        <form action="{{ route('payments.confirm', $payment) }}" method="POST">
            @csrf
            <div class="flex gap-3">
                <button type="button" onclick="hidePaymentModal()"
                    class="flex-1 border border-gray-300 text-gray-700 py-3 rounded-lg font-medium hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700 transition">
                    Ya, Sudah Bayar
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function showPaymentModal() {
        document.getElementById('paymentModal').classList.remove('hidden');
    }
    
    function hidePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('paymentModal');
        if (event.target === modal) {
            hidePaymentModal();
        }
    });
</script>
@endpush
@endsection