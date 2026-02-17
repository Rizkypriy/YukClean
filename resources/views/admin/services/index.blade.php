{{-- resources/views/admin/services/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Pengelolaan Layanan - Admin YukClean')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Pengelolaan Layanan & Harga</h1>
        <a href="{{ route('admin.services.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
            <i class="fas fa-plus mr-2"></i>Tambah Layanan
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Layanan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Dasar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga per Jam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Min Jam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($services as $service)
                <tr>
                    <td class="px-6 py-4 font-medium">{{ $service->name }}</td>
                    <td class="px-6 py-4">{{ $service->category }}</td>
                    <td class="px-6 py-4">{{ $service->price_formatted }}</td>
                    <td class="px-6 py-4">Rp {{ number_format($service->price_per_hour, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">{{ $service->min_hours }} jam</td>
                    <td class="px-6 py-4">
                        @if($service->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Aktif</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.services.edit', $service->id) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="deleteService({{ $service->id }})" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    function deleteService(id) {
        if (confirm('Apakah Anda yakin ingin menghapus layanan ini?')) {
            fetch(`/admin/services/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
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