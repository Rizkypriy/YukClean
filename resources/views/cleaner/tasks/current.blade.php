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
        @if(isset($currentTask) && $currentTask)
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm mb-6">
            <div class="flex justify-between items-start mb-3">
                <h2 class="font-semibold text-gray-700">Tugas Aktif</h2>
                @php $badge = $currentTask->status_badge; @endphp
                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $badge[0] }} {{ $badge[1] }}">
                    {{ $badge[2] }}
                </span>
            </div>

            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-blue-600 font-bold text-xl">{{ $cleaner->initials ?? 'CS' }}</span>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-gray-800">{{ $currentTask->customer_name }}</h3>
                    <p class="text-sm text-gray-600">{{ $currentTask->service_name ?? 'Layanan Kebersihan' }}</p>
                </div>
            </div>

            <div class="space-y-3 text-sm mb-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-map-marker-alt text-gray-400 w-5 mt-0.5"></i>
                    <span class="text-gray-700">{{ $currentTask->address }}</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="far fa-calendar text-gray-400 w-5 mt-0.5"></i>
                    <span class="text-gray-700">
                        {{ \Carbon\Carbon::parse($currentTask->task_date)->format('d M Y') }}, 
                        {{ substr($currentTask->start_time, 0, 5) }} - {{ substr($currentTask->end_time, 0, 5) }}
                    </span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fas fa-phone text-gray-400 w-5 mt-0.5"></i>
                    <span class="text-gray-700">{{ $currentTask->customer_phone ?? 'Tidak ada nomor' }}</span>
                </div>
            </div>    
        </div>

        {{-- Status Update Steps --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm mb-6">
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
                    {{-- CHECK ICON --}}
                    <div class="flex-shrink-0">
                        @if(
                            $currentStatus == $key ||
                            ($key == 'on_the_way' && in_array($currentStatus, ['in_progress','completed'])) ||
                            ($key == 'in_progress' && $currentStatus == 'completed')
                        )
                        <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        @else
                        <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center">
                            <span class="text-gray-400 text-xs">{{ $loop->iteration }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="flex-1">
                        <p class="font-medium text-gray-800">{{ $step[0] }}</p>
                        <p class="text-sm text-gray-500">{{ $step[1] }}</p>

                        {{-- BUTTON LOGIC BERURUTAN --}}
                        @if(
                            ($key == 'on_the_way' && $currentStatus == 'assigned') ||
                            ($key == 'in_progress' && $currentStatus == 'on_the_way') ||
                            ($key == 'completed' && $currentStatus == 'in_progress')
                        )
                        <button 
                            class="btn-update-status mt-3 bg-gradient-to-r from-green-500 to-green-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition inline-flex items-center shadow-md"
                            data-task-id="{{ $currentTask->id }}"
                            data-next-status="{{ $key }}"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ $step[0] }}
                        </button>
                        @endif

                        {{-- PROGRESS SLIDER --}}
                        @if($currentStatus == 'in_progress' && $key == 'in_progress')
                        <div class="mt-3 p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-sm font-medium text-gray-700">Progress:</span>
                                <span class="text-sm font-bold text-green-600" id="progressValue">{{ $currentTask->progress ?? 50 }}%</span>
                            </div>
                            <input type="range" id="progressSlider" min="0" max="100" value="{{ $currentTask->progress ?? 50 }}" 
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-green-500">
                            <button 
                                class="btn-update-progress mt-3 bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-600 transition w-full"
                                data-task-id="{{ $currentTask->id }}"
                            >
                                Update Progress
                            </button>
                        </div>
                        @endif

                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Contact Customer --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm mt-4">
            <h2 class="font-semibold text-gray-700 mb-3">Kontak Pelanggan</h2>
            <div class="flex gap-3">
                <a href="tel:{{ $currentTask->customer_phone }}" class="flex-1 bg-blue-600 text-white py-3 rounded-lg text-sm font-medium hover:bg-blue-700 transition text-center">
                    <i class="fas fa-phone mr-2"></i> Telepon
                </a>
                <a href="https://wa.me/{{ $currentTask->customer_phone }}" target="_blank" class="flex-1 bg-green-600 text-white py-3 rounded-lg text-sm font-medium hover:bg-green-700 transition text-center">
                    <i class="fab fa-whatsapp mr-2"></i> WhatsApp
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    document.querySelectorAll('.btn-update-status').forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.dataset.taskId;
            const nextStatus = this.dataset.nextStatus;

            let confirmMessage = '';
            if (nextStatus === 'on_the_way') confirmMessage = 'Mulai menuju lokasi pelanggan?';
            if (nextStatus === 'in_progress') confirmMessage = 'Mulai proses pembersihan?';
            if (nextStatus === 'completed') confirmMessage = 'Tandai pekerjaan selesai?';

            if (!confirm(confirmMessage)) return;

            fetch(`/cleaner/tasks/${taskId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: nextStatus })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Status berhasil diperbarui');
                    location.reload(); // user tracking otomatis ikut update
                } else {
                    alert('Gagal update status');
                }
            });
        });
    });

    // Slider progress
    const slider = document.getElementById('progressSlider');
    const progressValue = document.getElementById('progressValue');

    if (slider) {
        slider.addEventListener('input', () => {
            progressValue.textContent = slider.value + '%';
        });
    }

    // Update progress
    document.querySelectorAll('.btn-update-progress').forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.dataset.taskId;
            const progress = slider.value;

            fetch(`/cleaner/tasks/${taskId}/progress`,  {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ progress })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) alert('Progress diperbarui');
            });
        });
    });
});
</script>
@endpush
@endsection
