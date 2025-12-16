@extends('layouts.user') {{-- Pastikan layout ini ada dan memuat SweetAlert2 & Tailwind CSS --}}

{{-- Tambahkan CSRF Token di head layout jika belum ada, atau di sini jika perlu --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<div class="h-screen bg-gray-900 overflow-hidden">
    <div class="h-full flex flex-col">
       

        {{-- POS Navigation Tabs --}}
        <div class="bg-gray-800 px-6 py-3 border-b border-gray-700">
            <div class="flex space-x-1">
                {{-- <button class="bg-orange-500 text-white px-6 py-2 rounded-lg font-medium">
                    🛍️ Keranjang
                </button> --}}
                <a href="{{ route('user.transactions') }}" class="bg-gray-700 hover:bg-orange-500 text-white px-4 py-2 rounded font-medium transition-colors">
                    Pesanan
                </a>
                @if(config('fitur.wishlist'))
                <a href="{{ route('user.wishlist.index') }}" class="bg-gray-700 hover:bg-orange-500 text-white px-4 py-2 rounded font-medium transition-colors">
                    Favorit
                </a>
                @endif
            </div>
        </div>

        {{-- POS Filter Section --}}
        <div class="bg-gray-800 px-6 py-4 border-b border-gray-700">
            <form method="GET" action="{{ route('user.dashboard') }}" id="filter-form" class="flex flex-wrap items-center gap-4">
                {{-- Search Input --}}
                <div class="relative flex-1 min-w-64">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" id="search-input" placeholder="Cari kopi favorit Anda..."
                        value="{{ request('search') }}"
                        class="bg-gray-700 border border-gray-600 text-white placeholder-gray-400 pl-10 pr-4 py-2 rounded-lg w-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    <div id="search-results" class="absolute w-full z-30 mt-1 rounded-lg shadow-xl bg-gray-700 border border-gray-600 max-h-60 overflow-y-auto hidden"></div>
                </div>
                
                {{-- Category Filter --}}
                <select name="kategori" class="bg-gray-700 border border-gray-600 text-white px-4 py-2 rounded focus:ring-2 focus:ring-orange-500">
                    <option value="">Semua Menu</option>
                    @foreach ($kategori as $kat)
                    <option value="{{ $kat->id }}" {{ request('kategori')==$kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                    @endforeach
                </select>
                
                {{-- Items Per Page --}}
                <select name="perpage" class="bg-gray-700 border border-gray-600 text-white px-4 py-2 rounded-lg focus:ring-2 focus:ring-orange-500">
                    @php
                    $perPageOptions = [8, 12, 20, 50];
                    $currentPerPage = request('perpage') ?? 20; // Default 20 items per page
                    @endphp
                    @foreach ($perPageOptions as $option)
                    <option value="{{ $option }}" {{ $currentPerPage == $option ? 'selected' : '' }}>{{ $option }} items</option>
                    @endforeach
                </select>
                
                {{-- Filter Button --}}
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded font-medium transition-colors">
                    Filter
                </button>
            </form>
        </div>

        {{-- Main POS Content --}}
        <div class="flex-1 flex overflow-hidden" x-data="{ showCart: false, cartItems: [], cartTotal: 0 }">
            {{-- Products Area --}}
            <div class="flex-1 flex flex-col overflow-hidden">

                {{-- Product Grid Container --}}
                <div class="flex-1 overflow-y-auto bg-gray-900 p-6">
                
                    {{-- Mobile Cart Toggle --}}
                <div class="lg:hidden mb-4 relative z-10">
                    <button type="button" @click="showCart = !showCart" 
                        onclick="toggleMobileCart()" 
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white px-4 py-3 rounded-lg font-semibold flex items-center justify-center space-x-2 transition-colors shadow-lg">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                            </svg>
                            <span>Keranjang (<span id="mobile-cart-count">0</span>)</span>
                        </button>
                    </div>

                    {{-- PRODUCT GRID --}}
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                        @forelse ($produk as $item)

                        <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 js-product-card group cursor-pointer"
                            data-product-id="{{ $item->id }}"
                            data-name="{{ strtolower($item->nama) }}"
                            data-kategori="{{ strtolower($item->kategori->nama) }}"
                            data-desc="{{ strtolower(strip_tags($item->deskripsi ?? '')) }}"
                            data-product-name="{{ $item->nama }}"
                            data-product-price="{{ $item->harga }}"
                            data-product-stock="{{ $item->stok }}"
                            data-product-image="{{ asset('storage/' . $item->foto) }}">

                            {{-- Product Image --}}
                            <div class="relative aspect-square overflow-hidden">
                                @if($item->foto)
                                    <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama }}"
                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                                        loading="lazy" decoding="async" 
                                        sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw">
                                @else
                                    <div class="w-full h-full bg-gray-700 flex flex-col items-center justify-center text-gray-400">
                                        <div class="text-3xl mb-2 opacity-60">🍵</div>
                                        <div class="text-xs font-medium opacity-70">No Image</div>
                                    </div>
                                @endif

                                @if(config('fitur.favorit'))
                                {{-- FITUR FAVORIT/LIKE - DAPAT DIKONFIGURASI --}}
                                <button type="button" class="group btn-favorite absolute top-3 left-3 z-10 flex items-center gap-1.5 px-2.5 py-1 
                                                     bg-white/80 dark:bg-gray-700/80 
                                                     rounded-full text-gray-700 dark:text-gray-200 
                                                     hover:text-pink-500 dark:hover:text-pink-400 
                                                     transition duration-200 
                                                     [&.active]:bg-pink-50 dark:[&.active]:bg-[#4b1b28] 
                                                     {{ $item->is_favorited ? 'active' : '' }}"
                                    data-route="{{ route('user.favorite.toggle', $item) }}" title="Favorit">

                                    <span
                                        class="icon-heart 
                                                     transition-all duration-200 ease-in-out 
                                                     grayscale scale-100 opacity-70 
                                                     group-[.active]:grayscale-0 group-[.active]:scale-110 group-[.active]:opacity-100">
                                        ♥
                                    </span>

                                    <span class="like-count text-sm font-bold 
                                                     transition-all duration-200 ease-in-out 
                                                     text-gray-600 dark:text-gray-300 
                                                     group-[.active]:text-red-700 dark:group-[.active]:text-pink-300 
                                                     group-[.active]:font-extrabold">
                                        {{ $item->favorited_by_count }}
                                    </span>
                                </button>
                                @endif

                                @if(config('fitur.wishlist'))
                                @php
                                $wishlistItemId = $wishlistItems->get($item->id);
                                @endphp

                                {{-- FITUR WISHLIST - DAPAT DIKONFIGURASI --}}
                                @if($wishlistItemId)
                                <button type="button" onclick="removeFromWishlist({{ $wishlistItemId }})"
                                    class="absolute top-3 right-3 z-10 p-2 bg-white/80 dark:bg-gray-700/80 rounded-full text-yellow-400 hover:text-white hover:bg-yellow-500 transition duration-200"
                                    title="Hapus dari Wishlist">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.31h5.513c.498 0 .701.663.337.986l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-3.352a.563.563 0 00-.65 0L6.09 19.06a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.337-.986h5.513a.563.563 0 00.475-.31L11.48 3.5z" />
                                    </svg>
                                </button>
                                @else
                                <button type="button" onclick="addToWishlist({{ $item->id }})"
                                    class="absolute top-3 right-3 z-10 p-2 bg-white/80 dark:bg-gray-700/80 rounded-full text-gray-700 dark:text-gray-200 hover:text-pink-500 dark:hover:text-pink-400 transition duration-200"
                                    title="Tambah ke Wishlist">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.31h5.513c.498 0 .701.663.337.986l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-3.352a.563.563 0 00-.65 0L6.09 19.06a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.337-.986h5.513a.563.563 0 00.475-.31L11.48 3.5z" />
                                    </svg>
                                </button>
                                @endif
                                @endif
                            </div>

                            {{-- BLOK 2: KONTEN TEKS --}}
                            <div class="p-4 flex flex-col flex-grow">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-700 bg-gray-200 rounded mb-3 self-start">
                                    {{ $item->kategori->nama }}
                                </span>
                                <h2 class="text-lg font-semibold text-white mb-2 flex-grow leading-tight">{{
                                    $item->nama }}</h2>
                                @if(config('fitur.review') || config('fitur.rating'))
                                {{-- FITUR RATING/REVIEW - DAPAT DIKONFIGURASI --}}


                                {{-- FITUR RATING, ULASAN, DAN PENJUALAN - TIDAK ADA DI PERSYARATAN --}}
                                <div class="space-y-2 mb-3">
                                    <div class="flex items-center space-x-2">
                                        @php
                                        $rating = round($item->average_rating ?? 0, 1);
                                        $reviewsCount = $item->reviews_count ?? 0;
                                        $maxStars = 5;
                                        @endphp
                                        <div class="flex text-orange-400">
                                            @for ($i = 1; $i <= $maxStars; $i++) @if ($rating>= $i)
                                                <span>★</span>
                                                @elseif ($rating > ($i - 1))
                                                <span class="half-star">★</span>
                                                @else
                                                <span class="text-gray-500">★</span>
                                                @endif
                                                @endfor
                                        </div>
                                        <span class="text-sm font-semibold text-white">{{ $rating }}</span>
                                        <span class="text-sm text-gray-400">({{ $reviewsCount }} reviews)</span>
                                    </div>

                                    <div class="text-sm text-gray-400">
                                        <span>{{ $item->transaction_items_sum_jumlah ?? 0 }} terjual</span>
                                    </div>
                                </div>
                                @endif
                                {{-- FITUR TOMBOL LIHAT DETAIL/ULASAN --}}
                                <button type="button"
                                    class="inline-flex items-center text-sm text-orange-400 hover:text-orange-300 font-medium mb-4 hover:underline transition-colors"
                                    onclick='showProductDetails({{ $item->id }}, {{ json_encode($item->nama) }}, {{ json_encode($item->deskripsi) }})'>
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Lihat Detail
                                </button>

                                <div class="bg-gray-700 rounded p-3 mb-4">
                                    <div class="flex justify-between items-center">
                                        <p class="text-xl text-orange-400 font-bold">Rp {{
                                            number_format($item->harga, 0, ',', '.') }}</p>
                                        @if(config('fitur.show_stock'))
                                        <span class="px-2 py-1 text-xs font-bold rounded-full {{ $item->stok > 0 ? 'text-green-800 bg-green-100' : 'text-red-800 bg-red-100' }}">
                                            Stok: {{ $item->stok }}
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Tombol Keranjang --}}
                                <div class="mt-auto">
                                    @if ($item->stok > 0)
                                    <button type="button" onclick="addToCart({{ $item->id }})"
                                        class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded w-full transition-colors font-medium">
                                        Tambah ke Keranjang
                                    </button>
                                    @else
                                    <button type="button" disabled
                                        class="bg-gray-600 text-gray-400 px-4 py-2 rounded w-full cursor-not-allowed font-medium">
                                        Stok Habis
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @empty
                        <div class="col-span-full text-center py-16">
                            <div class="bg-gray-800 rounded-lg p-8">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.467-.881-6.08-2.33m0 0L3.34 15.249a8 8 0 1017.32 0l-2.58-2.579z"></path>
                                </svg>
                                <h3 class="text-xl font-bold text-white mb-2">Menu Tidak Ditemukan</h3>
                                <p class="text-gray-400">Tidak ada produk yang sesuai dengan filter Anda</p>
                            </div>
                        </div>
                        @endforelse
                    </div>

                    {{-- PAGINATION --}}
                    <div class="mt-6 flex justify-center">
                        {{ $produk->links() }}
                    </div>
                </div>
            </div>

            {{-- Cart Sidebar --}}
            <div class="fixed inset-y-0 right-0 z-50 w-full sm:w-80 bg-gray-800 shadow-2xl transform transition-transform duration-300 ease-in-out
                        lg:relative lg:inset-auto lg:z-auto lg:w-80 xl:w-96 lg:translate-x-0 border-l border-gray-700 overflow-x-hidden"
                :class="{'translate-x-0': showCart, 'translate-x-full lg:translate-x-0': !showCart}"
                @click.away="closeMobileCart()" x-cloak
                id="mobile-cart-sidebar">
                
                {{-- Cart Header --}}
                <div class="flex items-center justify-between px-3 py-2 border-b border-gray-700 bg-gray-800">
                    <h2 class="text-sm font-semibold text-white">Keranjang</h2>
                    <button @click="showCart = false" class="lg:hidden p-1 rounded-full hover:bg-gray-700 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Cart Content --}}
                <div class="flex flex-col h-full overflow-x-hidden">
                    {{-- Cart Items --}}
                    <div class="flex-1 overflow-y-auto overflow-x-hidden px-2 py-3" id="pos-cart-items">
                        <div class="text-center py-4 text-gray-400">
                            <div class="w-10 h-10 mx-auto mb-2 bg-gray-700 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <p class="text-xs">Keranjang kosong</p>
                            <p class="text-xs mt-1 opacity-75">Klik menu</p>
                        </div>
                    </div>

                    {{-- Cart Footer --}}
                    <div class="border-t border-gray-700 px-3 py-3 bg-gray-900">
                        {{-- Cart Summary --}}
                        <div class="mb-3">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-gray-400 text-xs">Items:</span>
                                <span class="text-white text-xs" id="cart-items-count">0 items</span>
                            </div>
                            <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-700">
                                <span class="text-white font-medium text-sm">Total:</span>
                                <span class="font-bold text-lg text-orange-400" id="cart-total-display">Rp 0</span>
                            </div>
                        </div>
                        
                        {{-- Action Buttons --}}
                        <div class="space-y-2" id="cart-actions">
                            {{-- Primary Checkout Button --}}
                            <button type="button" onclick="proceedToPayment()" 
                                class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white py-3 rounded-lg font-bold text-sm transition-all duration-200 transform hover:scale-105 shadow-lg">
                                Bayar Sekarang
                            </button>
                            
                            {{-- Clear Button --}}
                            <button type="button" onclick="clearPOSCart()" 
                                class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded text-sm font-medium transition-colors">
                                Kosongkan
                            </button>
                        </div>
                        
                        {{-- Empty Cart Message --}}
                        <div class="text-center py-2" id="empty-cart-message">
                            <p class="text-gray-400 text-xs">Keranjang kosong</p>
                            <p class="text-gray-500 text-xs mt-1">Pilih menu untuk mulai</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </form> {{-- End of filter form --}}
    </div>
</div>

<style>
    /* POS Layout Styles */
    body {
        background-color: #111827 !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .line-clamp-2 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }
    
    /* Custom Scrollbar for Cart */
    #pos-cart-items::-webkit-scrollbar {
        width: 6px;
    }
    
    #pos-cart-items::-webkit-scrollbar-track {
        background: #374151;
        border-radius: 3px;
    }
    
    #pos-cart-items::-webkit-scrollbar-thumb {
        background: #6b7280;
        border-radius: 3px;
    }
    
    #pos-cart-items::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
    
    /* Product Grid Responsiveness */
    @media (max-width: 640px) {
        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    
    @media (min-width: 641px) and (max-width: 768px) {
        .md\:grid-cols-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    /* Simplified Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.25rem;
    }
    
    .pagination a, .pagination span {
        padding: 0.5rem 0.75rem;
        background-color: #374151;
        color: #f3f4f6;
        border-radius: 0.25rem;
        text-decoration: none;
        transition: background-color 0.2s;
        font-size: 0.875rem;
    }
    
    .pagination a:hover {
        background-color: #f97316;
    }
    
    .pagination span[aria-current="page"] span {
        background-color: #f97316;
        color: white;
        font-weight: 600;
    }
    
    .pagination span[aria-disabled="true"] span {
        background-color: #4b5563;
        color: #6b7280;
        cursor: not-allowed;
    }
    
    /* Product Card Animation - Simplified */
    .js-product-card {
        transition: all 0.2s ease;
    }
    
    .js-product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    /* Search Results Styling */
    #search-results {
        backdrop-filter: blur(10px);
    }
    
    #search-results .hover\:bg-gray-700:hover {
        background-color: #374151;
    }
    
    /* Mobile Cart Overlay */
    @media (max-width: 1023px) {
        .lg\:relative.lg\:inset-auto {
            position: fixed;
            inset: 0;
        }
        
        /* Ensure mobile cart button is clickable */
        button[onclick="toggleMobileCart()"] {
            position: relative;
            z-index: 20;
            pointer-events: auto;
            touch-action: manipulation;
        }
        
        /* Prevent cart from covering content */
        #mobile-cart-sidebar {
            top: 0;
            right: 0;
            height: 100vh;
            pointer-events: auto;
        }
        
        /* Improve button tap area */
        .lg\:hidden button {
            min-height: 48px;
            -webkit-tap-highlight-color: transparent;
        }
    }
    
    /* Animation Classes */
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .5;
        }
    }
    
    /* Payment Method Buttons */
    .payment-method-btn {
        cursor: pointer;
        text-align: left;
        width: 100%;
        outline: none;
    }
    
    .payment-method-btn:focus {
        outline: 2px solid #f97316;
        outline-offset: 2px;
    }
    
    /* Cart UI Initial States */
    #cart-actions {
        display: block; /* Show by default for debugging */
    }
    
    #empty-cart-message {
        display: none; /* Hide empty message initially */
    }
