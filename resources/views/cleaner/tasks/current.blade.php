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
                if (d.success) location.reload();
                else alert(d.message || 'Gagal update');
            });
        };
    });

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
