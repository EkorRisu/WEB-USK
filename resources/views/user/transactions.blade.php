@extends('layouts.user')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">📦 Riwayat Transaksi</h2>

    @forelse($transaksi as $trx)
    <div class="bg-white rounded-lg p-6 mb-6 shadow-xl text-gray-800 border-l-4
@if($trx->status === 'pending') border-yellow-500 
@elseif($trx->status === 'dikirim') border-blue-500 
@elseif($trx->status === 'selesai') border-green-500 
@else border-gray-300 @endif">

        <div class="flex justify-between items-center border-b pb-4 mb-4">
            <div>
                <p class="text-lg font-semibold">🧾 <span class="text-gray-700">Invoice:</span> #{{ str_pad($trx->id, 6,
                    '0', STR_PAD_LEFT) }}</p>
                <p class="mt-1 text-sm text-gray-500">Tanggal: {{
                    \Carbon\Carbon::parse($trx->created_at)->translatedFormat('d F Y') }}</p>
            </div>

            <div class="flex flex-col items-end gap-2">
                <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full
@if($trx->status === 'pending') bg-yellow-100 text-yellow-800 
@elseif($trx->status === 'dikirim') bg-blue-100 text-blue-800 
@elseif($trx->status === 'selesai') bg-green-100 text-green-800 
@else bg-gray-200 text-gray-600 @endif">
                    {{ ucfirst($trx->status) }}
                </span>
                @if ($trx->status === 'dikirim')
                <form method="POST" action="{{ route('user.transactions.selesai', $trx->id) }}">
                    @csrf
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition font-semibold">
                        ✔ Terima Pesanan
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Bagian Detail Produk --}}
        <div class="product-details-section">
            <p class="text-md font-bold text-gray-700 mb-2">🛍 Detail Produk</p>
            <div class="space-y-3">
                @if ($trx->items && $trx->items->isNotEmpty())
                @foreach ($trx->items as $item)
                <div class="flex justify-between items-center text-sm p-3 bg-gray-50 rounded-md">
                    <div class="flex items-center **gap-3**"> {{-- Tambahkan gap-3 untuk jarak --}}

                        {{-- START: Tambahan Kode untuk Gambar --}}
                        @if ($item->produk->gambar ?? false)
                        <img src="{{ asset($item->produk->gambar) }}"
                            alt="{{ $item->produk->nama ?? 'Produk Dihapus' }}"
                            class="**w-10 h-10 object-cover rounded-md border**">
                        @endif
                        {{-- END: Tambahan Kode untuk Gambar --}}

                        <div class="flex items-center">
                            <span class="text-gray-900 font-medium">{{ $item->produk->nama ?? 'Produk Dihapus' }}</span>
                            <span class="ml-3 text-gray-500">x{{ $item->jumlah }}</span>
                        </div>
                    </div>
                    <div class="font-semibold text-gray-700">
                        Rp {{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}
                    </div>
                </div>
                @endforeach
                @else
                <p class="text-sm text-gray-500">Tidak ada detail produk yang tersedia.</p>
                @endif
            </div>
        </div>
        {{-- Akhir Bagian Detail Produk --}}

        <div class="mt-4 pt-4 border-t flex justify-between items-center">
            <p class="text-md font-semibold text-gray-600">Total Pembayaran:</p>
            <p class="text-2xl font-bold text-green-600">
                Rp {{ number_format($trx->total, 0, ',', '.') }}
            </p>
        </div>
    </div>
    @empty
    <p class="text-gray-500 text-center text-lg">🚫 Belum ada transaksi yang tercatat.</p>
    @endforelse
</div>
@endsection