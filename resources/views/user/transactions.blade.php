@extends('layouts.user')

@section('content')
<div x-data="{ 
    showReviewModal: false, 
    selectedItemId: null, 
    selectedProdukNama: '',
    selectedRating: null, // Digunakan untuk menyimpan rating yang dipilih
    selectedReviewText: ''
}" 
class="bg-gray-900 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <h2 class="text-3xl font-extrabold text-white mb-8 text-center">📦 {{ __('app.transactions') }}</h2>

        @forelse($transaksi as $trx)
        <div class="bg-gray-800 rounded-lg shadow-xl mb-6 border-l-4
            @if($trx->status === 'pending') border-yellow-500 
            @elseif($trx->status === 'dikirim') border-blue-500 
            @elseif($trx->status === 'selesai') border-green-500 
            @else border-gray-600 @endif">
            
            <div class="p-6">
                {{-- ... (Header Transaksi) ... --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-gray-700 pb-4 mb-4">
                    <div>
                        <p class="text-lg font-semibold text-white">🧾 
                            <span class="text-gray-300">{{ __('app.invoice') }}:</span> #{{ str_pad($trx->id, 6, '0', STR_PAD_LEFT) }}
                        </p>
                        <p class="mt-1 text-sm text-gray-400">
                            {{ __('app.date') }}: {{ \Carbon\Carbon::parse($trx->created_at)->translatedFormat('d F Y') }}
                        </p>
                    </div>
                    <div class="flex flex-col items-start sm:items-end gap-2 mt-4 sm:mt-0">
                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full
                            @if($trx->status === 'pending') bg-yellow-200 text-yellow-900 
                            @elseif($trx->status === 'dikirim') bg-blue-200 text-blue-900 
                            @elseif($trx->status === 'selesai') bg-green-200 text-green-900 
                            @else bg-gray-600 text-gray-100 @endif">
                            {{ ucfirst($trx->status) }}
                        </span>
                        @if ($trx->status === 'dikirim')
                        <form method="POST" action="{{ route('user.transactions.selesai', $trx->id) }}">
                            @csrf
                                <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition font-semibold shadow-md">
                                ✔ {{ __('app.accept_order') }}
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                <div class="product-details-section">
                    <p class="text-md font-bold text-gray-300 mb-3">🛍 {{ __('app.product_details') }}</p>
                    <div class="space-y-4">
                        @if ($trx->items && $trx->items->isNotEmpty())
                        @foreach ($trx->items as $item)
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between text-sm p-4 bg-gray-700 rounded-lg">
                            
                            <div class="flex items-center gap-4">
                                <img src="{{ $item->produk ? asset('storage/' . $item->produk->foto) : 'https://via.placeholder.com/60' }}"
                                    alt="{{ $item->nama_barang }}"
                                    class="w-12 h-12 object-cover rounded-md border border-gray-600">
                                <div>
                                    <span class="text-white font-medium">{{ $item->nama_barang }}</span>
                                    <span class="ml-3 text-gray-400">x{{ $item->jumlah }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-3 sm:mt-0 gap-4">
                                <div class="font-semibold text-gray-200 text-base">
                                    Rp {{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}
                                </div>

                                @if ($trx->status === 'selesai')
                                    @php
                                        $existingReview = $item->review;
                                    @endphp

                                    @if($existingReview)
                                        <button type="button" 
                                            @click="
                                                showReviewModal = true; 
                                                selectedItemId = {{ $item->id }}; 
                                                selectedProdukNama = {{ json_encode($item->nama_barang) }};
                                                selectedRating = {{ $existingReview->rating }};
                                                selectedReviewText = {{ json_encode($existingReview->review_text) }};
                                            "
                            class="bg-gray-600 hover:bg-gray-500 text-white px-3 py-1 rounded-lg text-xs transition font-semibold shadow">
                                {{ __('app.edit_review') }}
                                        </button>
                                    @else
                                        <button type="button" 
                                            @click="
                                                showReviewModal = true; 
                                                selectedItemId = {{ $item->id }}; 
                                                selectedProdukNama = {{ json_encode($item->nama_barang) }};
                                                selectedRating = null;
                                                selectedReviewText = '';
                                            "
                                            class="bg-pink-600 hover:bg-pink-700 text-white px-3 py-1 rounded-lg text-xs transition font-semibold shadow">
                                                {{ __('app.give_review') }}
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                        @endforeach
                        @else
                        <p class="text-sm text-gray-400">{{ __('app.no_product_details') }}</p>
                        @endif
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-700 flex justify-between items-center">
                    <p class="text-md font-semibold text-gray-300">{{ __('app.total_payment') }}:</p>
                    <p class="text-2xl font-bold text-green-400">
                        Rp {{ number_format($trx->total, 0, ',', '.') }}
                    </p>
                </div>
                {{-- Tampilkan pesan/admin note jika ada --}}
                @if(!empty($trx->note) || !empty($trx->pesan_admin))
                <div class="mt-4 p-3 bg-gray-200 dark:bg-gray-700 border-t border-gray-300 dark:border-gray-700 text-sm text-gray-800 dark:text-gray-300 rounded-lg">
                    <strong class="inline-block mr-2">📢 {{ __('app.seller_message') }}:</strong>
                    <span>{{ $trx->note ?? $trx->pesan_admin }}</span>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-16 bg-gray-800 rounded-lg">
            <p class="text-2xl text-gray-400">🚫 {{ __('app.no_transactions') }}</p>
            <p class="text-gray-500 mt-2">{{ __('app.no_transactions_sub') }}</p>
        </div>
        @endforelse

        <div class="mt-8">
            {{ $transaksi->links() }}
        </div>
    </div>

    {{-- MODAL REVIEW DENGAN STAR RATING INTERAKTIF BARU --}}
    {{-- <div x-show="showReviewModal" 
          @keydown.escape.window="showReviewModal = false"
          class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4"
          style="display: none;"
          x-init="$watch('showReviewModal', val => { if (!val) { selectedRating = null; selectedReviewText = ''; } })">
        
        <div @click.outside="showReviewModal = false" 
              class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg p-6">
            
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ __('app.give_review') }}</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('app.for_product') }} <strong x-text="selectedProdukNama"></strong></p>

            <form action="{{ route('user.reviews.store') }}" method="POST" id="reviewForm">
                @csrf
                <input type="hidden" name="transaction_item_id" x-bind:value="selectedItemId">
                 --}}
                {{-- INPUT RATING SEBENARNYA (Hidden) --}}
                {{-- <input type="hidden" name="rating" x-bind:value="selectedRating" required>  --}}

                {{-- Star Rating Interaktif
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('app.your_rating') }}</label>
                     --}}
                    {{-- Alpine Component untuk Rating --}}
                    {{-- <div x-data="{ tempRating: null }" class="flex flex-row-reverse justify-end items-center text-gray-400"> --}}
                        
                        {{-- Loop 5 bintang, dari 1 sampai 5 (index 0 adalah bintang 5) --}}
                        {{-- <template x-for="index in 5" :key="index">
                            <button type="button"
                                    :aria-label="'Bintang ' + (5 - index + 1)"
                                    @click.prevent="selectedRating = (5 - index + 1)"
                                    @mouseenter="tempRating = (5 - index + 1)"
                                    @mouseleave="tempRating = selectedRating"
                                    class="p-0.5 transition-colors duration-200 focus:outline-none">
                                <svg class="w-10 h-10 fill-current" 
                                    :class="{
                                        'text-yellow-400': (tempRating !== null ? tempRating : selectedRating) >= (5 - index + 1),
                                        'text-gray-400 dark:text-gray-600': (tempRating !== null ? tempRating : selectedRating) < (5 - index + 1),
                                    }"
                                    viewBox="0 0 24 24">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                </svg>
                            </button>
                        </template>
                    </div>
                    <p x-show="selectedRating" class="mt-2 text-sm text-center text-gray-500 dark:text-gray-400">
                        {{ __('app.you_gave_rating') }} <span class="font-bold text-yellow-400" x-text="selectedRating"></span> {{ __('app.stars') }}
                    </p>
                </div> --}}

                {{-- <div class="mb-6">
                    <label for="review_text" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('app.review_text_label') }}</label>
                    <textarea name="review_text" id="review_text" rows="4" 
                            x-model="selectedReviewText"
                            class="w-full p-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-pink-500 focus:border-pink-500 transition-colors duration-300"
                            placeholder="Tulis pengalaman Anda dengan buku ini..."
                            ></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="showReviewModal = false"
                            class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-4 py-2 rounded-lg transition font-medium">
                        {{ __('app.cancel') }}
                    </button>
                    <button type="submit"
                            class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-lg transition font-semibold"
                            :disabled="!selectedRating" {{-- Nonaktif jika rating belum dipilih --}}
                            {{-- :class="{'opacity-50 cursor-not-allowed': !selectedRating}">
                        {{ __('app.submit_review') }}
                    </button> --}}
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Menggunakan kelas utilitas Tailwind untuk warna dan ukuran. 
       W-10 dan H-10 digunakan untuk ukuran bintang (40px). 
       Warna default dan hover/aktif diatur langsung di Alpine :class.
    */
</style>
@endpush