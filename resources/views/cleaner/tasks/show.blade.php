{{-- resources/views/cleaner/tasks/show.blade.php --}}
@extends('cleaner.layouts.app')

@section('title', 'Detail Tugas')

@section('content')
<div class="min-h-screen bg-white pb-24">
    <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 text-white">
        <a href="{{ route('cleaner.tasks.index') }}" class="inline-flex items-center text-white mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold">Detail Tugas</h1>
    </div>

    <div class="p-5">
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <p class="text-gray-600">Halaman detail tugas sedang dalam pengembangan.</p>
        </div>
    </div>
</div>
@endsection