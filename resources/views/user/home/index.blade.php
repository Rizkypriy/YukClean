{{-- resources/views/home/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="min-h-screen py-4 md:py-8" style="background-color: #e8fdf3;">
    {{-- Container untuk desktop dengan card --}}
    <div class="desktop-container mx-auto" style="max-width: 100%;">
        {{-- Card Utama untuk Desktop --}}
        <div class="bg-white md:rounded-2xl md:shadow-xl overflow-hidden">
            
            {{-- Header dengan Welcome --}}
            <div class="rounded-b-2xl md:rounded-none p-5 text-white shadow-lg relative overflow-hidden"
                style="background: linear-gradient(135deg, #00bda2 0%, #00c85f 100%);">
                {{-- Header dengan Logo dan Yuk Clean --}}
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2">
                        {{-- Logo --}}
                        <img src="{{ asset('img/logo.png') }}" alt="Yuk Clean Logo" class="w-8 h-8">
                        <h1 class="text-xl font-bold">Yuk Clean</h1>
                    </div>
                    <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>

                {{-- Welcome Message --}}
                <div class="mb-6 text-center md:text-left">
                    <h2 class="text-xl font-semibold">Halo, Selamat Datang 👋</h2>
                    <p class="text-sm opacity-90 mt-1">Pilih layanan kebersihan yang Anda butuhkan</p>
                </div>

                {{-- Search Bar --}}
                <div class="relative search-wrapper" style="max-width: 100%;">
                    <svg class="w-5 h-5 text-gray-400 absolute left-4 top-3.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="searchInput" placeholder="Cari layanan kebersihan..."
                        class="w-full pl-12 pr-4 py-3 rounded-lg text-gray-900 placeholder-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-green-300">
                </div>
            </div>

            {{-- Konten dalam Card --}}
            <div class="p-5 md:p-8" style="background-color: #e8fdf3;">
                {{-- Promo Section dengan Background Gradient yang Cantik --}}
                <div class="space-y-4 promo-container">
                    @forelse($promos as $promo)
                    <div class="rounded-2xl p-3 text-white shadow-lg relative overflow-hidden promo-item"
                        style="background: {{ $promo->background_color }};">
                        {{-- Pattern/Texture --}}
                        <div class="absolute top-0 right-0 w-28 h-28 bg-white opacity-10 rounded-full -mr-8 -mt-8"></div>
                        <div class="absolute bottom-0 left-0 w-20 h-20 bg-white opacity-10 rounded-full -ml-6 -mb-6"></div>

                        <div class="relative">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-base mt-1">{{ $promo->title }}</p>
                                <span class="text-2xl">{{ $promo->icon ?? '🏷️' }}</span>
                            </div>
                            <p class="text-xs opacity-90 mt-1">{{ $promo->description }}</p>
                        </div>
                    </div>
                    @empty
                    {{-- Tampilkan promo default jika tidak ada di database --}}
                    <div class="rounded-2xl p-3 text-white shadow-lg relative overflow-hidden promo-item"
                        style="background: linear-gradient(135deg, #be79ff 0%, #645fff 100%);">
                        <div class="absolute top-0 right-0 w-28 h-28 bg-white opacity-10 rounded-full -mr-8 -mt-8"></div>
                        <div class="absolute bottom-0 left-0 w-20 h-20 bg-white opacity-10 rounded-full -ml-6 -mb-6"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-base mt-1">Diskon 20% Pengguna Baru!</p>
                                <span class="text-2xl">🏷️</span>
                            </div>
                            <p class="text-xs opacity-90 mt-1">Untuk pemesanan pertama Anda</p>
                        </div>
                    </div>
                    <div class="rounded-2xl p-3 text-white shadow-lg relative overflow-hidden promo-item"
                        style="background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);">
                        <div class="absolute top-0 right-0 w-28 h-28 bg-white opacity-10 rounded-full -mr-8 -mt-8"></div>
                        <div class="absolute bottom-0 left-0 w-20 h-20 bg-white opacity-10 rounded-full -ml-6 -mb-6"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-base mt-1">Promo Bundling Rumah!</p>
                                <span class="text-2xl">🎁</span>
                            </div>
                            <p class="text-xs opacity-90 mt-1.5">Hemat hingga 30% untuk paket lengkap</p>
                        </div>
                    </div>
                    @endforelse
                </div>

                {{-- Layanan Kebersihan Section dengan Grid --}}
                <div class="mt-10">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Layanan Kebersihan</h2>

                    {{-- Grid 2 kolom dengan tinggi yang sama --}}
                    <div class="grid grid-cols-2 gap-4 services-grid" id="servicesContainer">
                        @forelse($services as $service)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition flex flex-col h-full service-item service-card">
                            {{-- Icon dengan warna hijau konsisten --}}
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-3 shrink-0 mx-auto">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $service->icon_path }}" />
                                </svg>
                            </div>
                            
                            <div class="flex-1 flex flex-col">
                                <div class="text-center">
                                    <h3 class="font-semibold text-gray-800">{{ $service->name }}</h3>
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $service->description }}</p>
                                </div>
                                <p class="text-green-600 font-bold text-sm mt-auto text-center">{{ $service->formatted_price }}</p>
                            </div>
                            
                            <a href="{{ route('user.orders.create', $service) }}" 
                               class="w-full text-white py-2.5 rounded-lg text-xs font-medium transition-all duration-300 mt-4 shadow-md hover:shadow-lg text-center block order-button"
                               style="background: linear-gradient(135deg, #00bca4 0%, #00c954 100%);"
                               onmouseover="this.style.background='linear-gradient(135deg, #00a08b 0%, #00b045 100%)'"
                               onmouseout="this.style.background='linear-gradient(135deg, #00bca4 0%, #00c954 100%)'">
                                Pesan
                            </a>
                        </div>
                        @empty
                        <div class="col-span-2 text-center py-8">
                            <p class="text-gray-500">Belum ada layanan tersedia</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Paket Bundling Hemat --}}
                @if(isset($bundles) && $bundles->isNotEmpty())
                <div class="mt-8 mb-24 md:mb-8">
                    <h2 class="text-lg font-semibold mb-3">Paket Bundling Hemat</h2>

                    {{-- Horizontal Scroll Container --}}
                    <div class="overflow-x-auto pb-4 -mx-5 px-5 scrollbar-hide bundles-wrapper">
                        <div class="flex gap-4 bundles-container" style="min-width: min-content;">
                            @foreach($bundles as $bundle)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition flex flex-col h-full bundle-card"
                                style="width: 280px;">
                                <div class="flex flex-col h-full">
                                    <div class="flex items-start justify-between mb-4">
                                        <h2 class="font-bold text-xl text-black leading-tight">{{ $bundle->name }}</h2>
                                        <span class="{{ $bundle->badge_color ?? 'bg-red-500' }} text-white text-xs px-2 py-1 rounded-full whitespace-nowrap">
                                            -{{ $bundle->discount_percent }}%
                                        </span>
                                    </div>
                                    <div class="flex flex-col items-end mt-auto">
                                        <span class="text-2xl font-bold text-green-600">{{ $bundle->formatted_price }}</span>
                                        <span class="text-sm line-through opacity-75 text-black">{{ $bundle->formatted_original_price }}</span>
                                    </div>
                                    <a href="{{ route('user.orders.create.bundle', $bundle) }}" 
                                       class="w-full text-white py-2.5 rounded-lg text-xs font-medium transition-all duration-300 mt-4 shadow-md hover:shadow-lg text-center block bundle-button"
                                       style="background: linear-gradient(135deg, #00bca4 0%, #00c954 100%);"
                                       onmouseover="this.style.background='linear-gradient(135deg, #00a08b 0%, #00b045 100%)'"
                                       onmouseout="this.style.background='linear-gradient(135deg, #00bca4 0%, #00c954 100%)'">
                                        Ambil Paket
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    /* Global/Specific Styles */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 32px;
    }

    .hover\:shadow-md {
        transition: box-shadow 0.2s ease-in-out;
    }

    button, a {
        transition: all 0.2s ease-in-out;
    }

    button:active, a:active {
        transform: scale(0.98);
    }

    /* Desktop Styles - Applied when width > 768px */
    @media (min-width: 768px) {
        .desktop-container {
            max-width: 1200px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            padding-left: 24px !important;
            padding-right: 24px !important;
        }

        /* Card styling untuk desktop */
        .desktop-container > div {
            border-radius: 24px !important;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1), 0 10px 20px -5px rgba(0, 0, 0, 0.05) !important;
        }

        /* Header dalam card */
        .desktop-container > div > div:first-child {
            border-radius: 24px 24px 0 0 !important;
        }

        /* Welcome message di-center */
        .desktop-container > div > div:first-child .text-center {
            text-align: center !important;
        }

        /* Make promo section grid on desktop */
        .promo-container {
            display: grid !important;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)) !important;
            gap: 20px !important;
        }

        .promo-item {
            margin-bottom: 0 !important;
        }

        /* Make services grid 4 columns on desktop */
        .services-grid {
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 20px !important;
        }

        .service-card {
            height: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease !important;
        }

        .service-card:hover {
            transform: translateY(-4px) !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }

        /* Center the bundles section */
        .bundles-wrapper {
            max-width: 100% !important;
            overflow-x: visible !important;
        }

        .bundles-container {
            justify-content: center !important;
            flex-wrap: wrap !important;
            gap: 20px !important;
        }

        .bundle-card {
            width: calc(33.333% - 14px) !important;
            min-width: 280px !important;
            flex: 0 1 auto !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease !important;
        }

        .bundle-card:hover {
            transform: translateY(-4px) !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }

        /* Adjust search bar width */
        .search-wrapper {
            max-width: 600px !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        /* Order buttons */
        .order-button, .bundle-button {
            padding: 12px !important;
            font-size: 14px !important;
        }
    }

    /* Large desktop screens */
    @media (min-width: 1024px) {
        .services-grid {
            grid-template-columns: repeat(4, 1fr) !important;
        }

        .bundle-card {
            width: calc(25% - 15px) !important;
        }
    }

    /* Extra large screens */
    @media (min-width: 1280px) {
        .desktop-container {
            max-width: 1400px !important;
        }
        
        .services-grid {
            grid-template-columns: repeat(5, 1fr) !important;
        }
    }

    /* Mobile styles remain exactly the same */
    @media (max-width: 767px) {
        .desktop-container {
            max-width: 100% !important;
            padding: 0 !important;
        }

        /* Hapus background putih di mobile */
        .desktop-container > div {
            background-color: transparent !important;
            box-shadow: none !important;
        }

        .promo-container {
            display: block !important;
        }

        .services-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }

        .bundles-wrapper {
            overflow-x: auto !important;
        }

        .bundles-container {
            flex-wrap: nowrap !important;
        }

        .bundle-card {
            width: 280px !important;
            flex-shrink: 0 !important;
        }

        /* Hapus padding di mobile */
        .desktop-container > div > div:last-child {
            padding: 1.25rem !important;
        }
    }
</style>

@push('scripts')
<script>
    // Search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                let query = this.value.toLowerCase().trim();
                let items = document.querySelectorAll('.service-item');
                let container = document.getElementById('servicesContainer');
                let visibleCount = 0;
                
                // Hapus pesan "tidak ditemukan" sebelumnya jika ada
                let existingMessage = document.getElementById('noResultMessage');
                if (existingMessage) {
                    existingMessage.remove();
                }
                
                // Loop setiap item layanan
                items.forEach(function(item) {
                    let title = item.querySelector('h3').textContent.toLowerCase();
                    if (title.includes(query) || query === '') {
                        item.style.display = 'flex';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Tampilkan pesan jika tidak ada hasil
                if (visibleCount === 0 && query !== '') {
                    let noResult = document.createElement('div');
                    noResult.id = 'noResultMessage';
                    noResult.className = 'col-span-2 text-center py-8';
                    noResult.innerHTML = '<p class="text-gray-500">Layanan "<span class="font-semibold">' + 
                                        query + '</span>" tidak ditemukan</p>';
                    container.appendChild(noResult);
                }
            });
        }
    });
</script>
@endpush
@endsection