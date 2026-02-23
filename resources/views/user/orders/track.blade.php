{{-- resources/views/user/orders/track.blade.php --}}
@extends('layouts.app')

@section('title', 'Lacak Pesanan')

@section('content')
{{-- Tambahkan ini di bagian head atau sebelum menutup body --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.0/echo.iife.js"></script>
<script src="https://js.pusher.com/8.2/pusher.min.js"></script>

{{-- 🔥 TAMBAHKAN: Leaflet CSS dan JS (OpenStreetMap gratis) --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

{{-- Style untuk map --}}
<style>
    #map {
        height: 300px;
        width: 100%;
        border-radius: 0.75rem;
        z-index: 1;
    }
    .leaflet-container {
        border-radius: 0.75rem;
    }
    .custom-marker {
        background: #00bda2;
        border: 3px solid white;
        border-radius: 50%;
        width: 40px !important;
        height: 40px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    .custom-marker i {
        color: white;
        font-size: 20px;
    }
</style>

<div class="min-h-screen bg-white pb-24">
    {{-- Header --}}
    <div class="rounded-b-2xl p-5 text-white shadow-lg relative overflow-hidden" style="background: linear-gradient(135deg, #00bda2 0%, #00c85f 100%);">
        <a href="{{ route('user.orders.index', $order) }}" class="inline-flex items-center text-white mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold">Lacak Pesanan</h1>
        <p class="text-sm opacity-90 mt-1">{{ $order->order_number }}</p>
    </div>

    <div class="p-5 space-y-4">
        {{-- Status Pesanan --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-700 mb-2">📦 Status Pesanan Anda</h2>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    @php
                        // Gunakan status dari task jika ada, atau dari order
                        $currentStatus = isset($cleanerTask) ? $cleanerTask->status : $order->status;
                        
                        $statusBadge = match($currentStatus) {
                            'pending' => ['bg-yellow-100', 'text-yellow-600', 'Menunggu Konfirmasi'],
                            'confirmed' => ['bg-blue-100', 'text-blue-600', 'Dikonfirmasi'],
                            'available' => ['bg-blue-100', 'text-blue-600', 'Mencari Petugas'],
                            'assigned' => ['bg-purple-100', 'text-purple-600', 'Petugas Ditugaskan'],
                            'on_the_way' => ['bg-yellow-100', 'text-yellow-600', 'Petugas Menuju Lokasi'],
                            'on_progress' => ['bg-green-100', 'text-green-600', 'Sedang Diproses'],
                            'in_progress' => ['bg-green-100', 'text-green-600', 'Sedang Dibersihkan'],
                            'completed' => ['bg-gray-100', 'text-gray-600', 'Selesai'],
                            'cancelled' => ['bg-red-100', 'text-red-600', 'Dibatalkan'],
                            default => ['bg-gray-100', 'text-gray-600', $currentStatus],
                        };
                    @endphp
                    <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-medium {{ $statusBadge[0] }} {{ $statusBadge[1] }}">
                        {{ $statusBadge[2] }}
                    </span>
                </div>
                
                {{-- Estimasi Kedatangan (hanya untuk on_the_way) --}}
                @if(isset($cleanerTask) && $cleanerTask->status === 'on_the_way')
                <div class="text-right">
                    <p class="text-sm text-gray-500">Estimasi Kedatangan</p>
                    <p class="text-lg font-bold text-green-600" id="eta">Menghitung...</p>
                </div>
                @endif
            </div>
        </div>

        {{-- 🔥 MAP SECTION dengan Leaflet (OpenStreetMap gratis) --}}
        @if(isset($cleanerTask) && in_array($cleanerTask->status, ['on_the_way', 'in_progress']))
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-700 mb-3">📍 Lokasi Petugas Real-time</h2>
            
            {{-- Container Map --}}
            <div id="map" class="w-full h-64 rounded-lg border border-gray-200 mb-3"></div>
            
            {{-- Info Lokasi --}}
            <div class="flex items-center justify-between text-sm bg-gray-50 p-3 rounded-lg">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-gray-600">Lokasi diperbarui:</span>
                    <span id="lastUpdate" class="font-medium text-gray-800">-</span>
                </div>
                <span id="accuracy" class="text-xs text-gray-500">Real-time</span>
            </div>
        </div>
        @endif

        {{-- Tracking Steps --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-700 mb-4">📍 Tracking Pesanan</h2>
            
            @php
                // Tentukan step berdasarkan status task atau order
                if (isset($cleanerTask)) {
                    $stepOrder = [
                        'confirmed' => 1,
                        'assigned' => 2,
                        'on_the_way' => 3,
                        'in_progress' => 4,
                        'completed' => 5
                    ];
                    $currentStep = $stepOrder[$cleanerTask->status] ?? 1;
                } else {
                    $stepOrder = [
                        'confirmed' => 1,
                        'on_progress' => 3,
                        'completed' => 5
                    ];
                    $currentStep = $stepOrder[$order->status] ?? 1;
                }
            @endphp

            <div class="space-y-4">
                {{-- Step 1: Pesanan Dikonfirmasi --}}
                <div class="flex items-start gap-3">
                    <div class="shrink-0">
                        @if($currentStep >= 1)
                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        @else
                            <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-gray-400 text-xs">1</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">Pesanan Dikonfirmasi</p>
                        <p class="text-sm text-gray-500">Pesanan Anda telah dikonfirmasi dan sedang diproses</p>
                        @if($currentStep >= 1)
                            <span class="inline-block mt-1 text-xs text-green-600">✓ Selesai</span>
                        @endif
                    </div>
                </div>

                {{-- Step 2: Petugas Ditugaskan --}}
                <div class="flex items-start gap-3">
                    <div class="shrink-0">
                        @if($currentStep >= 2)
                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        @else
                            <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-gray-400 text-xs">2</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">Petugas Ditugaskan</p>
                        <p class="text-sm text-gray-500">Petugas telah ditugaskan untuk pesanan Anda</p>
                        @if($currentStep >= 2)
                            <span class="inline-block mt-1 text-xs text-green-600">✓ Selesai</span>
                        @elseif($currentStep == 1)
                            <span class="inline-block mt-1 text-xs text-yellow-600">⏳ Menunggu petugas</span>
                        @endif
                    </div>
                </div>

                {{-- Step 3: Petugas Menuju Lokasi --}}
                <div class="flex items-start gap-3">
                    <div class="shrink-0">
                        @if($currentStep >= 3)
                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        @else
                            <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-gray-400 text-xs">3</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">Petugas Menuju Lokasi</p>
                        <p class="text-sm text-gray-500">Petugas sedang dalam perjalanan ke lokasi Anda</p>
                        @if($currentStep >= 3)
                            <span class="inline-block mt-1 text-xs text-green-600">✓ Selesai</span>
                        @elseif($currentStep == 2)
                            <span class="inline-block mt-1 text-xs text-yellow-600">⏳ Menunggu petugas berangkat</span>
                        @endif
                    </div>
                </div>

                {{-- Step 4: Pembersihan Dimulai --}}
                <div class="flex items-start gap-3">
                    <div class="shrink-0">
                        @if($currentStep >= 4)
                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        @else
                            <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-gray-400 text-xs">4</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">Pembersihan Dimulai</p>
                        <p class="text-sm text-gray-500">Petugas sedang membersihkan rumah Anda</p>
                        @if($currentStep >= 4)
                            <span class="inline-block mt-1 text-xs text-green-600">✓ Selesai</span>
                        @elseif($currentStep == 3)
                            <span class="inline-block mt-1 text-xs text-yellow-600">⏳ Menunggu pembersihan dimulai</span>
                        @endif
                    </div>
                </div>

                {{-- Step 5: Selesai --}}
                <div class="flex items-start gap-3">
                    <div class="shrink-0">
                        @if($currentStep >= 5)
                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        @else
                            <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-gray-400 text-xs">5</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">Pesanan Selesai</p>
                        <p class="text-sm text-gray-500">Pembersihan telah selesai. Terima kasih telah menggunakan Yuk Clean!</p>
                        @if($currentStep >= 5)
                            <span class="inline-block mt-1 text-xs text-green-600">✓ Selesai</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Informasi Petugas --}}
        @if(isset($cleanerTask) && $cleanerTask->cleaner)
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-700 mb-4">👤 Informasi Petugas</h2>
            
            <div class="flex items-center gap-4">
                {{-- Avatar Petugas --}}
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    @if($cleanerTask->cleaner->avatar)
                        <img src="{{ Storage::url($cleanerTask->cleaner->avatar) }}" alt="{{ $cleanerTask->cleaner->name }}" class="w-full h-full object-cover rounded-full">
                    @else
                        <span class="text-blue-600 font-bold text-xl">{{ $cleanerTask->cleaner->initials }}</span>
                    @endif
                </div>
                
                {{-- Detail Petugas --}}
                <div class="flex-1">
                    <h3 class="font-bold text-lg text-gray-800">{{ $cleanerTask->cleaner->name }}</h3>
                    <p class="text-sm text-gray-600">Petugas Profesional</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-star text-yellow-400 mr-1"></i> {{ number_format($cleanerTask->cleaner->rating, 1) }} • 
                        <i class="fas fa-check-circle text-green-500 mr-1"></i> {{ $cleanerTask->cleaner->total_tasks }} tugas
                    </p>
                    
                    {{-- Kontak Petugas --}}
                    <div class="flex gap-3 mt-3">
                        <a href="tel:{{ $cleanerTask->cleaner->phone }}" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            Telepon
                        </a>
                        <a href="https://wa.me/{{ $cleanerTask->cleaner->phone }}" target="_blank"
                           class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Informasi Tambahan --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-700 mb-3">📋 Detail Pesanan</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Layanan</span>
                    <span class="font-medium text-gray-800">{{ $order->service->name ?? $order->bundle->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal</span>
                    <span class="font-medium text-gray-800">
                        @if($order->order_date)
                            {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Jam</span>
                    <span class="font-medium text-gray-800">{{ substr($order->start_time, 0, 5) }} - {{ substr($order->end_time, 0, 5) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Alamat</span>
                    <span class="font-medium text-gray-800 text-right">{{ $order->address }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 🔥 SCRIPT untuk tracking real-time dengan Leaflet --}}
@push('scripts')
<script>
    // Data order dari Laravel
    const orderId = {{ $order->id }};
    const hasCleanerTask = {{ isset($cleanerTask) && in_array($cleanerTask->status, ['on_the_way', 'in_progress']) ? 'true' : 'false' }};
    
    // Variabel global untuk map
    let map;
    let marker;
    let lastLat = -6.200000; // Default (Jakarta)
    let lastLng = 106.816666;

    // Inisialisasi Leaflet Map
    function initMap() {
        if (!hasCleanerTask) return; // Hanya inisialisasi jika ada task aktif
        
        // Coba dapatkan lokasi awal dari localStorage
        const savedLat = localStorage.getItem('lastLat_' + orderId);
        const savedLng = localStorage.getItem('lastLng_' + orderId);
        
        if (savedLat && savedLng) {
            lastLat = parseFloat(savedLat);
            lastLng = parseFloat(savedLng);
        }

        // Inisialisasi peta
        map = L.map('map').setView([lastLat, lastLng], 15);

        // Tambahkan tile layer dari OpenStreetMap (GRATIS!)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Buat custom icon untuk marker
        const customIcon = L.divIcon({
            className: 'custom-marker',
            html: '<i class="fas fa-broom"></i>',
            iconSize: [40, 40],
            popupAnchor: [0, -20]
        });

        // Tambahkan marker
        marker = L.marker([lastLat, lastLng], { 
            icon: customIcon,
            draggable: false
        }).addTo(map).bindPopup('Posisi petugas saat ini').openPopup();

        // Update info awal
        document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString('id-ID');
        
        // Update ETA
        if (document.getElementById('eta')) {
            updateETA();
        }
    }

    // Update marker dan peta
    function updateLocation(lat, lng) {
        if (!map || !marker) return;
        
        const newPos = [parseFloat(lat), parseFloat(lng)];
        
        // Update marker
        marker.setLatLng(newPos);
        
        // Geser peta dengan animasi halus
        map.panTo(newPos);
        
        // Update info
        document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString('id-ID');
        
        // Simpan ke localStorage
        localStorage.setItem('lastLat_' + orderId, lat);
        localStorage.setItem('lastLng_' + orderId, lng);
        
        // Update popup
        marker.bindPopup('Posisi petugas diperbarui: ' + new Date().toLocaleTimeString('id-ID')).openPopup();
    }

    // Update estimasi waktu (simulasi sederhana)
    function updateETA() {
        // Dalam implementasi nyata, ini bisa menggunakan Google Distance Matrix API
        // Tapi untuk sekarang, kita buat simulasi
        const etaElement = document.getElementById('eta');
        if (etaElement) {
            const minutes = Math.floor(Math.random() * 15) + 5;
            etaElement.textContent = minutes + ' menit';
        }
    }

    // Update ETA setiap 30 detik
    setInterval(updateETA, 30000);

    // Inisialisasi Laravel Echo
    if (hasCleanerTask) {
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env("PUSHER_APP_KEY") }}',
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
            forceTLS: true
        });

        // Dengarkan event real-time
        Echo.channel(`order.${orderId}`)
            .listen('OrderLocationUpdated', (data) => {
                console.log('📍 Lokasi baru:', data);
                updateLocation(data.latitude, data.longitude);
                
                // Update estimasi kedatangan
                updateETA();
            });
    }

    // Inisialisasi map setelah halaman siap
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
    });
</script>

{{-- Auto refresh halaman setiap 30 detik untuk update status (fallback) --}}
<script>
    setTimeout(function() {
        window.location.reload();
    }, 30000);
</script>
@endpush
@endsection