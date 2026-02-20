{{-- resources/views/admin/users/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Manajemen User - Admin YukClean')

@section('content')
<section class="mb-8">
    <h1 class="text-2xl font-bold">Manajemen User</h1>
    <p class="text-gray-500 mt-1">Kelola data pelanggan dan penyedia jasa</p>
</section>

<section class="bg-white p-6 rounded-2xl shadow-sm border border-gray-50">
    {{-- Tab Navigasi --}}
    <div class="flex gap-3 mb-6">
        <button id="tabPelanggan"
            class="px-6 py-2 rounded-full font-semibold transition-all border border-teal-500 bg-teal-500 text-white">
            Pelanggan
        </button>
        <button id="tabCleaner"
            class="px-6 py-2 rounded-full font-semibold transition-all border border-teal-500 text-teal-600 hover:bg-teal-50">
            Penyedia Jasa (Cleaner)
        </button>
    </div>

    {{-- Search & Filter --}}
    <div class="flex justify-between items-center mb-6 gap-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-4 flex-1">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   id="searchInput"
                   placeholder="Cari pelanggan..."
                   class="px-4 py-2 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all w-64 text-sm" />

            <select name="status"
                    id="statusFilter"
                    class="px-4 py-2 border border-gray-200 rounded-xl outline-none text-sm bg-white">
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
            </select>

            <div class="flex space-x-2">
                <button type="submit"
                    class="bg-teal-500 text-white px-6 py-2 rounded-lg hover:bg-teal-600 transition">
                    <i data-lucide="search" class="w-4 h-4 inline mr-1"></i>Cari
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Table Pelanggan --}}
    <div id="pelangganContainer" class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
            <thead class="bg-teal-50/50 text-teal-800 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">Nama</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Nomor HP</th>
                    <th class="px-6 py-4">Kota</th>
                    <th class="px-6 py-4 text-center">Total Pesanan</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $index => $user)
                <tr class="hover:bg-teal-50/30 transition-colors">
                    <td class="px-6 py-4">{{ $users->firstItem() + $index }}</td>
                    <td class="px-6 py-4 font-semibold">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-teal-500 rounded-full flex items-center justify-center text-white mr-3">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/'.$user->avatar) }}" alt="" class="w-8 h-8 rounded-full">
                                @else
                                    <span>{{ substr($user->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <span>{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-500">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $user->phone ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $user->city ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">{{ $user->orders_count ?? $user->orders?->count() ?? 0 }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($user->is_active ?? true)
                            <span class="px-4 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Aktif</span>
                        @else
                            <span class="px-4 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">Non-Aktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.users.show', $user->id) }}" 
                               class="p-2 text-gray-400 hover:text-teal-600 transition-colors">
                                <i data-lucide="eye" class="w-5 h-5"></i>
                            </a>
                            @if(isset($user->is_active))
                            <button onclick="toggleStatus({{ $user->id }})"
                                    class="p-2 text-gray-400 {{ $user->is_active ? 'hover:text-red-600' : 'hover:text-green-600' }} transition-colors">
                                <i data-lucide="{{ $user->is_active ? 'user-x' : 'user-check' }}" class="w-5 h-5"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                        Tidak ada data user
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>

    {{-- Table Cleaner (hidden by default) --}}
    <div id="cleanerContainer" class="hidden overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
            <thead class="bg-teal-50/50 text-teal-800 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">Nama Petugas</th>
                    <th class="px-6 py-4">Gender</th>
                    <th class="px-6 py-4">Nomor HP</th>
                    <th class="px-6 py-4">Rating</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{-- Data cleaner akan diisi dari controller --}}
                @forelse($cleaners ?? [] as $cleaner)
                <tr class="hover:bg-teal-50/30 transition-colors">
                    <td class="px-6 py-4 font-semibold">{{ $cleaner->name }}</td>
                    <td class="px-6 py-4 text-gray-500 text-xs">{{ $cleaner->gender ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $cleaner->phone ?? '-' }}</td>
                    <td class="px-6 py-4 text-orange-500 font-medium italic">
                        {{ number_format($cleaner->rating ?? 0, 1) }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $statusClass = match($cleaner->status ?? '') {
                                'available' => 'bg-green-100 text-green-700',
                                'working' => 'bg-amber-100 text-amber-700',
                                'offline' => 'bg-gray-100 text-gray-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <span class="px-4 py-1 rounded-full text-xs font-bold {{ $statusClass }}">
                            {{ ucfirst($cleaner->status ?? 'offline') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <button class="px-3 py-1 bg-teal-500 text-white rounded-lg text-xs font-bold hover:bg-teal-600">
                                Detail
                            </button>
                            <button class="px-3 py-1 bg-red-50 text-red-600 rounded-lg text-xs font-bold hover:bg-red-100">
                                Suspend
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        Tidak ada data petugas
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    // Inisialisasi ikon Lucide
    lucide.createIcons();

    /* TAB SWITCH LOGIC */
    const tabPelanggan = document.getElementById("tabPelanggan");
    const tabCleaner = document.getElementById("tabCleaner");
    const pelangganContainer = document.getElementById("pelangganContainer");
    const cleanerContainer = document.getElementById("cleanerContainer");
    const searchInput = document.getElementById("searchInput");

    tabPelanggan.addEventListener('click', () => {
        // Update Buttons
        tabPelanggan.className = "px-6 py-2 rounded-full font-semibold transition-all border border-teal-500 bg-teal-500 text-white";
        tabCleaner.className = "px-6 py-2 rounded-full font-semibold transition-all border border-teal-500 text-teal-600 hover:bg-teal-50";
        // Update Table
        pelangganContainer.classList.remove("hidden");
        cleanerContainer.classList.add("hidden");
        if (searchInput) searchInput.placeholder = "Cari pelanggan...";
    });

    tabCleaner.addEventListener('click', () => {
        // Update Buttons
        tabCleaner.className = "px-6 py-2 rounded-full font-semibold transition-all border border-teal-500 bg-teal-500 text-white";
        tabPelanggan.className = "px-6 py-2 rounded-full font-semibold transition-all border border-teal-500 text-teal-600 hover:bg-teal-50";
        // Update Table
        cleanerContainer.classList.remove("hidden");
        pelangganContainer.classList.add("hidden");
        if (searchInput) searchInput.placeholder = "Cari petugas...";
    });

    // Fungsi toggle status (sama seperti sebelumnya)
    function toggleStatus(id) {
        if (confirm('Apakah Anda yakin ingin mengubah status user ini?')) {
            fetch(`/admin/users/${id}/toggle-status`, {
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