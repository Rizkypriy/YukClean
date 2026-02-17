{{-- resources/views/admin/orders/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Monitoring Pekerjaan - Admin YukClean')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Monitoring Pekerjaan</h1>
        <p class="text-gray-600">Pantau status dan progress semua pekerjaan</p>
    </div>

    {{-- Status Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex justify-between items-center">
                <span class="text-yellow-700 font-medium">Menunggu</span>
                <span class="text-2xl font-bold text-yellow-700">{{ $orderStats['waiting'] }}</span>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex justify-between items-center">
                <span class="text-blue-700 font-medium">Sedang Berjalan</span>
                <span class="text-2xl font-bold text-blue-700">{{ $orderStats['in_progress'] }}</span>
            </div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex justify-between items-center">
                <span class="text-green-700 font-medium">Selesai</span>
                <span class="text-2xl font-bold text-green-700">{{ $orderStats['completed'] }}</span>
            </div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex justify-between items-center">
                <span class="text-red-700 font-medium">Dibatalkan</span>
                <span class="text-2xl font-bold text-red-700">{{ $orderStats['cancelled'] }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Distribusi Status Pekerjaan (Pie Chart) --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Distribusi Status Pekerjaan</h2>
            <canvas id="statusChart" height="200"></canvas>
        </div>

        {{-- Pekerjaan Aktif --}}
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Pekerjaan Aktif</h2>
            <div class="space-y-4">
                @foreach($orders->whereIn('status', ['waiting', 'in_progress']) as $order)
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

                    @if($order->status == 'in_progress' && $order->progress)
                    <div class="mt-3">
                        <div class="flex justify-between text-sm mb-1">
                            <span>Progress</span>
                            <span>{{ $order->progress }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $order->progress }}%"></div>
                        </div>
                    </div>
                    @endif

                    <div class="mt-2 text-right">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            Detail <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Semua Pesanan --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Semua Pesanan</h2>
        
        {{-- Filter --}}
        <div class="mb-4">
            <select onchange="window.location.href=this.value" class="border rounded-lg px-4 py-2">
                <option value="{{ route('admin.orders.index') }}" {{ !request('status') ? 'selected' : '' }}>Semua Status</option>
                <option value="{{ route('admin.orders.index', ['status' => 'waiting']) }}" {{ request('status') == 'waiting' ? 'selected' : '' }}>Menunggu</option>
                <option value="{{ route('admin.orders.index', ['status' => 'in_progress']) }}" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Berjalan</option>
                <option value="{{ route('admin.orders.index', ['status' => 'completed']) }}" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID Pesanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Layanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Petugas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono text-sm">{{ $order->order_number }}</td>
                        <td class="px-6 py-4">{{ $order->user->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $order->service->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $order->cleaner->name ?? '-' }}</td>
                        <td class="px-6 py-4">{!! $order->status_badge_html !!}</td>
                        <td class="px-6 py-4">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $orders->withQueryString()->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Status Distribution Chart
    const ctx = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Menunggu', 'Sedang Berjalan', 'Selesai', 'Dibatalkan'],
            datasets: [{
                data: [
                    {{ $orderStats['waiting'] }},
                    {{ $orderStats['in_progress'] }},
                    {{ $orderStats['completed'] }},
                    {{ $orderStats['cancelled'] }}
                ],
                backgroundColor: [
                    'rgba(234, 179, 8, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush
@endsection