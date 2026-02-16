{{-- resources/views/cleaner/tasks/current.blade.php --}}
@extends('cleaner.layouts.app')

@section('title', 'Status Pekerjaan')

@section('content')
<div class="min-h-screen bg-white pb-24">
    {{-- Header --}}
    <div class="bg-linear-to-r from-green-500 to-green-600 p-6 text-white">
        <a href="{{ route('cleaner.dashboard') }}" class="inline-flex items-center text-white mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold">Status Pekerjaan</h1>
    </div>

    <div class="p-5">
        @if($currentTask)
        {{-- Task Info Card --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm mb-6">
            <h2 class="font-semibold text-gray-700 mb-4">Tugas Aktif</h2>
            
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-blue-600 font-bold text-xl">{{ $currentTask->cleaner->initials }}</span>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-gray-800">{{ $currentTask->customer_name }}</h3>
                    <p class="text-sm text-gray-600">{{ $currentTask->service_type }}</p>
                </div>
            </div>

            <div class="space-y-3 text-sm mb-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-location-dot text-gray-400 w-5"></i>
                    <span class="text-gray-700">{{ $currentTask->address }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="far fa-calendar text-gray-400 w-5"></i>
                    <span class="text-gray-700">{{ $currentTask->formatted_date }}, {{ $currentTask->formatted_time }} WIB</span>
                </div>
            </div>

            {{-- Status Update Steps --}}
            <div class="space-y-4 mt-6">
                {{-- Step 1: Menuju Lokasi --}}
                <div class="flex items-start gap-3">
                    <div class="shrink-0">
                        @if(in_array($currentTask->status, ['on_the_way', 'in_progress', 'completed']))
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
                        <p class="font-medium text-gray-800">Menuju Lokasi</p>
                        <p class="text-sm text-gray-500">Perjalanan menuju lokasi pelanggan</p>
                        @if($currentTask->status === 'on_the_way')
                        <button onclick="updateStatus('in_progress')" 
                            class="mt-2 bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition">
                            Sampai di Lokasi
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Step 2: Sedang Membersihkan --}}
                <div class="flex items-start gap-3">
                    <div class="shrink-0">
                        @if($currentTask->status === 'in_progress')
                            <div class="w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        @elseif($currentTask->status === 'completed')
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
                        <p class="font-medium text-gray-800">Sedang Membersihkan</p>
                        <p class="text-sm text-gray-500">Proses pembersihan sedang berlangsung</p>
                        @if($currentTask->status === 'in_progress')
                        <button onclick="updateStatus('completed')" 
                            class="mt-2 bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition">
                            Selesaikan Pekerjaan
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Step 3: Selesai --}}
                <div class="flex items-start gap-3">
                    <div class="shrink-0">
                        @if($currentTask->status === 'completed')
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
                        <p class="font-medium text-gray-800">Selesai</p>
                        <p class="text-sm text-gray-500">Tandai pekerjaan selesai</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Customer Contact --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-700 mb-3">Kontak Pelanggan</h2>
            <div class="flex gap-3">
                <a href="tel:{{ $currentTask->customer_phone }}" 
                   class="flex-1 bg-blue-600 text-white py-3 rounded-lg text-sm font-medium hover:bg-blue-700 transition text-center">
                    <i class="fas fa-phone mr-2"></i> Telepon
                </a>
                <a href="https://wa.me/{{ $currentTask->customer_phone }}" target="_blank"
                   class="flex-1 bg-green-600 text-white py-3 rounded-lg text-sm font-medium hover:bg-green-700 transition text-center">
                    <i class="fab fa-whatsapp mr-2"></i> WhatsApp
                </a>
            </div>
        </div>
        @else
        <div class="text-center py-12">
            <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-500">Tidak ada tugas aktif</p>
            <a href="{{ route('cleaner.dashboard') }}" class="inline-block mt-4 text-green-600 font-medium">Cari tugas</a>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function updateStatus(status) {
        fetch('{{ route("cleaner.tasks.update-status", $currentTask) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
</script>
@endpush
@endsection