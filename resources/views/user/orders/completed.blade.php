@extends('layouts.app')

@section('title', 'Pesanan Selesai')

@section('content')
<div class="min-h-screen bg-white flex flex-col items-center justify-center px-6 pb-24">
    {{-- Ilustrasi / Icon --}}
    <div class="w-32 h-32 bg-green-100 rounded-full flex items-center justify-center mb-6">
        <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>

    {{-- Pesan Terima Kasih --}}
    <h1 class="text-3xl font-bold text-gray-800 mb-2 text-center">
        Terima Kasih! 👋
    </h1>
    <h2 class="text-xl font-semibold text-green-600 mb-4 text-center">
        Pesanan Selesai
    </h2>
    
    <p class="text-gray-600 text-center mb-8 max-w-md">
        Terima kasih sudah memakai jasa Yuk Clean! 
        Kami senang membantu rumah Anda tetap bersih dan nyaman.
    </p>

    {{-- Rating Section --}}
<div class="w-full max-w-md bg-gray-50 rounded-xl p-6 mb-6">
    <p class="text-gray-700 text-center mb-3">Bagaimana pengalaman Anda?</p>
    
    {{-- Rating Stars dengan Alpine.js --}}
    <div x-data="{ rating: 0, hoverRating: 0 }" class="mb-4">
        <div class="flex justify-center gap-2">
            <template x-for="star in 5" :key="star">
                <button @mouseenter="hoverRating = star" 
                        @mouseleave="hoverRating = 0"
                        @click="rating = star"
                        class="focus:outline-none transition-transform hover:scale-110">
                    <svg class="w-10 h-10 transition-colors duration-200" 
                         :class="(hoverRating ? star <= hoverRating : star <= rating) ? 'text-yellow-400' : 'text-gray-300'"
                         fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                    </svg>
                </button>
            </template>
        </div>
        
        {{-- Tampilkan rating yang dipilih --}}
        <p x-show="rating > 0" class="text-center text-sm text-gray-600 mt-2" x-text="'Anda memberi rating ' + rating + ' bintang'"></p>
        
        {{-- Form Review (muncul setelah pilih rating) --}}
        <div x-show="rating > 0" x-transition class="mt-4">
            <textarea id="reviewText"
                      rows="3" 
                      class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-300 mb-3"
                      placeholder="Tulis ulasan Anda (opsional)"></textarea>

            {{-- Tombol Kirim Rating --}}
            <button @click="submitRating(rating)"
                    class="w-full bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700 transition">
                Kirim Rating
            </button>
        </div>
    </div>
</div>

    {{-- Tombol Aksi --}}
    <div class="w-full max-w-md space-y-3">
        <a href="{{ route('user.orders.create', $order->service_id ?? $order->bundle_id) }}" 
           class="block w-full bg-green-600 text-white text-center py-4 rounded-xl font-semibold text-lg hover:bg-green-700 transition">
            Pesan Lagi
        </a>
        
        <a href="{{ route('user.dashboard') }}" 
           class="block w-full border border-green-600 text-green-600 text-center py-4 rounded-xl font-semibold text-lg hover:bg-green-50 transition">
            Kembali ke Home
        </a>
    </div>
</div>

@push('scripts')
<script>
window.submitRating = function(rating) {
    const review = document.getElementById('reviewText')?.value || '';
    const button = event.currentTarget;
    const originalText = button.innerText;
    
    // Loading state
    button.disabled = true;
    button.innerHTML = '⏳ Mengirim...';
    
    fetch('{{ route("user.orders.rate", $order) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            rating: rating,
            review: review
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect ke halaman riwayat
            window.location.href = '{{ route("user.orders.index") }}';
        } else {
            alert('Gagal: ' + data.message);
            button.disabled = false;
            button.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
        button.disabled = false;
        button.innerHTML = originalText;
    });
};
</script>
@endpush
@endsection