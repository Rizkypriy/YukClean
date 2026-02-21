@extends('layouts.app')

@section('title', 'Pesan Paket Bundling')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#f0fdf5] to-[#d3fcf2]">
    {{-- Header dengan Back Button --}}
    <div class="bg-white shadow-lg px-5 py-4 mb-10">
        <div class="grid grid-cols-[auto,1fr] gap-3">
            {{-- Kolom Kiri: Tombol Kembali --}}
            <div>
                <a href="{{ route('user.dashboard') }}" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition">
                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            </div>
            
            {{-- Kolom Kanan: Informasi Detail --}}
            <div>
                <h1 class="text-xl font-semibold text-black">Pesan Paket Bundling</h1>
                <p class="text-gray-500 text-sm">Pilih paket hemat untuk kebutuhan Anda</p>
            </div>
        </div>
    </div>

    {{-- Form Pemesanan --}}
    <form method="POST" action="{{ route('user.orders.store') }}" id="orderForm" class="pb-10">
        @csrf
        <input type="hidden" name="bundle_id" value="{{ $bundle->id }}">

        {{-- Bundle Info Card --}}
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl shadow-xl p-6 border border-[#cfcfcf] mx-auto w-[90%] md:w-[500px] mb-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-purple-200 rounded-xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="font-bold text-xl text-gray-800">{{ $bundle->name }}</h2>
                        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">-{{ $bundle->discount_percent }}%</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">{{ $bundle->description }}</p>
                    <div class="flex items-baseline gap-2 mt-2">
                        <span class="text-2xl font-bold text-green-600">{{ $bundle->formatted_price }}</span>
                        <span class="text-sm line-through opacity-75 text-gray-500">{{ $bundle->formatted_original_price }}</span>
                    </div>
                    <p class="text-xs text-green-600 mt-1">Hemat Rp {{ number_format($bundle->original_price - $bundle->price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Alamat Lengkap Card --}}
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-[#cfcfcf] mx-auto w-[90%] md:w-[500px] mb-4" style="min-height: 200px;">
            <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <span>📍</span> Alamat Lengkap
            </h3>
            <textarea name="address" rows="2" 
                class="w-full mt-6 h-32 px-4 py-3 rounded-xl border-0 bg-[#f3f3f5] {{ $errors->has('address') ? 'border-red-500' : '' }} focus:outline-none focus:ring-2 focus:ring-[#cfcfcf]"
                placeholder="Jl. Cendana No. 124, RT/RW Kukusan, Kecamatan, Kota"
                required>{{ old('address', Auth::user()->address) }}</textarea>
            @error('address')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Informasi Pemesan Card --}}
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-[#cfcfcf] mx-auto w-[90%] md:w-[500px] mb-4" style="min-height: 200px;">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span>👤</span> Informasi Pemesan
            </h3>

            <div class="space-y-4">
                <div>
                    <h5 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                        <span>👤</span> Nama Lengkap
                    </h5>
                    <input type="text" name="customer_name" 
                        class="w-full px-4 py-3 rounded-xl border-0 bg-[#f3f3f5] {{ $errors->has('customer_name') ? 'border-red-500' : '' }} focus:outline-none focus:ring-2 focus:ring-[#cfcfcf]"
                        placeholder="Nama Lengkap" value="{{ old('customer_name', Auth::user()->name) }}" required>
                    @error('customer_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <h5 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                        <span>📞</span> Nomor HP
                    </h5>
                    <input type="tel" name="customer_phone" 
                        class="w-full px-4 py-3 rounded-xl border-0 bg-[#f3f3f5] {{ $errors->has('customer_phone') ? 'border-red-500' : '' }} focus:outline-none focus:ring-2 focus:ring-[#cfcfcf]"
                        placeholder="Nomor HP" value="{{ old('customer_phone', Auth::user()->phone) }}" required>
                    @error('customer_phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Jadwal Booking Card --}}
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-[#cfcfcf] mx-auto w-[90%] md:w-[500px] mb-4" style="min-height: 200px;">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span>📅</span> Jadwal Booking
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs text-gray-500 mb-1 font-medium">Tanggal</label>
                    <input type="date" name="booking_date" 
                        class="w-full px-4 py-3 rounded-xl border-0 bg-[#f3f3f5] {{ $errors->has('booking_date') ? 'border-red-500' : '' }} focus:outline-none focus:ring-2 focus:ring-[#cfcfcf]"
                        min="{{ date('Y-m-d') }}" value="{{ old('booking_date') }}" required>
                    @error('booking_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1 font-medium">Jam Mulai</label>
                    <input type="time" name="start_time" id="start_time"
                        class="w-full px-4 py-3 rounded-xl border-0 bg-[#f3f3f5] {{ $errors->has('start_time') ? 'border-red-500' : '' }} focus:outline-none focus:ring-2 focus:ring-[#cfcfcf]"
                        value="{{ old('start_time') }}" required>
                    @error('start_time')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <input type="hidden" name="end_time" id="end_time" value="">
                </div>
            </div>
        </div>

        {{-- Promo Code Card --}}
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-[#cfcfcf] mx-auto w-[90%] md:w-[500px] mb-4">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span>🎫</span> Kode Promo
            </h3>
            <div class="flex gap-2">
                <input type="text" name="promo_code" id="promoCode" 
                    class="flex-1 px-4 py-3 rounded-xl border-0 bg-[#f3f3f5] focus:outline-none focus:ring-2 focus:ring-[#cfcfcf]"
                    placeholder="Masukkan kode promo" value="{{ old('promo_code') }}">
                <button type="button" id="checkPromoBtn" 
                    class="bg-gray-200 text-gray-700 px-6 py-3 rounded-xl hover:bg-gray-300 transition font-medium">
                    Cek
                </button>
            </div>
            <div id="promoMessage" class="mt-2 text-sm hidden"></div>
        </div>

        {{-- Catatan Card --}}
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-[#cfcfcf] mx-auto w-[90%] md:w-[500px] mb-4">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span>📝</span> Catatan (Opsional)
            </h3>
            <textarea name="notes" rows="2" 
                class="w-full px-4 py-3 rounded-xl border-0 bg-[#f3f3f5] focus:outline-none focus:ring-2 focus:ring-[#cfcfcf]"
                placeholder="Tambahkan catatan untuk petugas">{{ old('notes') }}</textarea>
        </div>

        {{-- Ringkasan Harga Card --}}
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-[#cfcfcf] mx-auto w-[90%] md:w-[500px] mb-4">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span>💰</span> Ringkasan Harga
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-medium" id="subtotalDisplay">{{ $bundle->formatted_price }}</span>
                </div>
                <div class="flex justify-between text-green-600" id="discountRow" style="display: none;">
                    <span>Diskon</span>
                    <span id="discountAmount">-Rp 0</span>
                </div>
                <div class="border-t border-gray-200 pt-2 mt-2">
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total</span>
                        <span class="text-green-600" id="totalDisplay">{{ $bundle->formatted_price }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol Submit --}}
        <div class="mx-auto w-[90%] md:w-[500px] mb-10">
            <button type="submit" 
                class="w-full text-white py-4 rounded-xl font-semibold text-lg transition-all duration-300 shadow-lg hover:shadow-xl active:scale-[0.98]"
                style="background: linear-gradient(135deg, #00bda2 0%, #00c85f 100%);">
                Buat Pesanan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const originalPrice = {{ $bundle->price }};
        const subtotalDisplay = document.getElementById('subtotalDisplay');
        const totalDisplay = document.getElementById('totalDisplay');
        const discountRow = document.getElementById('discountRow');
        const discountAmount = document.getElementById('discountAmount');
        const promoCode = document.getElementById('promoCode');
        const checkPromoBtn = document.getElementById('checkPromoBtn');
        const promoMessage = document.getElementById('promoMessage');
        
        // Hitung end_time otomatis
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        
        if (startTimeInput && endTimeInput) {
            startTimeInput.addEventListener('change', function() {
                if (this.value) {
                    const [hours, minutes] = this.value.split(':');
                    let endHour = parseInt(hours) + 2;
                    
                    if (endHour >= 24) {
                        endHour = endHour - 24;
                    }
                    
                    const endTime = `${endHour.toString().padStart(2, '0')}:${minutes}`;
                    endTimeInput.value = endTime;
                }
            });
            
            if (startTimeInput.value) {
                const event = new Event('change');
                startTimeInput.dispatchEvent(event);
            }
        }

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

            fetch('{{ route("user.orders.check-promo") }}', {
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
                
                // Auto hide after 5 seconds
                setTimeout(() => {
                    promoMessage.classList.add('hidden');
                }, 5000);
            })
            .catch(error => {
                promoMessage.className = 'mt-2 text-sm text-red-600';
                promoMessage.textContent = 'Terjadi kesalahan';
                promoMessage.classList.remove('hidden');
            });
        });

        // Format nomor HP (hanya angka)
        const phoneInput = document.querySelector('input[name="customer_phone"]');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }

        // Loading state saat submit
        const form = document.getElementById('orderForm');
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="opacity-0">Buat Pesanan</span><div class="absolute inset-0 flex items-center justify-center"><svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
        });
    });
</script>
@endpush
@endsection