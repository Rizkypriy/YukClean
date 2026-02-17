{{-- resources/views/admin/dashboard.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Dashboard - Admin YukClean')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Dashboard Monitoring</h1>
        <p class="text-gray-600">Selamat datang di dashboard admin YukClean</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Pesanan Hari Ini --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-600">Total Pesanan Hari Ini</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total_orders_today'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-600 text-sm">{{ $stats['orders_today_growth'] }}</span>
                <span class="text-gray-500 text-sm ml-2">dari kemarin</span>
            </div>
        </div>

        {{-- Pesanan Sedang Berjalan --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-600">Pesanan Sedang Berjalan</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['active_orders'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-spinner text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-blue-600 text-sm">Aktif</span>
            </div>
        </div>

        {{-- Pesanan Selesai Hari Ini --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-600">Pesanan Selesai</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['completed_orders_today'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-gray-500 text-sm">Hari ini</span>
            </div>
        </div>

        {{-- Total Pendapatan Bulanan --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-600">Total Pendapatan Bulanan</p>
                    <p class="text-3xl font-bold mt-2">Rp {{ number_format($stats['monthly_revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-wallet text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-600 text-sm">{{ $stats['revenue_growth'] }}</span>
                <span class="text-gray-500 text-sm ml-2">dari bulan lalu</span>
            </div>
        </div>

        {{-- Total Petugas Aktif --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-600">Total Petugas Aktif</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['active_cleaners'] }}</p>
                </div>
                <div class="bg-indigo-100 p-3 rounded-lg">
                    <i class="fas fa-users text-indigo-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-600 text-sm">Online</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Chart Pesanan Per Hari --}}
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Pesanan Per Hari</h2>
            <canvas id="ordersChart" height="200"></canvas>
        </div>

        {{-- Layanan Paling Diminati --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Layanan Paling Diminati</h2>
            <div class="space-y-4">
                @foreach($topServices as $index => $service)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>{{ $service['name'] }}</span>
                        <span class="font-medium">{{ $service['count'] }} pesanan</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        @php
                            $maxCount = $topServices->max('count');
                            $percentage = $maxCount > 0 ? ($service['count'] / $maxCount) * 100 : 0;
                        @endphp
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Pekerjaan Aktif --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Pekerjaan Aktif</h2>
        <div class="space-y-4">
            @php
                $activeOrders = App\Models\Order::with(['user', 'cleaner', 'service'])
                    ->whereIn('status', ['waiting', 'in_progress'])
                    ->latest()
                    ->limit(5)
                    ->get();
            @endphp

            @forelse($activeOrders as $order)
            <div class="border rounded-lg p-4 hover:bg-gray-50">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <span class="font-mono text-sm text-gray-500">{{ $order->order_number }}</span>
                        <span class="ml-2">{!! $order->status_badge !!}</span>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $order->scheduled_time ? $order->scheduled_time->format('H:i') : '-' }} 
                        - 
                        {{ $order->scheduled_time ? $order->scheduled_time->copy()->addHours(4)->format('H:i') : '-' }}
                    </div>
                </div>
                
                <h3 class="font-medium">{{ $order->service->name ?? 'Layanan' }}</h3>
                
                <div class="grid grid-cols-2 gap-2 mt-2 text-sm">
                    <div>
                        <span class="text-gray-500">Pelanggan:</span>
                        <span class="ml-1">{{ $order->user->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Petugas:</span>
                        <span class="ml-1">{{ $order->cleaner->name ?? 'Belum ditugaskan' }}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-500">Alamat:</span>
                        <span class="ml-1">{{ $order->address ?? '-' }}</span>
                    </div>
                </div>

                @if($order->status == 'in_progress')
                <div class="mt-3">
                    <div class="flex justify-between text-sm mb-1">
                        <span>Progress</span>
                        <span>{{ $order->progress ?? 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $order->progress ?? 0 }}%"></div>
                    </div>
                </div>
                @endif

                <div class="mt-2 text-right">
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        Detail <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">Tidak ada pekerjaan aktif</p>
            @endforelse

            <div class="text-center mt-4">
                <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-800">
                    Lihat Semua Pekerjaan <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Orders Chart
    const ctx = document.getElementById('ordersChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($weeklyOrders['labels']) !!},
            datasets: [{
                label: 'Jumlah Pesanan',
                data: {!! json_encode($weeklyOrders['data']) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
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
                        display: true,
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection