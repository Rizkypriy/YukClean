{{-- resources/views/cleaner/dashboard/index.blade.php --}}
@extends('cleaner.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="pb-24 bg-white">
    {{-- Header --}}
    <div class="bg-linear-to-r from-green-500 to-green-600 p-6 text-white">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold">Halo, {{ $cleaner->name }}</h1>
                <p class="text-sm opacity-90 mt-1">Selamat bekerja hari ini</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="bg-white/20 px-3 py-1 rounded-full text-sm">
                    <i class="fas fa-location-dot mr-1"></i> {{ $cleaner->radius_km }} km
                </span>
                <span class="bg-green-400 text-white px-3 py-1 rounded-full text-sm">
                    <i class="fas fa-circle mr-1"></i> {{ $cleaner->status_badge[2] }}
                </span>
            </div>
        </div>
    </div>

    <div class="p-5">
        {{-- Current Task Card (if any) --}}
        @if($currentTask)
        <div class="bg-green-50 rounded-xl border border-green-200 p-5 mb-6">
            <div class="flex justify-between items-start mb-3">
                <h2 class="font-semibold text-green-800">Tugas Aktif</h2>
                <span class="bg-green-600 text-white text-xs px-2 py-1 rounded-full">{{ $currentTask->status_badge[2] }}</span>
            </div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <span class="text-green-600 font-bold">{{ $currentTask->cleaner->initials }}</span>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $currentTask->customer_name }}</h3>
                    <p class="text-xs text-gray-500">{{ $currentTask->address }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                <span><i class="far fa-calendar mr-1"></i> {{ $currentTask->formatted_date }}</span>
                <span><i class="far fa-clock mr-1"></i> {{ $currentTask->formatted_time }}</span>
            </div>
            <a href="{{ route('cleaner.tasks.current') }}" 
               class="block w-full bg-green-600 text-white text-center py-3 rounded-lg font-medium hover:bg-green-700 transition">
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
                            {{ $task->task_type_icon ?? '📋' }} {{ ucfirst(str_replace('_', ' ', $task->task_type)) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">{{ $task->service_type }}</p>
                    <div class="flex items-center gap-4 text-xs text-gray-500 mb-3">
                        <span><i class="fas fa-location-dot mr-1"></i> {{ number_format($task->distance_km, 1) }} km</span>
                        <span><i class="far fa-calendar mr-1"></i> {{ $task->task_date->format('d M Y') }}, {{ $task->start_time }}</span>
                    </div>
                    <button onclick="acceptTask({{ $task->id }})" 
                        class="w-full border border-green-600 text-green-600 py-2 rounded-lg text-sm font-medium hover:bg-green-50 transition">
                        Lihat Detail
                    </button>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">Tidak ada tugas tersedia di sekitar Anda</p>
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
                        <span class="text-blue-600 font-bold">{{ $task->cleaner->initials }}</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-800">{{ $task->customer_name }}</h4>
                        <p class="text-xs text-gray-500">{{ $task->service_type }}</p>
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
                        <span class="text-green-600 font-bold">{{ $task->cleaner->initials }}</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-800">{{ $task->customer_name }}</h4>
                        <p class="text-xs text-gray-500">{{ $task->service_type }}</p>
                        <p class="text-xs text-gray-400">{{ $task->completed_at->diffForHumans() }}</p>
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
        if (confirm('Ambil tugas ini?')) {
            fetch(`/cleaner/tasks/${taskId}/accept`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => {
                if (response.ok) {
                    window.location.reload();
                }
            });
        }
    }
</script>
@endpush
@endsection