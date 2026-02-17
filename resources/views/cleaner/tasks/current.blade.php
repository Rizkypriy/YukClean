{{-- resources/views/cleaner/tasks/current.blade.php --}}
@extends('cleaner.layouts.app')

@section('title', 'Status Pekerjaan')

@section('content')
<div class="min-h-screen bg-white pb-24">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 text-white">
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
                    <span class="text-gray-700">{{ \Carbon\Carbon::parse($currentTask->task_date)->format('d M Y') }}, {{ substr($currentTask->start_time, 0, 5) }} - {{ substr($currentTask->end_time, 0, 5) }}</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fas fa-phone text-gray-400 w-5 mt-0.5"></i>
                    <span class="text-gray-700">{{ $currentTask->customer_phone ?? 'Tidak ada nomor' }}</span>
                </div>
            </div>

            {{-- Status Update Steps --}}
            <div class="space-y-4 mt-6">
                @php
                    $steps = [
                        'on_the_way' => ['Menuju Lokasi', 'Perjalanan menuju lokasi pelanggan'],
                        'in_progress' => ['Sedang Membersihkan', 'Proses pembersihan sedang berlangsung'],
                        'completed' => ['Selesaikan Pekerjaan', 'Tandai pekerjaan selesai']
                    ];
                    $currentStatus = $currentTask->status;
                @endphp

                @foreach($steps as $key => $step)
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        @if(in_array($key, ['on_the_way', 'in_progress', 'completed']) && 
                            ($currentStatus == $key || 
                             ($key == 'on_the_way' && in_array($currentStatus, ['in_progress', 'completed'])) ||
                             ($key == 'in_progress' && $currentStatus == 'completed')))
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
                          {{-- TOMBOL YANG BISA DIKLIK --}}
            @if($currentStatus == $key && $key != 'completed')
                <button onclick="updateTaskStatus('{{ $key == 'on_the_way' ? 'in_progress' : 'completed' }}')" 
                    class="mt-3 bg-green-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ $key == 'on_the_way' ? 'Sampai di Lokasi' : 'Selesaikan Pekerjaan' }}
                </button>
            @endif
        </div>
    </div>
    @endforeach
</div>

        {{-- Contact Customer --}}
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
        const taskId = {{ $currentTask->id ?? 0 }};
        if (!taskId) return;
        
        // Konfirmasi sebelum update
        let confirmMessage = '';
        if (status === 'in_progress') {
            confirmMessage = 'Apakah Anda sudah sampai di lokasi dan siap memulai pekerjaan?';
        } else if (status === 'completed') {
            confirmMessage = 'Apakah Anda yakin ingin menandai pekerjaan ini sebagai selesai?';
        }
        
        if (!confirm(confirmMessage)) return;
        
        // Tampilkan loading
        const btn = event.target;
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 inline" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg> Memproses...';    

      fetch(`/cleaner/tasks/${taskId}/status`, {
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
                // Tampilkan notifikasi sukses
                showNotification('✅ Status berhasil diperbarui!', 'success');
                
                // Redirect ke halaman yang sesuai
                if (status === 'completed') {
                    setTimeout(() => {
                        window.location.href = '{{ route("cleaner.dashboard") }}';
                    }, 2000);
                } else {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            } else {
                btn.disabled = false;
                btn.innerHTML = originalText;
                showNotification('❌ Gagal: ' + (data.message || 'Terjadi kesalahan'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btn.disabled = false;
            btn.innerHTML = originalText;
            showNotification('❌ Terjadi kesalahan saat memperbarui status', 'error');
        });
    }
    
    // Fungsi untuk menampilkan notifikasi
    function showNotification(message, type) {
        // Hapus notifikasi sebelumnya jika ada
        const existingNotification = document.querySelector('.custom-notification');
        if (existingNotification) existingNotification.remove();
        
        // Buat elemen notifikasi
        const notification = document.createElement('div');
        notification.className = `custom-notification fixed top-4 right-4 z-50 max-w-md p-4 rounded-lg shadow-lg alert ${
            type === 'success' ? 'bg-green-100 border-l-4 border-green-600 text-green-700' : 'bg-red-100 border-l-4 border-red-600 text-red-700'
        }`;
        notification.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 ${type === 'success' ? 'text-green-600' : 'text-red-600'}" fill="currentColor" viewBox="0 0 20 20">
                        ${type === 'success' 
                            ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
                            : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>'
                        }
                    </svg>
                    <span>${message}</span>
                </div>
                <button onclick="this.closest('.custom-notification').remove()" class="ml-4 ${type === 'success' ? 'text-green-700' : 'text-red-700'} hover:opacity-75">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto hide setelah 5 detik
        setTimeout(() => {
            if (notification.parentNode) notification.remove();
        }, 5000);
    }
</script>
@endpush
@endsection