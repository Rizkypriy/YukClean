{{-- resources/views/admin/layouts/navbar.blade.php --}}
<header class="bg-white shadow-sm">
    <div class="flex justify-between items-center px-6 py-3">
        {{-- Search Bar (opsional) --}}
        <div class="flex-1">
            <div class="relative max-w-md">
                <input type="text" 
                       placeholder="Search..." 
                       class="w-full border rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
        </div>

        {{-- Right Side Menu --}}
        <div class="flex items-center space-x-4">
            {{-- Notifications --}}
            <button class="relative p-2 text-gray-600 hover:text-blue-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-bell text-xl"></i>
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            {{-- Messages --}}
            <button class="relative p-2 text-gray-600 hover:text-blue-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-envelope text-xl"></i>
            </button>

            {{-- Profile Dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="text-sm font-medium">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</span>
                    <i class="fas fa-chevron-down text-xs" :class="{ 'transform rotate-180': open }"></i>
                </button>

                {{-- Dropdown Menu --}}
                <div x-show="open" 
                     @click.away="open = false"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user mr-2"></i> Profile
                    </a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-cog mr-2"></i> Settings
                    </a>
                    <hr class="my-2">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>