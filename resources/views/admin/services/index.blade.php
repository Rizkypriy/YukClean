{{-- resources/views/admin/services/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Pengelolaan Layanan - Admin YukClean')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Pengelolaan Layanan & Harga</h1>
        <p class="text-gray-500 mt-1">Kelola daftar layanan dan harga pembersihan Yuk Clean</p>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4 mb-6">
        <a href="{{ route('admin.services.create') }}" 
           class="bg-teal-700 hover:bg-teal-800 text-white px-5 py-2.5 rounded-lg font-semibold text-sm flex items-center justify-center gap-2 transition shadow-md">
            <i data-lucide="plus" class="w-5 h-5"></i> Tambah Layanan Baru
        </a>

        {{-- Kategori Filter Dropdown --}}
        <div class="relative inline-block w-full sm:w-48">
            <button id="kategoriDropdownBtn"
                class="w-full bg-white border border-gray-300 px-4 py-2.5 rounded-lg text-sm flex items-center justify-between hover:border-teal-600 transition">
                <span id="selectedKategori">Semua Kategori</span>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200"></i>
            </button>
            <div id="kategoriDropdown"
                class="hidden absolute right-0 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-xl z-50 overflow-hidden">
                <a href="#" data-value="" class="block px-4 py-2 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-700">Semua Kategori</a>
                @foreach($categories ?? [] as $category)
                <a href="#" data-value="{{ $category }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-700">{{ $category }}</a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Search Bar --}}
    <div class="flex items-center bg-white px-4 py-2 rounded-xl border border-gray-200 shadow-sm w-full max-w-xs transition-focus focus-within:ring-2 focus-within:ring-teal-500 mb-4">
        <i data-lucide="search" class="w-5 h-5 text-gray-400 mr-2"></i>
        <input type="text" id="searchInput" placeholder="Cari layanan..." class="outline-none text-sm w-full" />
    </div>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead class="bg-gray-50 border-b-2 border-teal-700">
                <tr>
                    <th class="p-4 text-xs font-bold uppercase tracking-wider">Nama Layanan</th>
                    <th class="p-4 text-xs font-bold uppercase tracking-wider">Kategori</th>
                    <th class="p-4 text-xs font-bold uppercase tracking-wider text-teal-800">Harga Dasar</th>
                    <th class="p-4 text-xs font-bold uppercase tracking-wider">Harga per Jam</th>
                    <th class="p-4 text-xs font-bold uppercase tracking-wider">Min Jam</th>
                    <th class="p-4 text-xs font-bold uppercase tracking-wider">Status</th>
                    <th class="p-4 text-xs font-bold uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody id="layananTableBody" class="divide-y divide-gray-100">
                @foreach($services as $service)
                <tr class="hover:bg-teal-50 transition-colors">
                    <td class="p-4 font-bold text-gray-900">{{ $service->name }}</td>
                    <td class="p-4">
                        <span class="px-3 py-1 bg-sky-100 text-sky-700 rounded-full text-xs font-bold border border-sky-200">
                            {{ $service->category }}
                        </span>
                    </td>
                    <td class="p-4 font-bold text-teal-700">{{ $service->price_formatted }}</td>
                    <td class="p-4 text-gray-600">Rp {{ number_format($service->price_per_hour, 0, ',', '.') }}</td>
                    <td class="p-4 text-gray-600">{{ $service->min_hours }} jam</td>
                    <td class="p-4">
                        @if($service->is_active)
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold border border-green-200">Aktif</span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold border border-gray-200">Nonaktif</span>
                        @endif
                    </td>
                    <td class="p-4">
                        <div class="flex gap-4">
                            <a href="{{ route('admin.services.edit', $service->id) }}" 
                               class="text-teal-600 hover:scale-110 transition-transform">
                                <i data-lucide="edit-3" class="w-5 h-5"></i>
                            </a>
                            <button onclick="deleteService({{ $service->id }})" 
                                    class="text-red-500 hover:scale-110 transition-transform">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($services->hasPages())
    <div class="mt-6">
        {{ $services->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6">
        <h3 class="text-lg font-bold mb-2">Konfirmasi Hapus</h3>
        <p class="text-gray-600 mb-4">
            Apakah Anda yakin ingin menghapus layanan <strong id="deleteLayananName"></strong>?
        </p>
        <p class="text-red-500 text-xs mb-6 italic">*Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex justify-end gap-3">
            <button id="cancelDelete" class="px-4 py-2 text-sm rounded-lg border border-gray-300 font-semibold text-gray-600 hover:bg-gray-50">
                Batal
            </button>
            <button id="confirmDelete" class="px-4 py-2 text-sm rounded-lg bg-red-500 text-white font-semibold hover:bg-red-600">
                Hapus
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    // Inisialisasi Lucide Icons
    lucide.createIcons();

    // Data dari Laravel untuk JavaScript (jika diperlukan untuk filter)
    const services = @json($services->items());
    
    // Dropdown Kategori Logic
    const dropBtn = document.getElementById('kategoriDropdownBtn');
    const dropContent = document.getElementById('kategoriDropdown');
    const selectedKategori = document.getElementById('selectedKategori');
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('layananTableBody');

    dropBtn.addEventListener('click', () => {
        dropContent.classList.toggle('hidden');
        dropBtn.querySelector('i').classList.toggle('rotate-180');
    });

    // Klik di luar dropdown
    document.addEventListener('click', (e) => {
        if (!dropBtn.contains(e.target) && !dropContent.contains(e.target)) {
            dropContent.classList.add('hidden');
            dropBtn.querySelector('i').classList.remove('rotate-180');
        }
    });

    // Filter kategori
    document.querySelectorAll('#kategoriDropdown a').forEach((link) => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const value = link.dataset.value;
            selectedKategori.textContent = link.textContent;
            dropContent.classList.add('hidden');
            
            // Filter table rows
            filterTable(value, searchInput.value);
        });
    });

    // Search filter
    searchInput.addEventListener('input', () => {
        filterTable(selectedKategori.dataset.value || '', searchInput.value);
    });

    function filterTable(category, searchTerm) {
        const rows = tableBody.querySelectorAll('tr');
        rows.forEach(row => {
            const categoryCell = row.querySelector('td:nth-child(2) span').textContent;
            const nameCell = row.querySelector('td:first-child').textContent.toLowerCase();
            
            const categoryMatch = !category || categoryCell === category;
            const searchMatch = !searchTerm || nameCell.includes(searchTerm.toLowerCase());
            
            row.style.display = categoryMatch && searchMatch ? '' : 'none';
        });
    }

    // Delete Service Function
    function deleteService(id) {
        // Cari nama layanan dari baris yang sesuai
        const rows = tableBody.querySelectorAll('tr');
        let serviceName = '';
        rows.forEach(row => {
            if (row.querySelector('button[onclick*="' + id + '"]')) {
                serviceName = row.querySelector('td:first-child').textContent;
            }
        });
        
        document.getElementById('deleteLayananName').textContent = serviceName;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
        
        // Set confirm action
        document.getElementById('confirmDelete').onclick = function() {
            fetch(`/admin/services/${id}`, {
                method: 'DELETE',
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
                } else {
                    alert('Gagal menghapus layanan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            })
            .finally(() => {
                document.getElementById('deleteModal').classList.add('hidden');
                document.getElementById('deleteModal').classList.remove('flex');
            });
        };
    }

    // Cancel delete
    document.getElementById('cancelDelete').addEventListener('click', () => {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    });

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        const modal = document.getElementById('deleteModal');
        if (e.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    });
</script>
@endpush
@endsection