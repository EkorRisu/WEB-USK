@extends('layouts.user') {{-- Pastikan ini sesuai dengan layout user Anda --}}

@section('content')
<div class="bg-gray-900 shadow-2xl p-6 md:p-10 max-w-7xl mx-auto my-8 rounded-xl border border-gray-700">
    
    <div class="header-section mb-8 border-b border-gray-700 pb-4">
        <h1 class="text-2xl md:text-3xl font-extrabold text-white mb-2">❤️ Wishlist Saya</h1>
        <p class="text-gray-400">Semua produk yang Anda simpan untuk nanti.</p>
    </div>

    <div class="mb-6">
        <a href="{{ route('user.dashboard') }}" 
           class="text-pink-400 hover:text-pink-300 transition duration-200">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    <div class="space-y-6">
        
        @forelse ($wishlistItems as $item)
            {{-- Pastikan item produknya masih ada --}}
            @if($item->produk)
                <div class="flex flex-col md:flex-row items-center bg-gray-800 p-4 rounded-lg shadow-lg border border-gray-700 gap-4">
                    
                    <img src="{{ asset('storage/' . $item->produk->foto) }}" alt="{{ $item->produk->nama }}" 
                         class="w-32 h-32 object-cover rounded-lg flex-shrink-0">

                    <div class="flex-grow text-center md:text-left">
                        <span class="text-xs font-semibold text-pink-400 uppercase">{{ $item->produk->kategori->nama ?? 'Tanpa Kategori' }}</span>
                        <h3 class="text-xl font-bold text-white">{{ $item->produk->nama }}</h3>
                        <p class="text-lg text-gray-300 font-semibold">Rp {{ number_format($item->produk->harga, 0, ',', '.') }}</p>
                        <span class="text-sm {{ $item->produk->stok > 0 ? 'text-green-400' : 'text-red-400' }}">
                            Stok: {{ $item->produk->stok > 0 ? $item->produk->stok : 'Habis' }}
                        </span>
                    </div>

                    <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                        
                        <form action="{{ route('user.wishlist.destroy', $item->id) }}" method="POST" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200 font-semibold">
                                Hapus
                            </button>
                        </form>
                        
                        @if($item->produk->stok > 0)
                            <form action="{{ route('user.cart.add', $item->produk->id) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" 
                                        class="w-full bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700 transition duration-200 font-semibold">
                                    🛒 Pindah ke Keranjang
                                </button>
                            </form>
                        @else
                            <button type="button" disabled 
                                    class="w-full bg-gray-500 text-gray-300 px-4 py-2 rounded-lg cursor-not-allowed font-semibold">
                                Stok Habis
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        @empty
            <div class="text-center py-10 bg-gray-800 rounded-lg">
                <p class="text-2xl text-gray-400">Wishlist Anda masih kosong.</p>
            </div>
        @endforelse

    </div>

</div>
@endsection