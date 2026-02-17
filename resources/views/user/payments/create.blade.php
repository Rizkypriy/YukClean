{{-- resources/views/user/payments/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
<div class="min-h-screen bg-white pb-24">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 text-white">  {{-- PERBAIKAN: dari 'bg-linear-to-r' ke 'bg-gradient-to-r' --}}
        <a href="{{ route('user.orders.show', $order) }}" class="inline-flex items-center text-white mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold">Pembayaran</h1>
    </div>

    <div class="p-5">
        {{-- Ringkasan Pesanan --}}
        <div class="bg-green-50 rounded-xl p-5 mb-6 border border-green-100">
            <h2 class="font-semibold text-gray-700 mb-2">📋 Ringkasan Pesanan</h2>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $order->service->name ?? $order->bundle->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $order->order_number }}</p>
                </div>
            </div>
        </div>

        {{-- Ringkasan Pembayaran --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm mb-6">
            <h2 class="font-semibold text-gray-700 mb-4">💰 Ringkasan Pembayaran</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Biaya Layanan</span>
                    <span class="font-medium">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
                
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Biaya Admin</span>
                    <span class="font-medium">Rp {{ number_format($adminFee, 0, ',', '.') }}</span>
                </div>
                
                @if($order->discount > 0)
                <div class="flex justify-between text-sm text-green-600">
                    <span>Diskon {{ $order->promo->code ?? '' }}</span>
                    <span>- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
                @endif
                
                <div class="border-t border-gray-200 pt-3 mt-3">
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total Pembayaran</span>
                        <span class="text-green-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pilih Metode Pembayaran dengan Alpine.js --}}
        <div x-data="{ 
            selectedMethod: null,
            selectedProvider: null,
            methods: {
                ewallet: { name: 'E-Wallet', icon: '📱', providers: ['gopay', 'ovo', 'dana', 'shopeepay'] },
                va: { name: 'Virtual Account', icon: '🏦', providers: ['bca', 'mandiri', 'bni', 'bri'] },
                qris: { name: 'QRIS', icon: '📲', providers: [] }
            }
        }">
            <form action="{{ route('user.payments.store', $order) }}" method="POST">
                @csrf
                
                {{-- Hidden inputs untuk menyimpan pilihan --}}
                <input type="hidden" name="payment_method" x-bind:value="selectedMethod">
                <input type="hidden" name="provider" x-bind:value="selectedProvider">

                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <h2 class="font-semibold text-gray-700 mb-4">💳 Pilih Metode Pembayaran</h2>
                    
                    {{-- Daftar Metode Pembayaran --}}
                    <div class="space-y-3">
                        {{-- E-Wallet --}}
                        <div class="border rounded-xl overflow-hidden">
                            <button 
                                type="button"
                                @click="selectedMethod = selectedMethod === 'ewallet' ? null : 'ewallet'; selectedProvider = null"
                                class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition"
                                :class="{ 'bg-green-50 border-green-500': selectedMethod === 'ewallet' }">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">📱</span>
                                    <span class="font-semibold text-gray-800">E-Wallet</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-500" :class="{ 'transform rotate-180': selectedMethod === 'ewallet' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            {{-- Provider E-Wallet --}}
                            <div x-show="selectedMethod === 'ewallet'" x-collapse class="p-4 border-t">
                                <p class="text-sm text-gray-500 mb-3">Pilih E-Wallet:</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <template x-for="provider in methods.ewallet.providers" :key="provider">
                                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                                               :class="{ 'border-green-500 bg-green-50': selectedProvider === provider }">
                                            <input type="radio" name="provider" :value="provider" x-model="selectedProvider" class="hidden">
                                            <span class="flex items-center">
                                                <span x-text="provider.charAt(0).toUpperCase() + provider.slice(1)" class="font-medium"></span>
                                            </span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Virtual Account --}}
                        <div class="border rounded-xl overflow-hidden">
                            <button 
                                type="button"
                                @click="selectedMethod = selectedMethod === 'va' ? null : 'va'; selectedProvider = null"
                                class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition"
                                :class="{ 'bg-green-50 border-green-500': selectedMethod === 'va' }">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">🏦</span>
                                    <span class="font-semibold text-gray-800">Virtual Account</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-500" :class="{ 'transform rotate-180': selectedMethod === 'va' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            {{-- Provider Virtual Account --}}
                            <div x-show="selectedMethod === 'va'" x-collapse class="p-4 border-t">
                                <p class="text-sm text-gray-500 mb-3">Pilih Bank:</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <template x-for="provider in methods.va.providers" :key="provider">
                                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                                               :class="{ 'border-green-500 bg-green-50': selectedProvider === provider }">
                                            <input type="radio" name="provider" :value="provider" x-model="selectedProvider" class="hidden">
                                            <span class="flex items-center">
                                                <span x-text="provider.toUpperCase()" class="font-medium"></span>
                                            </span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- QRIS --}}
                        <div class="border rounded-xl overflow-hidden">
                            <button 
                                type="button"
                                @click="selectedMethod = selectedMethod === 'qris' ? null : 'qris'; selectedProvider = 'qris'"
                                class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition"
                                :class="{ 'bg-green-50 border-green-500': selectedMethod === 'qris' }">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">📲</span>
                                    <span class="font-semibold text-gray-800">QRIS</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-500" :class="{ 'transform rotate-180': selectedMethod === 'qris' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            {{-- Konten QRIS --}}
                            <div x-show="selectedMethod === 'qris'" x-collapse class="p-4 border-t">
                                <div class="text-center p-4">
                                    <svg class="w-48 h-48 mx-auto mb-4 text-gray-700" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M5 5h4v4H5zM15 5h4v4h-4zM5 15h4v4H5zM15 15h4v4h-4zM9 9h6v6H9z"/>
                                    </svg>
                                    <p class="text-sm text-gray-600 mb-2">Scan QR dengan aplikasi pembayaran</p>
                                    <p class="text-xs text-gray-500">GoPay, OVO, Dana, ShopeePay, LinkAja</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Bayar (aktif hanya jika metode dan provider dipilih) --}}
                    <button 
                        type="submit" 
                        x-show="selectedMethod && (selectedMethod === 'qris' ? true : selectedProvider)"
                        x-transition
                        class="w-full mt-6 bg-green-600 text-white py-4 rounded-xl font-semibold text-lg transition-all duration-300 shadow-md hover:shadow-lg"
                        style="background: linear-gradient(135deg, #00bca4 0%, #00c954 100%);">
                        Bayar Sekarang - Rp {{ number_format($total, 0, ',', '.') }}
                    </button>

                    {{-- Pesan jika belum pilih metode --}}
                    <p x-show="!selectedMethod" class="text-center text-gray-500 mt-4">
                        Pilih metode pembayaran terlebih dahulu
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script untuk Alpine.js --}}
@push('scripts')
<script src="//unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
<script defer src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
@endsection