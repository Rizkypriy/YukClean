{{-- resources/views/cleaner/dashboard/index.blade.php --}}
@extends('cleaner.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="pb-10 bg-white">
    {{-- Header --}}
    <div class="rounded-b-2xl p-6 text-white shadow-lg mx-auto"
        style="background:#00bda2 ">
        <div class="mb-3">
            <h1 class="text-2xl font-bold mb-2">Halo, {{ $cleaner->name }}</h1>
            <p>Selamat bekerja hari ini</p>
        </div>
        
    </div>
    {{-- Informasi Radius dan Status --}}
            <div class="border-t border-gray-100 pt-6">
                <div class="flex items-center justify-center gap-20">
                    {{-- Radius --}}
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Radius</p>
                            <p class="font-semibold text-gray-800">{{ $cleaner->radius_km ?? 5 }} km</p>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Status</p>
                            <p class="font-semibold text-green-600">{{ ucfirst($cleaner->status ?? 'available') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
    <div class="p-5">
        {{-- Current Task Card (if any) --}}
        <h1 class="font-bold mb-3 mt-3">Tugas Tersedia</h1>
        @if(isset($currentTask) && $currentTask)
        <div class="bg-white rounded-xl border border-green-100 p-5 mb-6 shadow-lg">
            <div class="flex justify-between items-start mb-3">
                <h2 class="font-semibold text-green-800">Tugas Aktif</h2>
                <span class="bg-[#00bda2]  text-white text-xs px-2 py-1 rounded-full">{{ $currentTask->status_badge[2] }}</span>
            </div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <span class="text-green-600 font-bold">{{ $cleaner->initials }}</span>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $currentTask->customer_name }}</h3>
                    <p class="text-xs text-gray-500">{{ Str::limit($currentTask->address, 50) }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                <span><i class="far fa-calendar mr-1"></i> {{ $currentTask->formatted_date }}</span>
                <span><i class="far fa-clock mr-1"></i> {{ $currentTask->formatted_time }}</span>
            </div>
            <a href="{{ route('cleaner.tasks.current') }}" 
               class="block w-full bg-[#00bda2] text-white text-center py-3 rounded-lg font-medium hover:bg-green-700 transition">
                Lihat Detail
            </a>
        </div>
        @endif

        {{-- Available Tasks --}}
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Tugas Tersedia</h2>
            
            <div class="space-y-3">
                @forelse($availableTasks as $task)
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-semibold text-gray-800">{{ $task->customer_name }}</h3>
                        <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full">
                            {{ $task->service_icon ?? '📋' }} {{ $task->service_name ?? $task->service_type ?? 'Regular' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">{{ Str::limit($task->address, 100) }}</p>
                    <div class="flex items-center gap-4 text-xs text-gray-500 mb-3">
                        <span><i class="fas fa-location-dot mr-1"></i> 
                            @if($task->distance_km)
                                {{ number_format($task->distance_km, 1) }} km
                            @else
                                < {{ $cleaner->radius_km }} km
                            @endif
                        </span>
                        <span><i class="far fa-calendar mr-1"></i> 
                            {{ \Carbon\Carbon::parse($task->task_date)->format('d M Y') }}, 
                            {{ substr($task->start_time, 0, 5) }}
                        </span>
                    </div>
                    <button onclick="acceptTask({{ $task->id }})" 
                        class="w-full border border-green-600 text-green-600 py-2 rounded-lg text-sm font-medium hover:bg-green-50 transition">
                        Ambil Tugas
                    </button>
                </div>
                @empty
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-gray-500">Tidak ada tugas tersedia</p>
                    <p class="text-xs text-gray-400 mt-1">Tugas baru akan muncul setelah user melakukan pembayaran</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Today's Schedule --}}
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Jadwal Hari Ini</h2>
            
            @forelse($todayTasks as $task)
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm mb-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 font-bold">{{ $cleaner->initials }}</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-800">{{ $task->customer_name }}</h4>
                        <p class="text-xs text-gray-500">{{ $task->service_name ?? $task->service_type }}</p>
                        <p class="text-xs text-gray-400">{{ $task->formatted_time }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full {{ $task->status_badge[0] }} {{ $task->status_badge[1] }}">
                        {{ $task->status_badge[2] }}
                    </span>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-500 py-4">Tidak ada jadwal untuk hari ini</p>
            @endforelse
        </div>

        {{-- Recent Activities --}}
        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Aktivitas Terakhir</h2>
            
            @forelse($recentTasks as $task)
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm mb-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-green-600 font-bold">{{ $cleaner->initials }}</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-800">{{ $task->customer_name }}</h4>
                        <p class="text-xs text-gray-500">{{ $task->service_name ?? $task->service_type }}</p>
                        <p class="text-xs text-gray-400">{{ $task->completed_at ? $task->completed_at->diffForHumans() : 'Selesai' }}</p>
                    </div>
                    <span class="text-green-600 text-sm"><i class="fas fa-check-circle"></i></span>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-500 py-4">Belum ada aktivitas</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
    function acceptTask(taskId) {
        if (!taskId) {
            alert('ID tugas tidak valid');
            return;
        }
        
        if (confirm('Ambil tugas ini?')) {
            fetch(`/cleaner/tasks/${taskId}/accept`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('✅ Tugas berhasil diambil!');
                    window.location.reload();
                } else {
                    alert('❌ Gagal: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Terjadi kesalahan saat mengambil tugas. Silakan coba lagi.');
            });
        }
    }
</script>
@endpush
@endsection