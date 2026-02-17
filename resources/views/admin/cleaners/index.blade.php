{{-- resources/views/admin/cleaners/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Manajemen Cleaner - Admin YukClean')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Manajemen Penyedia Jasa</h1>
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">Penjualan</span>
                <span class="text-sm font-medium">{{ number_format($cleaners->total()) }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">Penyedia Jasa</span>
                <span class="text-sm font-medium">{{ App\Models\Cleaner::count() }}</span>
            </div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari petugas..." 
                       class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="w-48">
                <select name="status" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="working" {{ request('status') == 'working' ? 'selected' : '' }}>Working</option>
                    <option value="offline" {{ request('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                <a href="{{ route('admin.cleaners.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Petugas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor HP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Jobs</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($cleaners as $cleaner)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                                    @if($cleaner->avatar)
                                        <img src="{{ asset('storage/'.$cleaner->avatar) }}" alt="" class="w-8 h-8 rounded-full">
                                    @else
                                        <i class="fas fa-user text-gray-600"></i>
                                    @endif
                                </div>
                                <span class="font-medium">{{ $cleaner->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ $cleaner->gender ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $cleaner->phone ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($cleaner->rating))
                                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                                    @else
                                        <i class="far fa-star text-gray-300 text-sm"></i>
                                    @endif
                                @endfor
                                <span class="ml-1 text-sm">({{ number_format($cleaner->rating, 1) }})</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">{!! $cleaner->status_badge !!}</td>
                        <td class="px-6 py-4">{{ $cleaner->total_jobs }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.cleaners.show', $cleaner->id) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                <button onclick="toggleStatus({{ $cleaner->id }})" 
                                        class="text-{{ $cleaner->is_active ? 'red' : 'green' }}-600 hover:text-{{ $cleaner->is_active ? 'red' : 'green' }}-800">
                                    <i class="fas fa-{{ $cleaner->is_active ? 'ban' : 'check' }}"></i> 
                                    {{ $cleaner->is_active ? 'Suspend' : 'Aktifkan' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada data petugas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t">
            {{ $cleaners->withQueryString()->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleStatus(id) {
        if (confirm('Apakah Anda yakin ingin mengubah status petugas ini?')) {
            fetch(`/admin/cleaners/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                }
            });
        }
    }
</script>
@endpush
@endsection