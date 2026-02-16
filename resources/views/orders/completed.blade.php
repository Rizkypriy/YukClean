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
        
        <div class="flex justify-center gap-2 mb-4" x-data="{ rating: 0 }">
            <template x-for="star in 5" :key="star">
                <button @click="rating = star" class="focus:outline-none">
                    <svg class="w-10 h-10 transition-colors duration-200" 
                         :class="star <= rating ? 'text-yellow-400' : 'text-gray-300'"
                         fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                    </svg>
                </button>
            </template>
        </div>

        {{-- Form Review (opsional) --}}
        <textarea x-show="rating > 0" x-transition
                  rows="3" 
                  class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-300 mb-3"
                  placeholder="Tulis ulasan Anda (opsional)"></textarea>

        {{-- Tombol Kirim Rating --}}
        <button x-show="rating > 0" 
                @click="alert('Terima kasih atas rating ' + rating + ' bintang!')"
                class="w-full bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700 transition">
            Kirim Rating
        </button>
    </div>

    {{-- Tombol Aksi --}}
    <div class="w-full max-w-md space-y-3">
        <a href="{{ route('home') }}" 
           class="block w-full bg-green-600 text-white text-center py-4 rounded-xl font-semibold text-lg hover:bg-green-700 transition">
            Pesan Lagi
        </a>
        
        <a href="{{ route('home') }}" 
           class="block w-full border border-green-600 text-green-600 text-center py-4 rounded-xl font-semibold text-lg hover:bg-green-50 transition">
            Kembali ke Home
        </a>
    </div>
</div>

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
@endpush
@endsection