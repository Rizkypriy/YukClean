{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Yuk Clean - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Smooth scrolling */
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Hide scrollbar */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Active nav indicator */
        .nav-active {
            @apply text-green-600 relative;
        }
        
        .nav-active:after {
            content: '';
            @apply absolute -bottom-3 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-green-600 rounded-full;
        }
        
        /* Animasi */
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .alert { animation: slideIn 0.3s ease-out; }
        
        /* Style untuk teks biasa (warna hitam) */
        .text-regular {
            color: #333333;
        }
    </style>
</head>
<body class="bg-gray-50">

    {{-- Notifikasi Success --}}
    @if(session('success'))
        <div id="successAlert" class="fixed top-4 right-4 z-50 max-w-md bg-green-100 border-l-4 border-green-600 text-green-700 p-4 rounded-lg shadow-lg alert">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.closest('#successAlert').remove()" class="ml-4 text-green-700 hover:text-green-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>
        <script>setTimeout(() => document.getElementById('successAlert')?.remove(), 5000);</script>
    @endif

    {{-- Notifikasi Error --}}
    @if(session('error'))
        <div id="errorAlert" class="fixed top-4 right-4 z-50 max-w-md bg-red-100 border-l-4 border-red-600 text-red-700 p-4 rounded-lg shadow-lg alert">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.closest('#errorAlert').remove()" class="ml-4 text-red-700 hover:text-red-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>
        <script>setTimeout(() => document.getElementById('errorAlert')?.remove(), 5000);</script>
    @endif

    {{-- Main Content --}}
    @yield('content')
    
    {{-- BOTTOM NAVIGASI UNTUK USER (HANYA MUNCUL DI HALAMAN USER) --}}
    @auth
        @if(request()->is('user/*') && !request()->is('user/login') && !request()->is('user/register'))
        <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 py-2 px-4 z-50">
            <div class="flex justify-around items-center">
                {{-- Home / Dashboard User --}}
                <a href="{{ route('user.dashboard') }}" class="flex flex-col items-center {{ request()->routeIs('user.dashboard') ? 'nav-active text-green-600' : 'text-gray-500' }}">
                    <i class="fas fa-home text-xl"></i>
                    <span class="text-xs mt-1">Home</span>
                </a>
                
                {{-- Pesanan --}}
                <a href="{{ route('user.orders.index') }}" class="flex flex-col items-center {{ request()->routeIs('user.orders.*') ? 'nav-active text-green-600' : 'text-gray-500' }}">
                    <i class="fas fa-clipboard-list text-xl"></i>
                    <span class="text-xs mt-1">Pesanan</span>
                </a>
                
                {{-- Promo --}}
                <a href="{{ route('user.promo.index') }}" class="flex flex-col items-center {{ request()->routeIs('user.promo.*') ? 'nav-active text-green-600' : 'text-gray-500' }}">
                    <i class="fas fa-tags text-xl"></i>
                    <span class="text-xs mt-1">Promo</span>
                </a>
                
                {{-- Profil --}}
                <a href="{{ route('user.profile.index') }}" class="flex flex-col items-center {{ request()->routeIs('user.profile.*') ? 'nav-active text-green-600' : 'text-gray-500' }}">
                    <i class="fas fa-user text-xl"></i>
                    <span class="text-xs mt-1">Profil</span>
                </a>
            </div>
        </nav>
        @endif
    @endauth

    @stack('scripts')
</body>
</html>