</style>

{{-- SCRIPT: Live Search, Local Filter, dan Fungsi Lain --}}
<script>
    // *******************************************************************
    // FUNGSI UTILITY GLOBAL (Wajib untuk Swal, dll)
    // *******************************************************************

    function getSwalTheme() {
        const isDarkMode = document.documentElement.classList.contains('dark');
        return {
            background: isDarkMode ? '#1f2937' : '#ffffff',
            titleColor: isDarkMode ? '#f9fafb' : '#111827',
            color: isDarkMode ? '#d1d5db' : '#374151'
        };
    }
    
    // FITUR RATING/REVIEW - TIDAK ADA DI PERSYARATAN
    
    function generateStarRating(rating) {
        let stars = '';
        const maxStars = 5;
        const fullStar = '★';
        
        for (let i = 1; i <= maxStars; i++) {
            if (rating >= i) {
                stars += fullStar;
            } else if (rating > (i - 1) && rating % 1 !== 0) {
                stars += fullStar; 
            } else {
                stars += `<span class="text-gray-300 dark:text-gray-600">${fullStar}</span>`;
            }
        }
        return stars;
    }

    function showProductDetails(produkId, productName, description) {
        const theme = getSwalTheme();
        const isDarkMode = document.documentElement.classList.contains('dark');
        
        let formattedDescription = `<p class="${isDarkMode ? 'text-gray-400' : 'text-gray-500'}">Tidak ada deskripsi untuk produk ini.</p>`;
        if (description) {
            formattedDescription = description.replace(/\n/g, '<br>');
        }

        @if (config('fitur.review') || config('fitur.rating'))
        // Jika fitur review/rating aktif, fetch data dari server
        fetch(`{{ url('user/produk') }}/${produkId}/reviews`) 
            .then(response => {
                if (!response.ok) {
                    if(response.status === 404) {
                        return { reviews: [], average_rating: 0, reviews_count: 0 };
                    }
                    throw new Error('Gagal memuat ulasan.');
                }
                return response.json();
            })
            .then(data => {
                const reviews = data.reviews || [];
                const avgRating = (typeof data.average_rating === 'number' && !isNaN(data.average_rating)) ? (Math.round(data.average_rating * 10) / 10) : 'N/A';
                const totalReviews = data.reviews_count || 0;
                
                let reviewsHtml = '';
                if (reviews.length > 0) {
                    reviewsHtml += `<h4 class="text-lg font-bold ${theme.titleColor === '#f9fafb' ? 'text-gray-100' : 'text-gray-900'} mb-3">Ulasan Pengguna (${totalReviews})</h4>`;
                    
                    reviewsHtml += reviews.map(review => {
                        const userClass = isDarkMode ? 'text-pink-300' : 'text-pink-600';
                        const reviewStars = generateStarRating(review.rating);
                        
                        return `
                            <div class="p-3 mb-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800">
                                <p class="text-sm font-semibold ${userClass}">${review.user_name || 'Pengguna Anonim'}</p>
                                <div class="flex text-yellow-400 text-sm mb-1">${reviewStars}</div>
                                <p class="text-gray-600 dark:text-gray-300">${review.comment}</p>
                            </div>
                        `;
                    }).join('');
                } else {
                    reviewsHtml = `<p class="${isDarkMode ? 'text-gray-400' : 'text-gray-500'}">Belum ada ulasan untuk produk ini.</p>`;
                }

                const modalHtml = `
                    <div style="text-align: left;">
                        <h3 class="text-xl font-extrabold ${theme.titleColor === '#f9fafb' ? 'text-white' : 'text-gray-900'} mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Deskripsi</h3>
                        <div class="mb-6 leading-relaxed">${formattedDescription}</div>
                        
                        <h3 class="text-xl font-extrabold ${theme.titleColor === '#f9fafb' ? 'text-white' : 'text-gray-900'} mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Rating Rata-Rata: ${avgRating} / 5</h3>
                        <div class="max-h-80 overflow-y-auto pr-3">
                            ${reviewsHtml}
                        </div>
                    </div>
                `;

                Swal.fire({
                    ...theme,
                    title: productName,
                    html: modalHtml,
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#DB2777',
                    width: '700px',
                    customClass: { htmlContainer: 'text-left p-4 leading-relaxed' }
                });
            })
            .catch(error => {
                 const modalHtmlOnlyDesc = `
                    <div style="text-align: left;">
                        <h3 class="text-xl font-extrabold ${theme.titleColor === '#f9fafb' ? 'text-white' : 'text-gray-900'} mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Deskripsi</h3>
                        <div class="mb-6 leading-relaxed">${formattedDescription}</div>
                        <p class="text-sm text-red-500 dark:text-red-400">Gagal memuat ulasan: ${error.message}</p>
                    </div>
                `;
                Swal.fire({
                    ...theme,
                    title: productName,
                    html: modalHtmlOnlyDesc,
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#DB2777',
                    width: '600px',
                    customClass: { htmlContainer: 'text-left p-4 leading-relaxed' }
                });
            });
        @else
        // Jika fitur review/rating tidak aktif, langsung tampilkan modal dengan deskripsi saja
        const modalHtml = `
            <div style="text-align: left;">
                <h3 class="text-xl font-extrabold ${theme.titleColor === '#f9fafb' ? 'text-white' : 'text-gray-900'} mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Deskripsi</h3>
                <div class="mb-6 leading-relaxed">${formattedDescription}</div>
            </div>
        `;

        Swal.fire({
            ...theme,
            title: productName,
            html: modalHtml,
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#f97316',
            width: '600px',
            customClass: { htmlContainer: 'text-left p-4 leading-relaxed' }
        });
        @endif
    }
    

    @if(config('fitur.wishlist'))
    // FITUR WISHLIST - DAPAT DIKONFIGURASI
    
    function addToWishlist(produkId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || null;
        fetch("/user/wishlist", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ produk_id: produkId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                Swal.fire({ ...getSwalTheme(), title: 'Berhasil!', text: data.message, icon: 'success', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                location.reload(); 
            } else {
                Swal.fire({ ...getSwalTheme(), title: 'Info', text: data.info || 'Produk ini sudah ada di wishlist Anda.', icon: 'info', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            }
        });
    }

    function removeFromWishlist(wishlistItemId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || null;
        let url = `{{ url('user/wishlist') }}/${wishlistItemId}`;

        fetch(url, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                Swal.fire({ ...getSwalTheme(), title: 'Berhasil!', text: data.message, icon: 'success', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                location.reload(); 
            } else {
                Swal.fire({ ...getSwalTheme(), title: 'Oops...', text: data.error || 'Gagal menghapus dari wishlist.', icon: 'error', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            }
        });
    }
    @endif
    

    // *******************************************************************
    // POS CART FUNCTIONALITY
    // *******************************************************************
    
    let posCart = JSON.parse(localStorage.getItem('posCart')) || [];
    
    function updatePOSCartDisplay() {

        const cartContainer = document.getElementById('pos-cart-items');
        
        if (!cartContainer) {
            return;
        }
        
        // Calculate total
        const total = posCart.reduce((sum, item) => sum + (item.harga * item.quantity), 0);
        
        // Update mobile cart count
        const mobileCartCount = document.getElementById('mobile-cart-count');
        if (mobileCartCount) {
            mobileCartCount.textContent = posCart.length;
        }
        
        // Ensure mobile cart button remains clickable
        const mobileCartButton = document.querySelector('button[onclick="toggleMobileCart()"]');
        if (mobileCartButton) {
            mobileCartButton.style.pointerEvents = 'auto';
            mobileCartButton.style.zIndex = '20';
        }
        
        // Update cart summary displays
        const cartItemsCountEl = document.getElementById('cart-items-count');
        const cartTotalDisplayEl = document.getElementById('cart-total-display');
        const cartActionsEl = document.getElementById('cart-actions');
        const emptyCartMessageEl = document.getElementById('empty-cart-message');
        

        
        if (cartItemsCountEl) {
            cartItemsCountEl.textContent = posCart.length + ' item(s)';
        }
        if (cartTotalDisplayEl) {
            cartTotalDisplayEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }
        
        // Show/hide elements based on cart state
        if (posCart.length === 0) {
            if (cartActionsEl) cartActionsEl.style.display = 'none';
            if (emptyCartMessageEl) emptyCartMessageEl.style.display = 'block';
            
            cartContainer.innerHTML = `
                <div class="text-center py-4 text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <p class="text-xs">Keranjang kosong</p>
                    <p class="text-xs mt-1 opacity-75">Klik menu</p>
                </div>
            `;
        } else {
            if (cartActionsEl) cartActionsEl.style.display = 'block';
            if (emptyCartMessageEl) emptyCartMessageEl.style.display = 'none';
            
            cartContainer.innerHTML = posCart.map(item => `
                <div class="bg-gray-700 rounded-lg p-2 mb-2 mx-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2">
                        <img src="${item.foto}" alt="${item.nama}" class="w-8 h-8 object-cover rounded flex-shrink-0">
                        <div class="flex-1 min-w-0 overflow-hidden">
                            <h4 class="text-xs font-medium text-white truncate">${item.nama}</h4>
                            <p class="text-xs text-gray-300">Rp ${item.harga.toLocaleString('id-ID')}</p>
                        </div>
                        <button onclick="removePOSItem(${item.id})" class="text-red-400 hover:text-red-300 hover:bg-red-900/30 w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 transition-colors">
                            ✕
                        </button>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1">
                            <button onclick="updatePOSItemQuantity(${item.id}, -1)" 
                                class="w-6 h-6 flex items-center justify-center bg-gray-600 hover:bg-gray-500 rounded text-white transition-colors text-sm font-bold">
                                −
                            </button>
                            <span class="w-8 text-center text-xs font-bold text-white">${item.quantity}</span>
                            <button onclick="updatePOSItemQuantity(${item.id}, 1)" 
                                class="w-6 h-6 flex items-center justify-center bg-gray-600 hover:bg-gray-500 rounded text-white transition-colors text-sm font-bold">
                                +
                            </button>
                        </div>
                        <div class="text-right ml-2">
                            <p class="text-xs font-bold text-orange-400">Rp ${(item.harga * item.quantity).toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        // Save to localStorage
        localStorage.setItem('posCart', JSON.stringify(posCart));
    }
    
    function addToPOSCart(produkId, nama, harga, foto, stok) {
        const existingItem = posCart.find(item => item.id === produkId);
        
        if (existingItem) {
            if (existingItem.quantity < stok) {
                existingItem.quantity += 1;
                
                // Sync quantity update to server immediately
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || null;
                fetch(`{{ url('user/cart/add') }}/${produkId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ quantity: existingItem.quantity })
                }).then(() => {
                    Swal.fire({ 
                        ...getSwalTheme(), 
                        title: 'Ditambahkan!', 
                        text: `${nama} ditambahkan ke keranjang`, 
                        icon: 'success', 
                        toast: true, 
                        position: 'top-end', 
                        showConfirmButton: false, 
                        timer: 2000 
                    });
                }).catch(error => {
                    console.error('Failed to sync quantity to server:', error);
                    // Revert quantity on failure
                    existingItem.quantity -= 1;
                    updatePOSCartDisplay();
                    Swal.fire({ 
                        ...getSwalTheme(), 
                        title: 'Error!', 
                        text: 'Gagal menambahkan ke keranjang server', 
                        icon: 'error', 
                        toast: true, 
                        position: 'top-end', 
                        showConfirmButton: false, 
                        timer: 3000 
                    });
                });
            } else {
                Swal.fire({ 
                    ...getSwalTheme(), 
                    title: 'Stok Tidak Mencukupi!', 
                    text: `Stok ${nama} Tidak Mencukupi`, 
                    icon: 'warning', 
                    toast: true, 
                    position: 'top-end', 
                    showConfirmButton: false, 
                    timer: 3000 
                });
                return;
            }
        } else {
            posCart.push({
                id: produkId,
                nama: nama,
                harga: harga,
                foto: foto,
                quantity: 1,
                stok: stok
            });
            
            // Sync new item to server immediately
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || null;
            fetch(`{{ url('user/cart/add') }}/${produkId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ quantity: 1 })
            }).then(() => {
                Swal.fire({ 
                    ...getSwalTheme(), 
                    title: 'Ditambahkan!', 
                    text: `${nama} ditambahkan ke keranjang`, 
                    icon: 'success', 
                    toast: true, 
                    position: 'top-end', 
                    showConfirmButton: false, 
                    timer: 2000 
                });
            }).catch(error => {
                console.error('Failed to sync add to server:', error);
                Swal.fire({ 
                    ...getSwalTheme(), 
                    title: 'Peringatan!', 
                    text: `${nama} ditambahkan (sinkronisasi server gagal)`, 
                    icon: 'warning', 
                    toast: true, 
                    position: 'top-end', 
                    showConfirmButton: false, 
                    timer: 3000 
                });
            });
        }
        
        updatePOSCartDisplay();
    }
    
    function updatePOSItemQuantity(produkId, change) {
        const item = posCart.find(item => item.id === produkId);
        if (!item) return;
        
        const newQuantity = item.quantity + change;
        
        if (newQuantity <= 0) {
            removePOSItem(produkId);
        } else if (newQuantity <= item.stok) {
            const oldQuantity = item.quantity;
            item.quantity = newQuantity;
            updatePOSCartDisplay();
            
            // Sync quantity change to server
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || null;
            fetch(`{{ url('user/cart/add') }}/${produkId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ quantity: newQuantity })
            }).catch(error => {
                console.error('Failed to sync quantity to server:', error);
                // Revert on failure
                item.quantity = oldQuantity;
                updatePOSCartDisplay();
            });
        } else {
            Swal.fire({ 
                ...getSwalTheme(), 
                title: 'Stok Terbatas!', 
                text: `Stok ${item.nama} hanya ${item.stok} unit`, 
                icon: 'warning', 
                toast: true, 
                position: 'top-end', 
                showConfirmButton: false, 
                timer: 3000 
            });
        }
    }
    
    function removePOSItem(produkId) {
        const itemIndex = posCart.findIndex(item => item.id === produkId);
        if (itemIndex > -1) {
            const itemName = posCart[itemIndex].nama;
            
            // Remove from POS cart immediately for responsive UI
            posCart.splice(itemIndex, 1);
            updatePOSCartDisplay();
            
            // Sync removal to server cart
            syncCartItemRemoval(produkId).then(() => {
                Swal.fire({ 
                    ...getSwalTheme(), 
                    title: 'Dihapus!', 
                    text: `${itemName} dihapus dari keranjang`, 
                    icon: 'info', 
                    toast: true, 
                    position: 'top-end', 
                    showConfirmButton: false, 
                    timer: 2000 
                });
            }).catch(error => {
                console.error('Failed to sync removal to server:', error);
                // Optional: Show warning that server sync failed
                Swal.fire({ 
                    ...getSwalTheme(), 
                    title: 'Peringatan', 
                    text: `${itemName} dihapus dari keranjang (sinkronisasi server gagal)`, 
                    icon: 'warning', 
                    toast: true, 
                    position: 'top-end', 
                    showConfirmButton: false, 
                    timer: 3000 
                });
            });
        }
    }
    
    function clearPOSCart() {
        Swal.fire({
            ...getSwalTheme(),
            title: 'Kosongkan Keranjang?',
            text: 'Semua item akan dihapus dari keranjang',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kosongkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#EF4444'
        }).then((result) => {
            if (result.isConfirmed) {
                // Clear POS cart immediately for responsive UI
                posCart = [];
                updatePOSCartDisplay();
                
                // Sync clear to server cart
                syncCartClear().then(() => {
                    Swal.fire({ 
                        ...getSwalTheme(), 
                        title: 'Dikosongkan!', 
                        text: 'Keranjang telah dikosongkan', 
                        icon: 'success', 
                        toast: true, 
                        position: 'top-end', 
                        showConfirmButton: false, 
                        timer: 2000 
                    });
                }).catch(error => {
                    console.error('Failed to sync clear to server:', error);
                    Swal.fire({ 
                        ...getSwalTheme(), 
                        title: 'Peringatan', 
                        text: 'Keranjang dikosongkan (sinkronisasi server gagal)', 
                        icon: 'warning', 
                        toast: true, 
                        position: 'top-end', 
                        showConfirmButton: false, 
                        timer: 3000 
                    });
                });
            }
        });
    }
    
    function autoSyncToServer() {
        return new Promise((resolve, reject) => {
            if (posCart.length === 0) {
                reject('Keranjang kosong');
                return;
            }
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || null;
            
            // Sync items satu per satu ke server cart
            const syncPromises = posCart.map(item => {
                return fetch(`{{ url('user/cart/add') }}/${item.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ quantity: item.quantity })
                });
            });
            
            Promise.all(syncPromises)
                .then(() => {
                    // Don't clear POS cart immediately - let checkout process handle it
                    // This allows cancel order to still have items in cart
                    resolve();
                })
                .catch(error => {
                    reject(error);
                });
        });
    }
    
    function proceedToPayment() {
        if (posCart.length === 0) {
            Swal.fire({ 
                ...getSwalTheme(), 
                title: 'Keranjang Kosong!', 
                text: 'Pilih produk terlebih dahulu', 
                icon: 'warning', 
                toast: true, 
                position: 'top-end', 
                showConfirmButton: false, 
                timer: 2000 
            });
            return;
        }
        
        // Show payment method selection
        const total = posCart.reduce((sum, item) => sum + (item.harga * item.quantity), 0);
        const itemsCount = posCart.length;
        const itemsList = posCart.map(item => `• ${item.nama} (${item.quantity}x)`).join('<br>');
        
        Swal.fire({
            ...getSwalTheme(),
            title: 'Konfirmasi Pembayaran',
            html: `
                <div class="text-left mb-4">
                    <h4 class="font-bold mb-2">Ringkasan Pesanan:</h4>
                    <div class="bg-gray-100 dark:bg-gray-800 p-3 rounded mb-3">
                        <div class="text-sm">
                            ${itemsList}
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between font-bold">
                            <span>Total (${itemsCount} items):</span>
                            <span class="text-orange-600">Rp ${total.toLocaleString('id-ID')}</span>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Masuk Ke Metode Pembayaran</p>
                </div>
                <div class="grid gap-3">
                    <button onclick="selectPaymentMethod('xendit')" class="payment-method-btn bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 hover:bg-blue-100 dark:hover:bg-blue-800 p-4 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <span class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path>
                                </svg>
                            </span>
                            <div class="text-left">
                                <div class="font-semibold text-blue-800 dark:text-blue-200">Bayar Sekarang</div>
                                <div class="text-sm text-blue-600 dark:text-blue-400">Bayar Langsung</div>
                            </div>
                        </div>
                    </button>
                </div>
            `,
            showConfirmButton: false,
            showCancelButton: true,
            cancelButtonText: 'Kembali',
            width: '500px',
            customClass: {
                htmlContainer: 'text-left p-0'
            }
        });
    }
    
    function selectPaymentMethod(method) {
        Swal.close();
        
        if (method === 'xendit') {
            // Handle xendit payment - auto sync then redirect
            Swal.fire({
                ...getSwalTheme(),
                title: 'Menyiapkan Pembayaran...',
                html: 'Mohon tunggu, sedang memproses pesanan...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Since cart is now synced in real-time, just proceed to checkout
            setTimeout(() => {
                if (posCart.length === 0) {
                    Swal.fire({ 
                        ...getSwalTheme(), 
                        title: 'Keranjang Kosong!', 
                        text: 'Pilih produk terlebih dahulu', 
                        icon: 'warning' 
                    });
                    return;
                }
                
                // Clear POS cart and redirect to checkout (server cart already synced)
                posCart = [];
                updatePOSCartDisplay();
                window.location.href = '{{ route("user.checkout.form") }}';
            }, 1000);
        }
    }

    // FITUR ADD TO CART - MODIFIED FOR POS
    function addToCart(produkId) {
        // Get product data from the product card using data attributes
        const productCard = document.querySelector(`[data-product-id="${produkId}"]`);
        
        if (productCard) {
            const nama = productCard.dataset.productName || 'Produk';
            const harga = parseInt(productCard.dataset.productPrice) || 0;
            const foto = productCard.dataset.productImage || '';
            const stok = parseInt(productCard.dataset.productStock) || 999;
            
            addToPOSCart(produkId, nama, harga, foto, stok);
        } else {
            // Fallback to original server-side add to cart
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || null;
            let url = `{{ url('user/cart/add') }}/${produkId}`;

            fetch(url, {
                method: 'POST', 
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.error || 'Stok tidak mencukupi atau produk tidak ada'); });
                }
                return response.json(); 
            })
            .then(data => {
                if (data.message) {
                    Swal.fire({ ...getSwalTheme(), title: 'Berhasil!', text: data.message, icon: 'success', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                }
            })
            .catch(error => {
                Swal.fire({ ...getSwalTheme(), title: 'Oops!', text: error.message || 'Terjadi kesalahan. Silakan coba lagi.', icon: 'error', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            });
        }
    }

    // *******************************************************************
    // CART SYNC FUNCTIONS
    // *******************************************************************
    
    function syncCartItemRemoval(produkId) {
        return new Promise((resolve, reject) => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || null;
            
            fetch(`{{ url('user/cart/remove-product') }}/${produkId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    resolve();
                } else {
                    reject('Failed to remove item from server cart');
                }
            })
            .catch(error => {
                reject(error);
            });
        });
    }
    
    function syncCartClear() {
        return new Promise((resolve, reject) => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || null;
            
            fetch('{{ route("user.cart.clear") }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    resolve();
                } else {
                    reject('Failed to clear server cart');
                }
            })
            .catch(error => {
                reject(error);
            });
        });
    }
    
    function reloadCartFromServer() {
        return new Promise((resolve, reject) => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || null;
            
            fetch('{{ route("user.cart.api") }}', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.items && Array.isArray(data.items)) {
                    // Convert server cart items to POS cart format
                    posCart = data.items.map(item => ({
                        id: item.produk.id,
                        nama: item.produk.nama,
                        harga: item.produk.harga,
                        foto: item.produk.foto ? `{{ asset('storage/') }}/${item.produk.foto}` : '',
                        quantity: item.jumlah,
                        stok: item.produk.stok || 999
                    }));
                    updatePOSCartDisplay();
                    resolve(posCart.length);
                } else {
                    posCart = [];
                    updatePOSCartDisplay();
                    resolve(0);
                }
            })
            .catch(error => {
                console.error('Error reloading cart:', error);
                reject(error);
            });
        });
    }

    // *******************************************************************
    // MOBILE CART FUNCTIONS
    // *******************************************************************
    
    function toggleMobileCart() {
        const cartSidebar = document.getElementById('mobile-cart-sidebar');
        if (cartSidebar) {
            // Get Alpine.js component
            const alpineComponent = Alpine.$data(cartSidebar.closest('[x-data]'));
            if (alpineComponent) {
                alpineComponent.showCart = !alpineComponent.showCart;
            }
        }
    }
    
    function closeMobileCart() {
        const cartSidebar = document.getElementById('mobile-cart-sidebar');
        if (cartSidebar && window.innerWidth < 1024) { // Only close on mobile
            const alpineComponent = Alpine.$data(cartSidebar.closest('[x-data]'));
            if (alpineComponent) {
                alpineComponent.showCart = false;
            }
        }
    }

    // *******************************************************************
    // MAIN DOM CONTENT LOADED
    // *******************************************************************

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded successfully');
        
        // Check if products are loaded
        const productCards = document.querySelectorAll('.js-product-card');
        console.log('Found product cards:', productCards.length);
        
        // Initialize POS Cart Display and reload from server if needed
        setTimeout(() => {
            // Check URL params to see if user returned from checkout
            const urlParams = new URLSearchParams(window.location.search);
            const fromCheckout = urlParams.get('from') === 'checkout_cancel';
            
            // Check if we have POS cart or need to reload from server
            if (posCart.length === 0) {
                reloadCartFromServer().then(itemCount => {
                    if (itemCount > 0) {
                        console.log(`Reloaded ${itemCount} items from server cart`);
                        const message = fromCheckout 
                            ? `Checkout dibatalkan. ${itemCount} item dikembalikan ke keranjang`
                            : `${itemCount} item berhasil dimuat dari pesanan sebelumnya`;
                        
                        Swal.fire({ 
                            ...getSwalTheme(), 
                            title: 'Keranjang Dimuat!', 
                            text: message, 
                            icon: fromCheckout ? 'success' : 'info', 
                            toast: true, 
                            position: 'top-end', 
                            showConfirmButton: false, 
                            timer: 4000 
                        });
                    } else if (fromCheckout) {
                        Swal.fire({ 
                            ...getSwalTheme(), 
                            title: 'Checkout Dibatalkan', 
                            text: 'Keranjang kosong, silakan pilih menu', 
                            icon: 'info', 
                            toast: true, 
                            position: 'top-end', 
                            showConfirmButton: false, 
                            timer: 3000 
                        });
                    }
                    
                    // Clean up URL
                    if (fromCheckout) {
                        window.history.replaceState({}, '', '{{ route("user.dashboard") }}');
                    }
                }).catch(error => {
                    console.error('Failed to reload cart:', error);
                    if (fromCheckout) {
                        Swal.fire({ 
                            ...getSwalTheme(), 
                            title: 'Checkout Dibatalkan', 
                            text: 'Gagal memuat keranjang, silakan refresh halaman', 
                            icon: 'warning', 
                            toast: true, 
                            position: 'top-end', 
                            showConfirmButton: false, 
                            timer: 3000 
                        });
                        window.history.replaceState({}, '', '{{ route("user.dashboard") }}');
                    }
                });
            } else {
                updatePOSCartDisplay();
                if (fromCheckout) {
                    Swal.fire({ 
                        ...getSwalTheme(), 
                        title: 'Checkout Dibatalkan', 
                        text: 'Keranjang tetap tersimpan', 
                        icon: 'success', 
                        toast: true, 
                        position: 'top-end', 
                        showConfirmButton: false, 
                        timer: 3000 
                    });
                    window.history.replaceState({}, '', '{{ route("user.dashboard") }}');
                }
            }
        }, 100);
        
        @if(config('fitur.chat'))
        // --- CHAT NOTIFICATION LOGIC - DAPAT DIKONFIGURASI ---
        const chatBadge = document.getElementById('chat-badge');
        if(chatBadge) {
            let lastKnownCount = parseInt(localStorage.getItem('last_unread_chat_count')) || 0;

            function checkUnreadChats() {
                fetch("{{ route('user.chat.unread.count') }}")
                    .then(response => response.json())
                    .then(data => {
                        const count = data.count;
                        if (count > 0) {
                            chatBadge.textContent = count > 99 ? '99+' : count;
                            chatBadge.classList.remove('hidden');
                            if (count > lastKnownCount) {
                                Swal.fire({ ...getSwalTheme(), title: 'Pesan Baru!', text: `Anda memiliki ${count} pesan yang belum dibaca dari Admin.`, icon: 'info', toast: true, position: 'top-end', showConfirmButton: false, timer: 5000, timerProgressBar: true });
                            }
                        } else {
                            chatBadge.classList.add('hidden');
                        }
                        lastKnownCount = count;
                        localStorage.setItem('last_unread_chat_count', count);
                    });
            }
            checkUnreadChats();
            setInterval(checkUnreadChats, 5000); 
        }
        @endif

        @if(config('fitur.favorit'))
        // FITUR FAVORIT - DAPAT DIKONFIGURASI
    
        document.querySelectorAll('.btn-favorite').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); 
                const btn = this, route = btn.dataset.route, countSpan = btn.querySelector('.like-count');
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || null;

                fetch(route, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' } })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        btn.classList.toggle('active', data.is_favorited);
                        if (countSpan) { countSpan.textContent = data.total_likes; }
                    } else {
                        Swal.fire({ ...getSwalTheme(), title: 'Oops...', text: 'Gagal memfavoritkan.', icon: 'error' });
                    }
                });
            });
        });
        @endif
    

        // FITUR LIVE SEARCH AUTO-SUGGESTION - TIDAK ADA DI PERSYARATAN
    
        const searchInput = document.getElementById('search-input');
        const searchResultsContainer = document.getElementById('search-results');
        let searchTimeout;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length === 0) {
                searchResultsContainer.classList.add('hidden');
                searchResultsContainer.innerHTML = '';
                return;
            }

            searchTimeout = setTimeout(() => {
                fetchLiveSearch(query);
            }, 200);
        });
        
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResultsContainer.contains(e.target)) {
                searchResultsContainer.classList.add('hidden');
            }
        });
        
        // Ensure mobile cart functionality works on touch devices
        document.addEventListener('touchstart', function() {}, { passive: true });
        
        // Alternative mobile cart toggle for better compatibility
        document.addEventListener('click', function(e) {
            if (e.target.closest('button[onclick="toggleMobileCart()"]')) {
                e.preventDefault();
                toggleMobileCart();
            }
        });

        function fetchLiveSearch(query) {
            const searchUrl = `{{ route('user.search.products') }}?query=${encodeURIComponent(query)}`;

            searchResultsContainer.innerHTML = `
                <div class="p-3 text-center text-sm text-gray-500 dark:text-gray-400">
                    <svg class="animate-spin h-5 w-5 text-pink-600 inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Mencari...
                </div>`;
            searchResultsContainer.classList.remove('hidden');


            fetch(searchUrl, { method: 'GET', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal memuat hasil pencarian dari server.');
                }
                return response.json();
            })
            .then(data => {
                displayResults(data);
            })
            .catch(error => {
                console.error('Error fetching search results:', error);
                searchResultsContainer.innerHTML = `
                    <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                        Gagal memuat hasil pencarian dari server.
                    </div>`;
                searchResultsContainer.classList.remove('hidden');
            });
        }

        function highlightMatch(text, query) {
            if (!query) return text;
            const regex = new RegExp(`(${query})`, 'gi');
            return text.replace(regex, '<span class="bg-yellow-200 dark:bg-yellow-900">$1</span>');
        }

        function displayResults(products) {
            searchResultsContainer.innerHTML = '';

            if (!products || products.length === 0) {
                searchResultsContainer.innerHTML = `
                    <div class="flex items-center px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                        Tidak menemukan produk
                    </div>`;
                searchResultsContainer.classList.remove('hidden');
                return;
            }

            const list = document.createElement('div');
            list.className = 'py-1';

            products.forEach(product => {
                const item = document.createElement('div');
                item.className = 'flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer';
                
                item.innerHTML = `
                    <svg class="w-4 h-4 mr-3 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <div class="flex-1 text-gray-900 dark:text-white">
                        ${highlightMatch(product.nama, searchInput.value)}
                    </div>
                `;

                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    searchInput.value = product.nama;
                    searchResultsContainer.classList.add('hidden');
                    document.getElementById('filter-form').submit();
                });

                list.appendChild(item);
            });

            searchResultsContainer.appendChild(list);
            searchResultsContainer.classList.remove('hidden');
        }
    
    });
</script>
@endsection