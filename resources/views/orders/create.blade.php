@extends('layouts.app')

@section('title', 'Pesan Layanan')

@section('content')
<div class="min-h-screen bg-white pb-24">
    {{-- Header --}}
    <div class="bg-linear-to-r from-green-500 to-green-600 p-6 text-white">
        <a href="{{ route('home') }}" class="inline-flex items-center text-white mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold">Detail Pemesanan</h1>
    </div>

    <div class="p-5">
        {{-- Service Info Card --}}
        <div class="bg-green-50 rounded-xl p-5 mb-6 border border-green-100">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $service->icon_path }}" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800">{{ $service->name }}</h2>
                    <p class="text-sm text-gray-600">{{ $service->description }}</p>
                    <p class="text-green-600 font-bold text-lg mt-1">{{ $service->formatted_price }}</p>
                </div>
            </div>
        </div>

        {{-- Form Pemesanan --}}
        <form method="POST" action="{{ route('orders.store') }}" id="orderForm">
            @csrf
            <input type="hidden" name="service_id" value="{{ $service->id }}">

            {{-- Alamat Lengkap --}}
            <div class="mb-6">
                <h3 class="font-semibold text-gray-700 mb-3">📍 Alamat Lengkap</h3>
                <textarea name="address" rows="3" 
                class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-300 {{ $errors->has('address') ? 'border-red-500' : 'border-gray-200' }}"
                placeholder="Jl. Contoh No. 123, RT/RW, Kelurahan, Kecamatan, Kota"
                required>{{ old('address', Auth::user()->address) }}</textarea>
                @error('address')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Detail Rumah --}}
            <div class="mb-6">
                <h3 class="font-semibold text-gray-700 mb-3">🏠 Detail Rumah</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <input type="number" name="floor_count" 
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-300"
                            placeholder="Jumlah lantai" value="{{ old('floor_count') }}">
                    </div>
                    <div>
                        <input type="text" name="room_size" 
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-300"
                            placeholder="Ukuran ruangan" value="{{ old('room_size') }}">
                    </div>
                </div>
                <textarea name="special_conditions" rows="2" 
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-300 mt-3"
                    placeholder="Kondisi khusus (opsional)">{{ old('special_conditions') }}</textarea>
            </div>

            {{-- Informasi Pemesan --}}
            <div class="mb-6">
                <h3 class="font-semibold text-gray-700 mb-3">👤 Informasi Pemesan</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Nama Lengkap</label>
                        <input type="text" name="customer_name" 
                        class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-300 {{ $errors->has('customer_name') ? 'border-red-500' : 'border-gray-200' }}"
                        placeholder="Masukkan nama lengkap" value="{{ old('customer_name', Auth::user()->name) }}" required>
                        @error('customer_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Nomor HP</label>
                        <input type="tel" name="customer_phone" 
                            class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-300 {{ $errors->has('customer_phone') ? 'border-red-500' : 'border-gray-200' }}"
                            placeholder="08xxxxxxxxxx" value="{{ old('customer_phone', Auth::user()->phone) }}" required>
                        @error('customer_phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Jadwal Booking --}}
            <div class="mb-6">
                <h3 class="font-semibold text-gray-700 mb-3">📅 Jadwal Booking</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Tanggal</label>
                        <input type="date" name="booking_date" 
                            class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-300 {{ $errors->has('booking_date') ? 'border-red-500' : 'border-gray-200' }}"
                            min="{{ date('Y-m-d') }}" value="{{ old('booking_date') }}" required>
                            @error('booking_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Jam Mulai</label>
                       <input type="time" name="start_time" 
                        class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-300 {{ $errors->has('start_time') ? 'border-red-500' : 'border-gray-200' }}"
                        value="{{ old('start_time') }}" required>
                        @error('start_time')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Jam Selesai</label>
                       <input type="time" name="end_time" 
                            class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-300 {{ $errors->has('end_time') ? 'border-red-500' : 'border-gray-200' }}"
                            value="{{ old('end_time') }}" required>
                        @error('end_time')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Promo Code --}}
            <div class="mb-6">
                <h3 class="font-semibold text-gray-700 mb-3">🎫 Kode Promo</h3>
                <div class="flex gap-2">
                    <input type="text" name="promo_code" id="promoCode" 
                        class="flex-1 px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-300"
                        placeholder="Masukkan kode promo" value="{{ old('promo_code') }}">
                    <button type="button" id="checkPromoBtn" 
                        class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition font-medium">
                        Cek
                    </button>
                </div>
                <div id="promoMessage" class="mt-2 text-sm hidden"></div>
            </div>

            {{-- Catatan --}}
            <div class="mb-6">
                <h3 class="font-semibold text-gray-700 mb-3">📝 Catatan (Opsional)</h3>
                <textarea name="notes" rows="2" 
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-300"
                    placeholder="Tambahkan catatan untuk petugas">{{ old('notes') }}</textarea>
            </div>

            {{-- Ringkasan Harga --}}
            <div class="bg-gray-50 rounded-xl p-5 mb-6">
                <h3 class="font-semibold text-gray-700 mb-3">💰 Ringkasan Harga</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium" id="subtotalDisplay">{{ $service->formatted_price }}</span>
                    </div>
                    <div class="flex justify-between text-green-600" id="discountRow" style="display: none;">
                        <span>Diskon</span>
                        <span id="discountAmount">-Rp 0</span>
                    </div>
                    <div class="border-t border-gray-200 pt-2 mt-2">
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span class="text-green-600" id="totalDisplay">{{ $service->formatted_price }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Submit --}}
            <button type="submit" 
                class="w-full text-white py-4 rounded-xl font-semibold text-lg transition-all duration-300 shadow-md hover:shadow-lg"
                style="background: linear-gradient(135deg, #00bca4 0%, #00c954 100%);">
                Buat Pesanan
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const originalPrice = {{ $service->price }};
        const subtotalDisplay = document.getElementById('subtotalDisplay');
        const totalDisplay = document.getElementById('totalDisplay');
        const discountRow = document.getElementById('discountRow');
        const discountAmount = document.getElementById('discountAmount');
        const promoCode = document.getElementById('promoCode');
        const checkPromoBtn = document.getElementById('checkPromoBtn');
        const promoMessage = document.getElementById('promoMessage');

        function formatRupiah(amount) {
            return 'Rp ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function updatePrice(discount = 0) {
            const total = originalPrice - discount;
            totalDisplay.textContent = formatRupiah(total);
            if (discount > 0) {
                discountRow.style.display = 'flex';
                discountAmount.textContent = '- ' + formatRupiah(discount);
            } else {
                discountRow.style.display = 'none';
            }
        }

        checkPromoBtn.addEventListener('click', function() {
            const code = promoCode.value.trim();
            
            if (!code) {
                promoMessage.className = 'mt-2 text-sm text-red-600';
                promoMessage.textContent = 'Masukkan kode promo';
                promoMessage.classList.remove('hidden');
                return;
            }

            fetch('{{ route("orders.check-promo") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    code: code,
                    subtotal: originalPrice
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    promoMessage.className = 'mt-2 text-sm text-green-600';
                    promoMessage.textContent = 'Kode promo valid!';
                    updatePrice(data.discount);
                } else {
                    promoMessage.className = 'mt-2 text-sm text-red-600';
                    promoMessage.textContent = data.message;
                    updatePrice(0);
                }
                promoMessage.classList.remove('hidden');
            })
            .catch(error => {
                promoMessage.className = 'mt-2 text-sm text-red-600';
                promoMessage.textContent = 'Terjadi kesalahan';
                promoMessage.classList.remove('hidden');
            });
        });
    });
</script>
@endpush
@endsection