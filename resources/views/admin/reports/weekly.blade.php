@extends('admin.layouts.app')

@section('title', 'Laporan Mingguan - Admin YukClean')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Laporan Mingguan</h1>
        <p class="text-gray-500 mt-1">Ringkasan performa layanan periode {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
    </div>

    {{-- Filter Tanggal --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('admin.reports.weekly') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                       class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                       class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
            </div>
            <div class="flex gap-2">
                <button type="submit" 
                        class="bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 transition">
                    Tampilkan
                </button>
                <a href="{{ route('admin.reports.weekly') }}" 
                   class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Ringkasan Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Pesanan</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $summary['total_orders'] }}</h3>
                </div>
                <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="shopping-bag" class="w-6 h-6 text-teal-600"></i>
                </div>
            </div>
            <div class="mt-4 flex gap-3 text-sm">
                <span class="text-green-600">{{ $summary['completed_orders'] }} selesai</span>
                <span class="text-red-600">{{ $summary['cancelled_orders'] }} batal</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Pendapatan</p>
                    <h3 class="text-3xl font-bold text-gray-900">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</h3>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="wallet" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <p class="mt-4 text-sm text-gray-600">Rata-rata: Rp {{ number_format($summary['avg_order_value'], 0, ',', '.') }}/pesanan</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">User Baru</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $summary['new_users'] }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <p class="mt-4 text-sm text-gray-600">Pelanggan baru</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Cleaner Baru</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $summary['new_cleaners'] }}</h3>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="user-check" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
            <p class="mt-4 text-sm text-gray-600">Petugas baru</p>
        </div>
    </div>

    {{-- Grafik Pesanan per Hari --}}
   <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Pesanan per Hari</h2>
        <div class="flex gap-4 text-sm">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-teal-600 rounded-full"></span>
                <span>Total</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                <span>Selesai</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                <span>Batal</span>
            </div>
        </div>
    </div>
    <div class="h-64"> {{-- Container dengan tinggi tetap --}}
        <canvas id="ordersChart" class="w-full h-full"></canvas>
    </div>
</div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Layanan Terpopuler --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Layanan Terpopuler</h2>
            <div class="space-y-4">
                @forelse($popularServices as $service)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium">{{ $service->service->name ?? 'Unknown' }}</span>
                        <span class="text-teal-600 font-semibold">{{ $service->total }} pesanan</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        @php
                            $maxCount = $popularServices->max('total');
                            $percentage = $maxCount > 0 ? ($service->total / $maxCount) * 100 : 0;
                        @endphp
                        <div class="bg-teal-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Belum ada data layanan</p>
                @endforelse
            </div>
        </div>

        {{-- Cleaner Terbaik --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Cleaner Terbaik Minggu Ini</h2>
            <div class="space-y-4">
                @forelse($topCleaners as $cleaner)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-teal-500 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($cleaner->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="font-medium">{{ $cleaner->name }}</h4>
                            <p class="text-xs text-gray-500">{{ $cleaner->tasks_count }} tugas selesai</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="flex items-center gap-1">
                            <span class="text-yellow-400">⭐</span>
                            <span class="font-medium">{{ number_format($cleaner->rating, 1) }}</span>
                        </div>
                        <p class="text-xs text-gray-500">{{ $cleaner->satisfaction_rate ?? 0 }}% kepuasan</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Belum ada data cleaner</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Tabel Detail Pesanan --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Detail Pesanan Minggu Ini</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b-2 border-teal-600">
                        <th class="p-4 text-sm font-semibold">Tanggal</th>
                        <th class="p-4 text-sm font-semibold">Total</th>
                        <th class="p-4 text-sm font-semibold">Selesai</th>
                        <th class="p-4 text-sm font-semibold">Dibatalkan</th>
                        <th class="p-4 text-sm font-semibold">Persentase Selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($dailyOrders as $order)
                    @php
                        $completionRate = $order->total > 0 ? round(($order->completed / $order->total) * 100) : 0;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 font-medium">{{ Carbon\Carbon::parse($order->date)->format('d M Y') }}</td>
                        <td class="p-4">{{ $order->total }}</td>
                        <td class="p-4 text-green-600 font-medium">{{ $order->completed }}</td>
                        <td class="p-4 text-red-500">{{ $order->cancelled }}</td>
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                    <div class="bg-teal-600 h-2 rounded-full" style="width: {{ $completionRate }}%"></div>
                                </div>
                                <span class="text-sm">{{ $completionRate }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tombol Export --}}
    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.reports.export.pdf', request()->query()) }}" 
           class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2">
            <i data-lucide="file-text" class="w-5 h-5"></i>
            Export PDF
        </a>
        <a href="{{ route('admin.reports.export.excel', request()->query()) }}" 
           class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
            <i data-lucide="file-spreadsheet" class="w-5 h-5"></i>
            Export Excel
        </a>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<script>
      // Inisialisasi Lucide Icons
    lucide.createIcons();
    // Grafik Pesanan
    const ctx = document.getElementById('ordersChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [
                {
                    label: 'Total Pesanan',
                    data: {!! json_encode($chartData['total']) !!},
                    borderColor: '#0d9488',
                    backgroundColor: 'rgba(13, 148, 136, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Selesai',
                    data: {!! json_encode($chartData['completed']) !!},
                    borderColor: '#22c55e',
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    borderDash: [5, 5]
                },
                {
                    label: 'Dibatalkan',
                    data: {!! json_encode($chartData['cancelled']) !!},
                    borderColor: '#ef4444',
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    borderDash: [5, 5]
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection