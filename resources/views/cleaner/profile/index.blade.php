{{-- resources/views/cleaner/profile/index.blade.php --}}
@extends('cleaner.layouts.app')

@section('title', 'Profil Petugas')

@section('content')
<div class="pb-24 bg-white">
    {{-- Header --}}
  <div class="rounded-b-2xl p-6 text-white shadow-lg mx-auto"
        style="background:#00bda2 ">
        <div class="flex mb-4">
            <h1 class="text-2xl font-bold">Profil Petugas</h1>
        </div>
        
        <div class="bg-white text-black rounded-3xl shadow-lg p-6 border border-gray-100 min-w-[300px] max-w-[600px] mx-auto">
                <div class="grid grid-cols-[auto,1fr] gap-4 items-center">
                    {{-- Foto Profil --}}
                    <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-500 rounded-full flex items-center justify-center text-white text-2xl font-bold shrink-0">
                        @if($cleaner->avatar)
                            <img src="{{ asset('storage/'.$cleaner->avatar) }}" alt="Profile" class="w-full h-full rounded-full object-cover">
                        @else
                            {{-- Inisial dari nama --}}
                            {{ substr($cleaner->name, 0, 1) }}{{ substr(strstr($cleaner->name, ' ', true) ?: $cleaner->name, 1, 1) ?? '' }}
                        @endif
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">{{ $cleaner->name }}</h1>
                        <p>{{ $cleaner->gender }}</p>
                        @php
                            $rating = $cleaner->rating ?? 5.0;
                            $fullStars = floor($rating);
                            $halfStar = ($rating - $fullStars) >= 0.5;
                            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                        @endphp

                        <div class="flex items-center gap-1">
                            {{-- Bintang Penuh --}}
                            @for($i = 1; $i <= $fullStars; $i++)
                                <span class="text-yellow-400 text-lg">⭐</span>
                            @endfor
                            
                            {{-- Setengah Bintang --}}
                            @if($halfStar)
                                <span class="text-yellow-400 text-lg relative">
                                    <span class="absolute overflow-hidden w-1/2">⭐</span>
                                    <span class="text-gray-300">☆</span>
                                </span>
                            @endif
                            
                            {{-- Bintang Kosong --}}
                            @for($i = 1; $i <= $emptyStars; $i++)
                                <span class="text-gray-300 text-lg">☆</span>
                            @endfor
                            
                            <span class="text-sm text-gray-600 ml-1">({{ number_format($rating, 1) }})</span>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-3 mt-3">
                    <div class="flex justify-between text-center text-sm">
                        <div>
                            <span class="text-2xl font-bold text-[#00bda2]">{{ $cleaner->total_tasks }}</span>
                            <p class="text-sm">Tugas Selesai</p>
                        </div>
                        <div>
                            <span class="text-2xl font-bold text-[#00bda2]">{{ $cleaner->rating }}</span>
                            <p class="text-sm">Rating</p>
                        </div>
                        <div>
                            <span class="text-2xl font-bold text-[#00bda2]">{{ $cleaner->satisfaction_rate }}</span>
                            <p class="text-sm">Kepuasan</p>
                        </div>
                    </div>
                </div>
        </div>
    </div>
    {{-- Monthly Performance --}}
    <div class="px-5 mt-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Performa Bulan Ini</h2>
        
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="space-y-4">
                {{-- Tugas Selesai --}}
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm text-gray-600">Tugas Selesai</span>
                        <span class="font-semibold">{{ $monthlyCompleted ?? 0 }} tugas</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        @php
                            $taskPercentage = isset($monthlyCompleted) ? min(100, $monthlyCompleted * 4) : 0;
                        @endphp
                        <div class="bg-green-600 rounded-full h-2" style="width: {{ $taskPercentage }}%"></div>
                    </div>
                </div>

                {{-- Hari Aktif --}}
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm text-gray-600">Hari Aktif</span>
                        <span class="font-semibold">{{ $activeDays ?? 0 }} hari</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        @php
                            $daysPercentage = isset($activeDays) ? min(100, $activeDays * 3.33) : 0;
                        @endphp
                        <div class="bg-green-600 rounded-full h-2" style="width: {{ $daysPercentage }}%"></div>
                    </div>
                </div>

                {{-- Rating --}}
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm text-gray-600">Rating Rata-rata</span>
                        <span class="font-semibold">{{ number_format($rating ?? $cleaner->rating ?? 0, 1) }}</span>
                    </div>
                    <div class="flex items-center">
                        @php $avgRating = $rating ?? $cleaner->rating ?? 0; @endphp
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $avgRating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="px-5 mt-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Aktivitas Terakhir</h2>
        
        <div class="space-y-3">
            @forelse($recentTasks ?? [] as $task)
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-green-600 font-bold">{{ $cleaner->initials }}</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-800">{{ $task->customer_name ?? 'Pelanggan' }}</h4>
                        <p class="text-xs text-gray-500">{{ $task->service_type ?? 'Layanan' }}</p>
                        <p class="text-xs text-gray-400">
                            @if(isset($task->completed_at))
                                {{ \Carbon\Carbon::parse($task->completed_at)->format('d M Y') }}
                            @else
                                {{ isset($task->task_date) ? \Carbon\Carbon::parse($task->task_date)->format('d M Y') : 'Tanggal tidak tersedia' }}
                            @endif
                        </p>
                    </div>
                    <span class="text-green-600 text-sm"><i class="fas fa-check-circle"></i></span>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-500 py-4">Belum ada aktivitas</p>
            @endforelse
        </div>
    </div>

    {{-- Logout Button --}}
    <div class="px-5 mt-8 mb-24">
        <form method="POST" action="{{ route('cleaner.logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin keluar?');">
            @csrf
            <button type="submit" class="w-full bg-red-50 text-red-600 py-3.5 rounded-xl font-medium hover:bg-red-100 transition border border-red-100">
                Keluar
            </button>
        </form>
    </div>
</div>
@endsection