@extends('admin.layouts.app')

@section('title', 'Monitoring Pekerjaan - Admin YukClean')

@section('content')
<div class="space-y-8">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-1">Monitoring Pekerjaan</h1>
        <p class="text-gray-500 text-[15px]">Pantau status dan progress semua pekerjaan</p>
    </div>

    {{-- Status Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200 text-center hover:-translate-y-0.5 hover:shadow-md transition-all">
            <div class="text-gray-500 text-sm mb-2 font-medium uppercase tracking-wider">Menunggu</div>
            <div class="text-3xl font-bold text-blue-500">{{ $statusStats['waiting'] }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200 text-center hover:-translate-y-0.5 hover:shadow-md transition-all">
            <div class="text-gray-500 text-sm mb-2 font-medium uppercase tracking-wider">Sedang Berjalan</div>
            <div class="text-3xl font-bold text-emerald-500">{{ $statusStats['in_progress'] }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200 text-center hover:-translate-y-0.5 hover:shadow-md transition-all">
            <div class="text-gray-500 text-sm mb-2 font-medium uppercase tracking-wider">Selesai</div>
            <div class="text-3xl font-bold text-red-500">{{ $statusStats['completed'] }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200 text-center hover:-translate-y-0.5 hover:shadow-md transition-all">
            <div class="text-gray-500 text-sm mb-2 font-medium uppercase tracking-wider">Dibatalkan</div>
            <div class="text-3xl font-bold text-amber-500">{{ $statusStats['cancelled'] }}</div>
        </div>
    </div>

    {{-- Distribusi Status Chart --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
        <div class="flex items-center gap-3 mb-6 text-yuk-teal">
            <i data-lucide="pie-chart" class="w-6 h-6"></i>
            <h3 class="text-gray-900 text-lg font-semibold">Distribusi Status Pekerjaan</h3>
        </div>

        <div class="flex flex-col md:flex-row gap-8 items-center mb-6">
            {{-- Pie Chart --}}
            <div class="relative w-48 h-48 shrink-0">
                <div class="pie-chart w-full h-full rounded-full relative overflow-hidden shadow-inner"
                     style="background: conic-gradient(
                        #1890ff 0deg {{ $percentages['waiting'] * 3.6 }}deg,
                        #52c41a {{ $percentages['waiting'] * 3.6 }}deg {{ ($percentages['waiting'] + $percentages['in_progress']) * 3.6 }}deg,
                        #ff4d4f {{ ($percentages['waiting'] + $percentages['in_progress']) * 3.6 }}deg {{ ($percentages['waiting'] + $percentages['in_progress'] + $percentages['completed']) * 3.6 }}deg,
                        #faad14 {{ ($percentages['waiting'] + $percentages['in_progress'] + $percentages['completed']) * 3.6 }}deg 360deg
                     );">
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[90px] h-[90px] bg-white rounded-full flex flex-col items-center justify-center shadow-sm border-[3px] border-white">
                        <span class="text-[10px] text-gray-500 uppercase tracking-tighter">Total</span>
                        <span class="text-2xl font-bold text-gray-900">{{ $totalOrders }}</span>
                    </div>
                </div>
            </div>

            {{-- Legend --}}
            <div class="flex-1 flex justify-between items-start min-w-[280px] w-full">
                <div class="space-y-4">
                    <div class="flex items-center gap-3 text-[15px] font-medium text-gray-900">
    <i class="fas fa-circle text-[#1890ff] text-lg"></i>
    Menunggu
</div>
<div class="flex items-center gap-3 text-[15px] font-medium text-gray-900">
    <i class="fas fa-circle text-[#52c41a] text-lg"></i>
    Berjalan
</div>
<div class="flex items-center gap-3 text-[15px] font-medium text-gray-900">
    <i class="fas fa-circle text-[#ff4d4f] text-lg"></i>
    Selesai
</div>
<div class="flex items-center gap-3 text-[15px] font-medium text-gray-900">
    <i class="fas fa-circle text-[#faad14] text-lg"></i>
    Dibatalkan
</div>
                </div>
                <div class="space-y-4 text-right">
                    <div class="text-xl font-bold text-[#1890ff]">{{ $statusStats['waiting'] }}</div>
                    <div class="text-xl font-bold text-[#52c41a]">{{ $statusStats['in_progress'] }}</div>
                    <div class="text-xl font-bold text-[#ff4d4f]">{{ $statusStats['completed'] }}</div>
                    <div class="text-xl font-bold text-[#faad14]">{{ $statusStats['cancelled'] }}</div>
                </div>
            </div>
        </div>

        {{-- Persentase --}}
       <div class="flex flex-wrap gap-6 pt-5 border-t border-gray-100">
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <i class="fas fa-circle text-[#1890ff] text-xs"></i>
        <span>Menunggu: {{ $percentages['waiting'] }}%</span>
    </div>
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <i class="fas fa-circle text-[#52c41a] text-xs"></i>
        <span>Berjalan: {{ $percentages['in_progress'] }}%</span>
    </div>
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <i class="fas fa-circle text-[#ff4d4f] text-xs"></i>
        <span>Selesai: {{ $percentages['completed'] }}%</span>
    </div>
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <i class="fas fa-circle text-[#faad14] text-xs"></i>
        <span>Dibatalkan: {{ $percentages['cancelled'] }}%</span>
    </div>
</div>
    </div>

    {{-- Pekerjaan Aktif --}}
    <div class="space-y-5">
        <h2 class="flex items-center gap-2 text-xl font-semibold text-gray-900">
            <i data-lucide="briefcase" class="text-yuk-teal w-6 h-6"></i>
            Pekerjaan Aktif
        </h2>

        @forelse($activeJobs as $job)
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 hover:-translate-y-0.5 hover:shadow-lg hover:border-yuk-teal transition-all group">
            <div class="flex justify-between items-start mb-4 flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <h3 class="text-lg font-bold text-gray-900">{{ $job->order_number }}</h3>
                    <span class="bg-emerald-100 text-emerald-700 border border-emerald-300 px-3 py-1 rounded-full text-xs font-semibold uppercase">
                        Berjalan
                    </span>
                </div>
                <div class="flex gap-5 text-sm text-gray-500">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="clock" class="text-yuk-teal w-3.5 h-3.5"></i>
                        Mulai: {{ substr($job->start_time, 0, 5) }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="check-circle" class="text-yuk-teal w-3.5 h-3.5"></i>
                        Selesai: {{ substr($job->end_time, 0, 5) }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-2.5 mb-4 pb-4 border-b border-gray-100">
                <i data-lucide="home" class="text-yuk-teal w-[18px] h-[18px]"></i>
                <strong class="text-[16px] font-semibold text-gray-900">{{ $job->service->name ?? 'Layanan' }}</strong>
            </div>

            <div class="mb-5 space-y-2">
                <p class="flex items-center gap-2.5 text-sm text-gray-500">
                    <i data-lucide="user" class="text-yuk-teal w-4 h-4 shrink-0"></i>
                    Pelanggan: {{ $job->user->name ?? $job->customer_name }}
                </p>
                <p class="flex items-center gap-2.5 text-sm text-gray-500">
                    <i data-lucide="users" class="text-yuk-teal w-4 h-4 shrink-0"></i>
                    Petugas: {{ $job->cleaner->name ?? 'Belum ditugaskan' }}
                </p>
                <p class="flex items-center gap-2.5 text-sm text-gray-500">
                    <i data-lucide="map-pin" class="text-yuk-teal w-4 h-4 shrink-0"></i>
                    {{ $job->address }}
                </p>
            </div>

            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
            <div class="flex justify-between mb-2 text-sm font-medium">
                <span class="text-gray-500">Progress</span>
                <span class="text-yuk-teal font-bold">{{ $job->progress ?? 0 }}%</span>
            </div>
            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-yuk-teal transition-all duration-500" style="width: {{ $job->progress ?? 0 }}%"></div>
            </div>
        </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 text-center">
            <p class="text-gray-500">Tidak ada pekerjaan aktif</p>
        </div>
        @endforelse
    </div>

    {{-- Riwayat Pekerjaan Selesai --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 mt-10">
        <h2 class="flex items-center gap-2 text-xl font-semibold text-gray-900 mb-6">
            <i data-lucide="history" class="text-yuk-teal w-6 h-6"></i>
            Riwayat Pekerjaan Selesai
        </h2>

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead>
                    <tr class="bg-gray-50 border-b-2 border-yuk-teal">
                        <th class="p-4 text-sm font-semibold uppercase tracking-wider text-gray-900">ID PESANAN</th>
                        <th class="p-4 text-sm font-semibold uppercase tracking-wider text-gray-900">PELANGGAN</th>
                        <th class="p-4 text-sm font-semibold uppercase tracking-wider text-gray-900">PETUGAS</th>
                        <th class="p-4 text-sm font-semibold uppercase tracking-wider text-gray-900">LAYANAN</th>
                        <th class="p-4 text-sm font-semibold uppercase tracking-wider text-gray-900">SELESAI PADA</th>
                        <th class="p-4 text-sm font-semibold uppercase tracking-wider text-gray-900">RATING</th>
                        <th class="p-4 text-sm font-semibold uppercase tracking-wider text-gray-900">PEMBAYARAN</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
    @forelse($completedJobs as $job)
    <tr class="hover:bg-teal-50/50 transition-colors">
        <td class="p-4 text-sm font-bold">{{ $job->order_number }}</td>
        <td class="p-4 text-sm">{{ $job->user->name ?? $job->customer_name }}</td>
        <td class="p-4 text-sm">{{ $job->cleaner->name ?? '-' }}</td>
        <td class="p-4 text-sm">
            <span class="px-3 py-1 bg-gray-100 rounded-md text-xs font-medium">
                {{ $job->service->name ?? 'Layanan' }}
            </span>
        </td>
        <td class="p-4 text-sm">
            {{ $job->completed_at ? \Carbon\Carbon::parse($job->completed_at)->format('d M Y, H:i') : '-' }}
        </td>
        <td class="p-4">
            @if($job->rating)
            <div class="flex gap-0.5">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $job->rating)
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400 text-amber-400"></i>
                    @else
                        <i data-lucide="star" class="w-4 h-4 text-gray-300"></i>
                    @endif
                @endfor
            </div>
            @else
            <span class="text-gray-400 text-xs">Belum ada rating</span>
            @endif
        </td>
        <td class="p-4 text-sm font-bold text-yuk-teal">Rp {{ number_format($job->total, 0, ',', '.') }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="7" class="p-8 text-center text-gray-500">Belum ada pekerjaan selesai</td>
    </tr>
    @endforelse
</tbody>

        {{-- Pagination --}}
        @if($completedJobs->hasPages())
        <div class="mt-6">
            {{ $completedJobs->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Inisialisasi ikon Lucide
    lucide.createIcons();

</script>
@endpush
@endsection