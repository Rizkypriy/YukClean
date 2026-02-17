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
                    <p class="text-3xl font-bold mt-2">{{ $stats['total_orders_today'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Pesanan Sedang Berjalan --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-600">Pesanan Sedang Berjalan</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['active_orders'] ?? 0 }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-spinner text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Pesanan Selesai --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-600">Pesanan Selesai</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['completed_orders_today'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Petugas Aktif --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-600">Total Petugas Aktif</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['active_cleaners'] ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection