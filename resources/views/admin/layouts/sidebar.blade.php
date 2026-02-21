<aside class="w-80 bg-teal-800 text-white flex flex-col justify-between p-6 shrink-0">    {{-- Header Sidebar --}}
    <div>
        <div class="flex items-center gap-3 mb-10 border-b border-teal-600">
            <img src="{{ asset('img/logo1.png') }}" alt="Yuk Clean Logo" class="w-20 h-20">
            <div>
                <h2 class="text-xl font-bold leading-tight">YukClean</h2>
                <span class="text-xs text-teal-300">Admin Dashboard</span>
            </div>
        </div>

        {{-- Navigation Menu --}}
        <nav class="space-y-2">
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 p-3 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-teal-700 font-semibold' : 'hover:bg-white/10 text-cyan-50' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i> 
                Dashboard
            </a>
            
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center gap-3 p-3 rounded-xl transition-all {{ request()->routeIs('admin.users.*') ? 'bg-teal-700 font-semibold' : 'hover:bg-white/10 text-cyan-50' }}">
                <i data-lucide="users" class="w-5 h-5"></i> 
                Manajemen User
            </a>
            
            <a href="{{ route('admin.orders.monitoring') }}" 
               class="flex items-center gap-3 p-3 rounded-xl transition-all {{ request()->routeIs('admin.orders.*') ? 'bg-teal-700 font-semibold' : 'hover:bg-white/10 text-cyan-50' }}">
                <i data-lucide="clipboard-list" class="w-5 h-5"></i> 
                Monitoring Pekerjaan
            </a>
            
            <a href="{{ route('admin.services.index') }}" 
               class="flex items-center gap-3 p-3 rounded-xl transition-all {{ request()->routeIs('admin.services.*') ? 'bg-teal-700 font-semibold' : 'hover:bg-white/10 text-cyan-50' }}">
                <i data-lucide="package" class="w-5 h-5"></i> 
                Pengelolaan Layanan
            </a>

            <a href="{{ route('admin.reports.weekly') }}" 
   class="flex items-center gap-3 p-3 rounded-xl transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-teal-700 font-semibold' : 'hover:bg-white/10 text-cyan-50' }}">
    <i data-lucide="file-text" class="w-5 h-5"></i> 
    Laporan Mingguan
</a>
        </nav>
    </div>

    {{-- Logout Button --}}
    <form method="POST" action="{{ route('admin.logout') }}" id="logoutForm">
        @csrf
        <button type="submit" 
                class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-white/10 transition-all text-cyan-50 opacity-80 hover:opacity-100">
            <i data-lucide="log-out" class="w-5 h-5"></i> 
            Logout
        </button>
    </form>

    {{-- Admin Info (Opsional, bisa dihapus jika tidak diperlukan) --}}
    {{-- 
    <div class="mt-6 pt-6 border-t border-teal-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-teal-600 rounded-full flex items-center justify-center text-white">
                <i data-lucide="user" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-sm font-medium">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</p>
                <p class="text-xs text-cyan-200">Administrator</p>
            </div>
        </div>
    </div>
    --}}
</aside>