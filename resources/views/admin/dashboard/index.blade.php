@extends('admin.layouts.app')

@section('title', 'Dashboard - Admin YukClean')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <section class="mb-8">
        <h1 class="text-2xl font-bold">Dashboard Monitoring</h1>
        <p class="text-gray-500 mt-1">Selamat datang di dashboard admin Yuk Clean</p>
    </section>

    {{-- Stats Cards dengan style baru --}}
    <section class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
        {{-- Total Pesanan Hari Ini --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-50">
            <div class="w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center mb-3 text-white">
                <i data-lucide="box" class="w-5 h-5"></i>
            </div>
            <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">
                Pesanan Hari Ini
            </p>
            <div class="flex items-baseline gap-2 mt-1">
                <h2 class="text-2xl font-bold">{{ $stats['total_orders_today'] ?? 0 }}</h2>
                <span class="text-xs font-semibold text-green-600">+12%</span>
            </div>
        </div>

        {{-- Pesanan Sedang Berjalan --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-50">
            <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center mb-3 text-white">
                <i data-lucide="trending-up" class="w-5 h-5"></i>
            </div>
            <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">
                Pesanan Berjalan
            </p>
            <div class="flex items-baseline gap-2 mt-1">
                <h2 class="text-2xl font-bold">{{ $stats['active_orders'] ?? 0 }}</h2>
                <span class="text-xs text-blue-500">Aktif</span>
            </div>
        </div>

        {{-- Pesanan Selesai --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-50">
            <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center mb-3 text-white">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
            <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">
                Pesanan Selesai
            </p>
            <div class="flex items-baseline gap-2 mt-1">
                <h2 class="text-2xl font-bold">{{ $stats['completed_orders_today'] ?? 0 }}</h2>
                <span class="text-xs text-emerald-500 italic">Hari ini</span>
            </div>
        </div>

        {{-- Pendapatan Bulanan --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-50">
            <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center mb-3 text-white">
                <i data-lucide="dollar-sign" class="w-5 h-5"></i>
            </div>
            <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">
                Pendapatan
            </p>
            <div class="flex items-baseline gap-2 mt-1">
                <h2 class="text-2xl font-bold text-sm lg:text-base">
                    Rp {{ number_format($stats['monthly_revenue'] ?? 0, 0, ',', '.') }}
                </h2>
                <span class="text-xs font-semibold text-green-600">{{ $stats['revenue_growth'] ?? '+23%' }}</span>
            </div>
        </div>

        {{-- Total Petugas Aktif --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-50">
            <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center mb-3 text-white">
                <i data-lucide="user-check" class="w-5 h-5"></i>
            </div>
            <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">
                Petugas Aktif
            </p>
            <div class="flex items-baseline gap-2 mt-1">
                <h2 class="text-2xl font-bold">{{ $stats['active_cleaners'] ?? 0 }}</h2>
                <span class="text-xs text-purple-500">Online</span>
            </div>
        </div>
    </section>

    {{-- Charts Section --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-50">
            <h3 class="font-bold mb-4">Pesanan Per Hari</h3>
            <canvas id="orderChart" height="100"></canvas>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-50">
            <h3 class="font-bold mb-4">Layanan Populer</h3>
            <canvas id="serviceChart" height="200"></canvas>
        </div>
    </section>

    {{-- Monitoring Pesanan --}}
    <section class="bg-white rounded-2xl shadow-sm border border-gray-50 overflow-hidden">
        <div class="p-6 flex justify-between items-center border-b border-gray-50">
            <h3 class="font-bold">Monitoring Pesanan</h3>
            <select
                id="statusFilter"
                class="bg-gray-50 border border-gray-200 text-sm rounded-lg px-3 py-2 outline-none"
            >
                <option value="all">Semua Status</option>
                <option value="running">Berjalan</option>
                <option value="done">Selesai</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-teal-50/50 text-teal-800 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-4 text-center">ID</th>
                        <th class="px-6 py-4">Pelanggan</th>
                        <th class="px-6 py-4">Petugas</th>
                        <th class="px-6 py-4">Layanan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentOrders ?? [] as $order)
                    <tr data-status="{{ in_array($order->status, ['in_progress', 'on_progress']) ? 'running' : 'done' }}" class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-center font-mono font-medium">
                            {{ $order->order_number }}
                        </td>
                        <td class="px-6 py-4 font-semibold">
                            {{ $order->user->name ?? $order->customer_name }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $order->cleaner->name ?? 'Belum ditugaskan' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $order->service->name ?? $order->bundle->name ?? 'Layanan' }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusClass = match($order->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'confirmed' => 'bg-blue-100 text-blue-700',
                                    'on_progress', 'in_progress' => 'bg-teal-100 text-teal-700',
                                    'completed' => 'bg-green-100 text-green-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                                $statusText = match($order->status) {
                                    'pending' => 'Pending',
                                    'confirmed' => 'Dikonfirmasi',
                                    'on_progress', 'in_progress' => 'Berjalan',
                                    'completed' => 'Selesai',
                                    'cancelled' => 'Dibatalkan',
                                    default => ucfirst($order->status),
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-teal-700 font-bold hover:underline">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Belum ada data pesanan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Inisialisasi ikon Lucide
    lucide.createIcons();

    // Filter Status
    const filter = document.getElementById('statusFilter');
    const rows = document.querySelectorAll('tbody tr[data-status]');

    if (filter) {
        filter.addEventListener('change', () => {
            rows.forEach((row) => {
                const status = row.dataset.status;
                row.style.display =
                    filter.value === 'all' || filter.value === status ? '' : 'none';
            });
        });
    }

    // Chart Pesanan Per Hari
    const ctxOrder = document.getElementById('orderChart')?.getContext('2d');
    if (ctxOrder) {
        new Chart(ctxOrder, {
            type: 'line',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                datasets: [{
                    data: [18, 22, 19, 25, 21, 28, 24],
                    borderColor: '#0f766e',
                    backgroundColor: 'rgba(15, 118, 110, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#0f766e',
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { display: false } } }
            }
        });
    }

    // Chart Layanan Populer
    const ctxService = document.getElementById('serviceChart')?.getContext('2d');
    if (ctxService) {
        new Chart(ctxService, {
            type: 'bar',
            data: {
                labels: ['Rumah', 'Kantor', 'Kaca', 'Karpet', 'AC'],
                datasets: [{
                    data: [45, 32, 28, 22, 18],
                    backgroundColor: '#14b8a6',
                    borderRadius: 6,
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false } } }
            }
        });
    }
</script>
@endpush
@endsection