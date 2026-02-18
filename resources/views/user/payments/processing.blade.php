@extends('layouts.app')

@section('title', 'Mencari Petugas')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#f0fdf5] to-[#d3fcf2] flex flex-col items-center justify-center p-6">
    {{-- Animasi Loading --}}
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full text-center">
        {{-- Icon Animasi --}}
        <div class="relative w-24 h-24 mx-auto mb-6">
            <div class="absolute inset-0 border-4 border-green-200 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-t-green-500 rounded-full animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        {{-- Status Text --}}
        <h2 class="text-2xl font-bold text-gray-800 mb-3">Petugas Sedang Dicari...</h2>
        <p class="text-gray-600 mb-8">Sistem sedang memilih petugas terbaik untuk Anda secara acak</p>

        {{-- Progress Bar --}}
        <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
            <div class="bg-green-500 h-2 rounded-full animate-pulse" style="width: 60%"></div>
        </div>

        {{-- Tombol Lihat Status (akan muncul setelah redirect) --}}
        <div class="mt-8 hidden" id="trackingButton">
            <a href="{{ route('user.orders.track', $order) }}" 
               class="inline-block w-full text-white py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl"
               style="background: linear-gradient(135deg, #00bda2 0%, #00c85f 100%);">
                Lihat Status Pemesanan
            </a>
        </div>
    </div>
</div>

<script>
    // Simulasi pencarian petugas (redirect setelah beberapa detik)
    setTimeout(function() {
        // Sembunyikan animasi loading
        document.querySelector('.animate-spin').style.display = 'none';
        document.querySelector('.animate-pulse').style.display = 'none';
        
        // Tampilkan pesan sukses
        document.querySelector('h2').textContent = 'Petugas Ditemukan!';
        document.querySelector('p').textContent = 'Petugas akan segera menuju lokasi Anda';
        
        // Tampilkan tombol lihat status
        document.getElementById('trackingButton').classList.remove('hidden');
        
        // Redirect otomatis ke halaman tracking setelah 3 detik
        setTimeout(function() {
            window.location.href = "{{ route('user.orders.track', $order) }}";
        }, 3000);
        
    }, 3000); // 3 detik simulasi pencarian
</script>
@endsection