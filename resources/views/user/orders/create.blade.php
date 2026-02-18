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
                    <input type="time" name="start_time" 
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
        const form = document.getElementById('orderForm');
        const endTimeInput = document.getElementById('end_time');
        
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
        
        // Optional: Tambahkan loading state saat submit
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="opacity-0">Buat Pesanan</span><div class="absolute inset-0 flex items-center justify-center"><svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
        });

        // Optional: Validasi sederhana di client-side
        function validateForm() {
            const address = document.querySelector('textarea[name="address"]').value.trim();
            const customerName = document.querySelector('input[name="customer_name"]').value.trim();
            const customerPhone = document.querySelector('input[name="customer_phone"]').value.trim();
            const bookingDate = document.querySelector('input[name="booking_date"]').value;
            const startTime = document.querySelector('input[name="start_time"]').value;

            if (!address || !customerName || !customerPhone || !bookingDate || !startTime) {
                alert('Harap isi semua field yang wajib diisi');
                return false;
            }

            return true;
        }

        // Uncomment jika ingin menggunakan validasi client-side
        /*
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Buat Pesanan';
            }
        });
        */

        // Optional: Format nomor HP (hanya angka)
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