<div class="w-64 bg-white shadow-lg flex flex-col">
    <div class="p-4 border-b">
        <h1 class="text-xl font-bold text-blue-600">YukClean Admin</h1>
        <p class="text-sm text-gray-600">Dashboard Monitoring</p>
    </div>
    
    <nav class="flex-1 p-4">
        <ul class="space-y-2">
            <li>
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center space-x-2 p-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-chart-pie w-5"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center space-x-2 p-2 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-users w-5"></i>
                    <span>Manajemen User</span>
                </a>
            </li>
        
            <li>
                <a href="{{ route('admin.orders.index') }}" 
                   class="flex items-center space-x-2 p-2 rounded-lg {{ request()->routeIs('admin.orders.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-clipboard-list w-5"></i>
                    <span>Monitoring Pekerjaan</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('admin.services.index') }}" 
                   class="flex items-center space-x-2 p-2 rounded-lg {{ request()->routeIs('admin.services.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-tags w-5"></i>
                    <span>Pengelolaan Layanan</span>
                </a>
            </li>
            
        </ul>
    </nav>
    {{-- Logout di bagian bawah sidebar --}}
<div class="p-4 border-t">
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" class="flex items-center space-x-2 w-full p-2 text-left text-red-600 hover:bg-red-50 rounded-lg">
            <i class="fas fa-sign-out-alt w-5"></i>
            <span>Logout</span>
        </button>
    </form>
</div>
    <div class="p-4 border-t">
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <p class="text-sm font-medium">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</p>
                <p class="text-xs text-gray-500">Administrator</p>
            </div>
        </div>
    </div>
</div>

