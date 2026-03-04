@extends('layouts.app')

@section('title', 'Pesan Layanan')

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
                <h1 class="text-xl font-semibold text-black">Detail Pemesanan</h1>
                <p class="text-gray-500 text-sm">Cleaning Ruangan</p>
            </div>
        </div>
    </div>

    {{-- Form Pemesanan --}}
    <form method="POST" action="{{ route('user.orders.store') }}" id="orderForm" class="pb-10">
        @csrf
        <input type="hidden" name="service_id" value="{{ $service->id }}">
        {{-- TAMBAHKAN INPUT HIDDEN UNTUK MENYIMPAN HARGA SERVICE --}}
        <input type="hidden" id="servicePrice" value="{{ $service->price }}">

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

        {{-- Detail Rumah Card --}}
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-[#cfcfcf] mx-auto w-[90%] md:w-[500px] mb-4" style="min-height: 200px;">
            <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <span>🏠</span> Detail Rumah
            </h3>
            <textarea name="special_conditions" rows="2" 
                class="w-full mt-6 h-32 px-4 py-3 rounded-xl border-0 bg-[#f3f3f5] focus:outline-none focus:ring-2 focus:ring-[#cfcfcf]"
                placeholder="Jumlah lantai, ukuran ruangan, kondisi khusus, dll">{{ old('special_conditions') }}</textarea>
        </div>

        {{-- Informasi Pemesan Card --}}
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-[#cfcfcf] mx-auto w-[90%] md:w-[500px] mb-4" style="min-height: 200px;">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                Informasi Pemesan
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
                    {{-- TAMBAHKAN INPUT HIDDEN UNTUK END_TIME --}}
                    <input type="hidden" name="end_time" id="end_time" value="">
                </div>
            </div>
        </div>

        {{-- TAMBAHKAN PROMO CODE CARD --}}
        {{-- Promo Code Card --}}
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-[#cfcfcf] mx-auto w-[90%] md:w-[500px] mb-4 service-card">
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

        {{-- TAMBAHKAN RINGKASAN HARGA CARD --}}
        {{-- Ringkasan Harga Card --}}
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-[#cfcfcf] mx-auto w-[90%] md:w-[500px] mb-4 service-card">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span>💰</span> Ringkasan Harga
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-medium" id="subtotalDisplay">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-green-600" id="discountRow" style="display: none;">
                    <span>Diskon</span>
                    <span id="discountAmount">-Rp 0</span>
                </div>
                <div class="border-t border-gray-200 pt-2 mt-2">
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total</span>
                        <span class="text-green-600" id="totalDisplay">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
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

{{-- Tambahkan style untuk mode desktop --}}
<style>
    /* Desktop Styles - Applied when width > 768px */
    @media (min-width: 768px) {
        .min-h-screen {
            padding: 2rem !important;
            background-color: #e8fdf3 !important;
            background-image: none !important;
        }

        .desktop-wrapper {
            max-width: 1200px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            padding-left: 24px !important;
            padding-right: 24px !important;
        }

        /* Card styling untuk desktop */
        .desktop-wrapper > .bg-white {
            border-radius: 24px !important;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1), 0 10px 20px -5px rgba(0, 0, 0, 0.05) !important;
        }

        /* Header dalam card */
        .desktop-wrapper > .bg-white > div:first-child {
            border-radius: 24px 24px 0 0 !important;
        }

        /* Konten dalam card */
        .desktop-wrapper > .bg-white > div:last-child {
            padding: 2rem !important;
        }

        /* Card hover effect untuk desktop */
        .bg-white.rounded-2xl.shadow-xl {
            transition: transform 0.2s ease, box-shadow 0.2s ease !important;
        }

        .bg-white.rounded-2xl.shadow-xl:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 5px 10px -5px rgba(0, 0, 0, 0.05) !important;
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

{{-- Script untuk membungkus konten dalam wrapper desktop --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.innerWidth >= 768) {
            const container = document.querySelector('.min-h-screen');
            const content = container.innerHTML;
            
            // Buat wrapper desktop
            container.innerHTML = `
                <div class="desktop-wrapper">
                    <div class="bg-white md:rounded-2xl md:shadow-xl overflow-hidden">
                        ${content}
                    </div>
                </div>
            `;
        }
    });
</script>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const form = document.getElementById('orderForm');
        
        // TAMBAHKAN VARIABEL UNTUK PROMO
        const originalPrice = {{ $service->price }};
        const subtotalDisplay = document.getElementById('subtotalDisplay');
        const totalDisplay = document.getElementById('totalDisplay');
        const discountRow = document.getElementById('discountRow');
        const discountAmount = document.getElementById('discountAmount');
        const promoCode = document.getElementById('promoCode');
        const checkPromoBtn = document.getElementById('checkPromoBtn');
        const promoMessage = document.getElementById('promoMessage');
        
        // Hitung end_time otomatis saat start_time dipilih
        if (startTimeInput && endTimeInput) {
            startTimeInput.addEventListener('change', function() {
                if (this.value) {
                    // Tambah 2 jam dari start_time
                    const [hours, minutes] = this.value.split(':');
                    let endHour = parseInt(hours) + 2;
                    
                    // Format ulang jam (00-23)
                    if (endHour >= 24) {
                        endHour = endHour - 24;
                    }
                    
                    const endTime = `${endHour.toString().padStart(2, '0')}:${minutes}`;
                    endTimeInput.value = endTime;
                    
                    console.log('Start time:', this.value, 'End time:', endTime);
                }
            });
            
            // Trigger sekali jika ada value awal
            if (startTimeInput.value) {
                const event = new Event('change');
                startTimeInput.dispatchEvent(event);
            }
        }

        // TAMBAHKAN FUNGSI FORMAT RUPIAH
        function formatRupiah(amount) {
            return 'Rp ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // TAMBAHKAN FUNGSI UPDATE PRICE
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

        // TAMBAHKAN EVENT LISTENER UNTUK CEK PROMO
        if (checkPromoBtn) {
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
        }
        
        // Loading state saat submit
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="opacity-0">Buat Pesanan</span><div class="absolute inset-0 flex items-center justify-center"><svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
        });

        // Format nomor HP (hanya angka)
        const phoneInput = document.querySelector('input[name="customer_phone"]');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }

        console.log('Form pemesanan siap digunakan');
    });
</script>
@endpush
@endsection