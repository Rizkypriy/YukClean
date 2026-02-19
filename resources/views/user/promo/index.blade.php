@extends('layouts.app')

@section('title', 'Promo')

@section('content')
<div class="pb-24 bg-[#e8fdf3;]">
    {{-- Header dengan gradient --}}
    <div class="rounded-b-2xl p-5 text-white shadow-lg relative overflow-hidden"
         style="background: linear-gradient(135deg, #00bda2 0%, #00c85f 100%);">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Yuk Clean</h1>
        </div>

        {{-- Welcome Message --}}
        <div class="mb-6">
            <h2 class="text-xl font-semibold">Promo & Bundling</h2>
            <p class="text-sm opacity-90 mt-1">Dapatkan penawaran terbaik untuk layanan kebersihan</p>
        </div>

        {{-- Search Bar untuk Cek Promo --}}
        <div class="flex items-center gap-2">
            <input type="text" id="promoCode" placeholder="Masukkan kode promo"
                class="w-full px-4 py-3 rounded-lg text-gray-900 placeholder-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-green-300">
            <button id="checkPromoBtn" 
                class="bg-white text-green-600 px-6 py-3 rounded-lg text-sm font-medium hover:shadow-lg transition-all duration-300 whitespace-nowrap border border-green-200 hover:border-green-300">
                Pakai
            </button>
        </div>
        <div id="promoMessage" class="mt-2 text-sm text-white hidden"></div>
    </div>

    {{-- Promo List --}}
    <div class="px-5 mt-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Promo Spesial</h2>
        
        @forelse($promos as $promo)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-3 hover:shadow-md transition">
            <div class="flex items-start gap-4">
                {{-- Discount Badge --}}
                <div class="bg-green-100 rounded-xl p-3 text-center min-w-20">
                    <span class="block text-2xl font-bold text-green-600">
                        {{ $promo->discount_type == 'percentage' ? $promo->discount_value.'%' : 'Rp '.number_format($promo->discount_value,0,',','.') }}
                    </span>
                    <span class="text-xs text-gray-600">s.d @if($promo->valid_until)
            {{ \Carbon\Carbon::parse($promo->valid_until)->format('d M Y') }}
        @else
            Tanpa batas
        @endif</span>  {{-- Sesuai database --}}
                </div>
                
                {{-- Promo Details --}}
                <div class="flex-1">
                    <h3 class="font-bold text-lg text-gray-800">{{ $promo->title }}</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $promo->description }}</p>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-tag mr-1"></i>
                        Min. transaksi: Rp {{ number_format($promo->min_purchase, 0, ',', '.') }}  {{-- Sesuai database --}}
                    </p>
                    
                    {{-- Promo Code --}}
                    <div class="mt-3 flex items-center gap-2">
                        <span class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded-lg font-mono">
                            {{ $promo->code }}
                        </span>
                        <button onclick="copyPromoCode('{{ $promo->code }}')" 
                                class="text-blue-600 text-xs hover:text-blue-800">
                            <i class="far fa-copy mr-1"></i>Salin
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <p class="text-gray-500">Belum ada promo tersedia</p>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('checkPromoBtn')?.addEventListener('click', function() {
        const code = document.getElementById('promoCode').value;
        const messageDiv = document.getElementById('promoMessage');
        
        if (!code) {
            showMessage('Masukkan kode promo', 'red');
            return;
        }
        
        // PERBAIKAN: Ubah route dari 'promo.check' menjadi 'user.promo.check'
        fetch('{{ route("user.promo.check") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                code: code,
                subtotal: 0
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.valid) {
                showMessage('Kode promo valid! ' + data.promo.title, 'green');
            } else {
                showMessage(data.message, 'red');
            }
        })
        .catch(error => {
            showMessage('Terjadi kesalahan', 'red');
        });
    });

    function showMessage(text, type) {
        const messageDiv = document.getElementById('promoMessage');
        messageDiv.className = 'mt-2 text-sm text-' + type + '-600';
        messageDiv.textContent = text;
        messageDiv.classList.remove('hidden');
        
        setTimeout(() => {
            messageDiv.classList.add('hidden');
        }, 5000);
    }

    function copyPromoCode(code) {
        navigator.clipboard.writeText(code).then(function() {
            alert('Kode promo berhasil disalin!');
        }, function() {
            alert('Gagal menyalin kode');
        });
    }
</script>

@endpush
@endsection