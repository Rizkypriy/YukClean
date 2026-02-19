{{-- resources/views/user/orders/completed.blade.php --}}
@extends('layouts.app')

@section('title', 'Pesanan Selesai')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#f0fdf5] to-[#d3fcf2] flex flex-col items-center justify-center p-6">
    <div class="bg-white rounded-3xl shadow-xl p-8 max-w-md w-full text-center">
        {{-- Icon Sukses --}}
        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        {{-- Judul --}}
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Terima Kasih! 😍</h1>
        <h2 class="text-xl font-semibold text-green-600 mb-4">Pesanan Selesai</h2>
        
        {{-- Pesan Terima Kasih --}}
        <p class="text-gray-600 mb-6">
            Terima kasih sudah memakai jasa Yuk Clean!<br>
            Kami senang membantu rumah Anda tetap bersih dan nyaman.
        </p>

        {{-- Rating --}}
        <div class="mb-8">
            <p class="text-sm text-gray-500 mb-3">Bagaimana pengalaman Anda?</p>
            <div class="flex justify-center gap-2 text-3xl" id="ratingStars">
                <span class="cursor-pointer hover:scale-110 transition star" data-rating="1">⭐</span>
                <span class="cursor-pointer hover:scale-110 transition star" data-rating="2">⭐</span>
                <span class="cursor-pointer hover:scale-110 transition star" data-rating="3">⭐</span>
                <span class="cursor-pointer hover:scale-110 transition star" data-rating="4">⭐</span>
                <span class="cursor-pointer hover:scale-110 transition star" data-rating="5">⭐</span>
            </div>
            <p class="text-xs text-gray-400 mt-2">Klik bintang untuk memberikan rating</p>
        </div>

        {{-- Tombol Aksi --}}
        <div class="space-y-3">
            <a href="{{ route('user.orders.create', $order->service_id ?? $order->bundle_id) }}" 
               class="block w-full text-white py-3 rounded-xl font-semibold transition-all duration-300 shadow-md hover:shadow-lg"
               style="background: linear-gradient(135deg, #00bda2 0%, #00c85f 100%);">
                Pesan Lagi
            </a>
            
            <a href="{{ route('user.dashboard') }}" 
               class="block w-full bg-gray-100 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-200 transition">
                Kembali ke Home
            </a>
        </div>

        {{-- Form Rating Tersembunyi --}}
        <form id="ratingForm" method="POST" action="{{ route('user.orders.rate', $order) }}" class="hidden">
            @csrf
            <input type="hidden" name="rating" id="selectedRating" value="">
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Handle rating stars
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('selectedRating');
    const ratingForm = document.getElementById('ratingForm');

    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.dataset.rating;
            ratingInput.value = rating;
            
            // Update tampilan bintang
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('opacity-100');
                    s.classList.remove('opacity-30');
                } else {
                    s.classList.add('opacity-30');
                    s.classList.remove('opacity-100');
                }
            });

            // Submit form rating (opsional)
            // ratingForm.submit();
            
            // Atau tampilkan notifikasi
            alert('Terima kasih atas rating ' + rating + ' bintang!');
        });
    });
</script>
@endpush
@endsection