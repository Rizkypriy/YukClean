{{-- resources/views/cleaner/tasks/current.blade.php --}}
@extends('cleaner.layouts.app')
@section('title', 'Status Pekerjaan')

@section('content')
<div class="min-h-screen bg-white pb-24">

    {{-- Header --}}
    <div class="bg-[#00bda2] p-6 text-white">
        <a href="{{ route('cleaner.dashboard') }}" class="inline-flex items-center text-white mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Dashboard
        </a>
        <h1 class="text-2xl font-bold">Status Pekerjaan</h1>
    </div>

    <div class="p-5">

        {{-- ❗ Jika tidak ada task --}}
        @if(empty($currentTask))
            <div class="text-center py-10">
                <h2 class="text-lg font-semibold text-gray-700">Tidak ada tugas aktif</h2>
                <p class="text-gray-500 mt-2">Silakan ambil tugas dari dashboard.</p>
                <a href="{{ route('cleaner.dashboard') }}"
                   class="inline-block mt-4 bg-[#00bda2] text-white px-5 py-2 rounded-lg">
                    Kembali ke Dashboard
                </a>
            </div>
        @else

        @php
            $cleaner = auth('cleaner')->user();
            $badge = $currentTask->status_badge;
            $phone = preg_replace('/^0/', '62', $currentTask->customer_phone);
        @endphp

        {{-- CARD TASK --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-lg mb-6">
            <div class="flex justify-between items-start mb-3">
                <h2 class="font-semibold text-gray-700">Tugas Aktif</h2>
                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $badge[0] }} {{ $badge[1] }}">
                    {{ $badge[2] }}
                </span>
            </div>

            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-blue-600 font-bold text-xl">
                        {{ strtoupper(substr($cleaner->name ?? 'CS',0,2)) }}
                    </span>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-gray-800">{{ $currentTask->customer_name }}</h3>
                    <p class="text-sm text-gray-600">{{ $currentTask->service_name ?? 'Layanan Kebersihan' }}</p>
                </div>
            </div>

            <div class="space-y-3 text-sm mb-4">
                <div class="flex items-start gap-3">
                    <span class="text-gray-700">{{ $currentTask->address }}</span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-gray-700">
                        {{ \Carbon\Carbon::parse($currentTask->task_date)->format('d M Y') }},
                        {{ substr($currentTask->start_time, 0, 5) }} - {{ substr($currentTask->end_time, 0, 5) }}
                    </span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-gray-700">{{ $currentTask->customer_phone ?? 'Tidak ada nomor' }}</span>
                </div>
            </div>
        </div>

        {{-- 🔥 TAMBAHKAN: CARD TRACKING REAL-TIME (hanya muncul saat status on_the_way atau in_progress) --}}
        @if(in_array($currentTask->status, ['on_the_way', 'in_progress']))
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-lg mb-6">
            <h2 class="font-semibold text-gray-700 mb-3">📍 Tracking Real-time</h2>
            
            {{-- Status tracking --}}
            <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm text-gray-600">Lokasi Anda sedang dikirim ke pelanggan</span>
                </div>
                <span id="trackingStatus" class="text-xs text-gray-500">Aktif</span>
            </div>

            {{-- Tombol kontrol tracking --}}
            <div class="flex gap-2">
                <button id="startTrackingBtn" 
                        class="flex-1 bg-[#00bda2] text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-80 transition {{ $currentTask->status == 'on_the_way' ? '' : 'hidden' }}">
                    <i class="fas fa-play mr-2"></i>Mulai Tracking
                </button>
                <button id="stopTrackingBtn" 
                        class="flex-1 bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-80 transition hidden">
                    <i class="fas fa-stop mr-2"></i>Hentikan Tracking
                </button>
                <button id="refreshLocationBtn"
                        class="w-10 h-10 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition flex items-center justify-center">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>

            {{-- Info lokasi terkini --}}
            <div class="mt-3 text-xs text-gray-500 text-center" id="locationInfo">
                Menunggu untuk memulai tracking...
            </div>
        </div>
        @endif

        {{-- UPDATE STATUS --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-lg mb-6">
            <h2 class="font-semibold text-gray-700">Update status pekerjaan</h2>

            @php
            $steps = [
                'on_the_way' => ['Menuju Lokasi', 'Perjalanan menuju lokasi pelanggan'],
                'in_progress' => ['Sedang Membersihkan', 'Proses pembersihan sedang berlangsung'],
                'completed' => ['Pekerjaan Selesai', 'Tandai pekerjaan selesai']
            ];
            $currentStatus = $currentTask->status;
            @endphp

            <div class="space-y-4 mt-6">
                @foreach($steps as $key => $step)
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        @if(
                            $currentStatus == $key ||
                            ($key == 'on_the_way' && in_array($currentStatus, ['in_progress','completed'])) ||
                            ($key == 'in_progress' && $currentStatus == 'completed')
                        )
                        <div class="w-6 h-6 bg-[#00bda2] rounded-full flex items-center justify-center text-white">✓</div>
                        @else
                        <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center text-xs">
                            {{ $loop->iteration }}
                        </div>
                        @endif
                    </div>

                    <div class="flex-1">
                        <p class="font-medium text-gray-800">{{ $step[0] }}</p>
                        <p class="text-sm text-gray-500">{{ $step[1] }}</p>

                        {{-- BUTTON STATUS --}}
                        @if(
                            ($key == 'on_the_way' && $currentStatus == 'assigned') ||
                            ($key == 'in_progress' && $currentStatus == 'on_the_way') ||
                            ($key == 'completed' && $currentStatus == 'in_progress')
                        )
                        <button
                            class="btn-update-status mt-3 bg-[#00bda2] text-white px-5 py-2 rounded-lg text-sm"
                            data-task-id="{{ $currentTask->id }}"
                            data-next-status="{{ $key }}">
                            {{ $step[0] }}
                        </button>
                        @endif

                        {{-- PROGRESS --}}
                        @if($currentStatus == 'in_progress' && $key == 'in_progress')
                        <div class="mt-3 p-4 bg-gray-50 rounded-lg">
                            <div class="flex justify-between text-sm mb-2">
                                <span>Progress</span>
                                <span id="progressValue">{{ $currentTask->progress ?? 0 }}%</span>
                            </div>
                            <input type="range" id="progressSlider" min="0" max="100"
                                value="{{ $currentTask->progress ?? 0 }}"
                                class="w-full accent-[#00bda2]">
                            <button class="btn-update-progress mt-3 bg-[#00bda2] text-white px-4 py-2 rounded-lg w-full"
                                    data-task-id="{{ $currentTask->id }}">
                                Update Progress
                            </button>
                        </div>
                        @endif

                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- KONTAK --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-lg mt-4">
            <h2 class="font-semibold text-gray-700 mb-3">Kontak Pelanggan</h2>
            <div class="flex gap-3">
                <a href="tel:{{ $currentTask->customer_phone }}"
                   class="flex-1 bg-[#00bda2] text-white py-3 rounded-lg text-center hover:opacity-80 shadow-lg">
                    Telepon
                </a>
                <a href="https://wa.me/{{ $phone }}" target="_blank"
                   class="flex-1 border border-[#00bda2] text-[#00bda2] py-3 rounded-lg text-center hover:opacity-80 shadow-lg">
                    WhatsApp
                </a>
            </div>
        </div>

        @endif
    </div>
</div>

{{-- SCRIPT --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const taskId = {{ $currentTask->id ?? 0 }};
    const currentStatus = '{{ $currentTask->status ?? '' }}';
    
    // ===========================================
    // 🔥 TRACKING REAL-TIME
    // ===========================================
    let trackingInterval;
    let isTracking = false;
    
    const startBtn = document.getElementById('startTrackingBtn');
    const stopBtn = document.getElementById('stopTrackingBtn');
    const refreshBtn = document.getElementById('refreshLocationBtn');
    const locationInfo = document.getElementById('locationInfo');
    const trackingStatus = document.getElementById('trackingStatus');

    // Fungsi untuk mendapatkan lokasi
    function getCurrentLocation() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error('Geolocation tidak didukung'));
                return;
            }
            
            navigator.geolocation.getCurrentPosition(
                position => resolve(position),
                error => reject(error),
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    }

    // Fungsi untuk mengirim lokasi ke server
    async function sendLocationToServer() {
        try {
            const position = await getCurrentLocation();
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            const response = await fetch(`/cleaner/tasks/${taskId}/update-location`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ 
                    latitude: lat, 
                    longitude: lng 
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                locationInfo.innerHTML = `
                    <i class="fas fa-check-circle text-green-500 mr-1"></i>
                    Lokasi terkirim: ${new Date().toLocaleTimeString()}
                `;
                return true;
            } else {
                throw new Error(data.message || 'Gagal mengirim lokasi');
            }
        } catch (error) {
            console.error('❌ Error:', error);
            locationInfo.innerHTML = `
                <i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i>
                Gagal: ${error.message}
            `;
            return false;
        }
    }

    // Mulai tracking
    function startTracking() {
        if (isTracking) return;
        
        isTracking = true;
        trackingInterval = setInterval(sendLocationToServer, 10000); // Kirim setiap 10 detik
        
        // Kirim lokasi pertama segera
        sendLocationToServer();
        
        // Update UI
        if (startBtn) startBtn.classList.add('hidden');
        if (stopBtn) stopBtn.classList.remove('hidden');
        if (trackingStatus) {
            trackingStatus.innerHTML = '🟢 Tracking Aktif';
            trackingStatus.classList.add('text-green-600');
        }
        
        // Simpan status tracking di localStorage
        localStorage.setItem('tracking_task_' + taskId, 'active');
    }

    // Hentikan tracking
    function stopTracking() {
        if (!isTracking) return;
        
        isTracking = false;
        clearInterval(trackingInterval);
        
        // Update UI
        if (startBtn) startBtn.classList.remove('hidden');
        if (stopBtn) stopBtn.classList.add('hidden');
        if (trackingStatus) {
            trackingStatus.innerHTML = '⏸️ Tracking Berhenti';
            trackingStatus.classList.remove('text-green-600');
            trackingStatus.classList.add('text-gray-500');
        }
        if (locationInfo) locationInfo.innerHTML = 'Tracking dihentikan sementara';
        
        // Hapus dari localStorage
        localStorage.removeItem('tracking_task_' + taskId);
    }

    // Refresh lokasi manual
    async function refreshLocation() {
        if (refreshBtn) {
            refreshBtn.disabled = true;
            refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }
        
        await sendLocationToServer();
        
        if (refreshBtn) {
            refreshBtn.disabled = false;
            refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i>';
        }
    }

    // Cek status tracking sebelumnya
    function checkPreviousTracking() {
        const wasTracking = localStorage.getItem('tracking_task_' + taskId);
        if (wasTracking === 'active' && (currentStatus === 'on_the_way' || currentStatus === 'in_progress')) {
            startTracking();
        }
    }

    // Event listeners untuk tracking
    if (startBtn) {
        startBtn.addEventListener('click', startTracking);
    }
    
    if (stopBtn) {
        stopBtn.addEventListener('click', stopTracking);
    }
    
    if (refreshBtn) {
        refreshBtn.addEventListener('click', refreshLocation);
    }

    // Auto-start jika status sesuai
    if (currentStatus === 'on_the_way' || currentStatus === 'in_progress') {
        checkPreviousTracking();
    }

    // Hentikan tracking saat halaman ditutup
    window.addEventListener('beforeunload', function() {
        if (isTracking) {
            clearInterval(trackingInterval);
        }
    });

    // ===========================================
    // UPDATE STATUS
    // ===========================================
    document.querySelectorAll('.btn-update-status').forEach(btn => {
        btn.onclick = () => {
            const id = btn.dataset.taskId;
            const status = btn.dataset.nextStatus;

            if (!confirm('Update status?')) return;

            fetch(`/cleaner/tasks/${id}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status })
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    // Jika status berubah, handle tracking
                    if (status === 'on_the_way') {
                        startTracking();
                    } else if (status === 'completed') {
                        stopTracking();
                    }
                    location.reload();
                } else alert(d.message || 'Gagal update');
            });
        };
    });

    // ===========================================
    // PROGRESS
    // ===========================================
    const slider = document.getElementById('progressSlider');
    const progressText = document.getElementById('progressValue');

    if (slider) {
        slider.oninput = () => progressText.innerText = slider.value + '%';
    }

    document.querySelectorAll('.btn-update-progress').forEach(btn => {
        btn.onclick = () => {
            fetch(`/cleaner/tasks/${btn.dataset.taskId}/progress`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ progress: slider.value })
            })
            .then(r => r.json())
            .then(d => alert(d.message));
        };
    });
});
</script>
@endpush
@endsection