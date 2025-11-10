@extends('layouts.user') {{-- Pastikan layout ini ada dan memuat SweetAlert2 & Tailwind CSS --}}

{{-- Tambahkan CSRF Token di head layout jika belum ada, atau di sini jika perlu --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<div class="py-8">
    <div
        class="bg-gray-100 dark:bg-gray-900 shadow-2xl p-6 md:p-10 max-w-7xl mx-auto rounded-xl border border-gray-200 dark:border-gray-700">
        <div class="header-section mb-8 border-b border-gray-200 dark:border-gray-700 pb-4">
            <h1 class="text-2xl md:text-3xl font-extrabold mb-2 
                             text-transparent bg-clip-text bg-gradient-to-r 
                             from-pink-600 to-purple-600 
                             dark:from-pink-400 dark:to-purple-400">
                👋 {{ __('app.welcome_user', ['name' => auth()->user()->name]) }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">{{ __('app.view_products') }}</p>
        </div>

        {{-- NAVIGATION TABS --}}
        <div class="grid grid-cols-3 gap-3 mb-8">
            <a href="{{ route('user.cart') }}"
                class="flex items-center justify-center space-x-2 bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700 transition duration-200 shadow-md">
                <span>🛒</span>
                <span class="hidden sm:inline">{{ __('app.cart') }}</span>
            </a>
            {{-- FITUR WISHLIST - TIDAK ADA DI PERSYARATAN --}}
            <a href="{{ route('user.wishlist.index') }}" class="flex items-center justify-center space-x-2 bg-transparent border border-gray-300 dark:border-gray-600 
                                     text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg 
                                     hover:bg-gray-100 dark:hover:bg-gray-800 transition duration-200">
                <span>⭐</span>
                <span class="hidden sm:inline">{{ __('app.wishlist') }}</span>
            </a>
            <a href="{{ route('user.transactions') }}" class="flex items-center justify-center space-x-2 bg-transparent border border-gray-300 dark:border-gray-600 
                                     text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg 
                                     hover:bg-gray-100 dark:hover:bg-gray-800 transition duration-200">
                <span>📦</span>
                <span class="hidden sm:inline">{{ __('app.transactions') }}</span>
            </a>
            <a href="{{ route('user.chat') }}" id="chat-link" class="relative flex items-center justify-center space-x-2 bg-transparent border border-gray-300 dark:border-gray-600 
                                     text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg 
                                     hover:bg-gray-100 dark:hover:bg-gray-800 transition duration-200">
                <span>💬</span>
                <span class="hidden sm:inline">{{ __('app.chat') }}</span>
                <span id="chat-badge"
                    class="absolute top-0 right-0 -mt-2 -mr-2 px-2 py-0.5 text-xs font-bold leading-none text-red-100 transform bg-red-600 rounded-full hidden">0</span>
            </a>
        </div>

        {{-- FORM FILTER UTAMA --}}
        <form method="GET" action="{{ route('user.dashboard') }}" id="filter-form">

            <div
                class="mb-8 flex flex-col md:flex-row gap-4 items-center bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow-sm">

                {{-- WRAPPER UNTUK LIVE SEARCH --}}
                {{-- FITUR LIVE SEARCH AUTO-SUGGESTION - TIDAK ADA DI PERSYARATAN --}}
                <div class="relative w-full md:w-5/12">
                    <input type="text" name="search" id="search-input" placeholder="{{ __('app.search_placeholder') }}"
                        value="{{ request('search') }}"
                        class="border border-gray-300 dark:border-gray-700 p-3 rounded-lg w-full bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500 focus:border-pink-500">

                    <div id="search-results"
                        class="absolute w-full z-30 mt-1 rounded-lg shadow-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 max-h-60 overflow-y-auto hidden">
                    </div>
                </div>

                {{-- PENCARIAN SEDERHANA SESUAI PERSYARATAN --}}
                <div class="relative w-full md:w-5/12">
                    <input type="text" name="search" placeholder="{{ __('app.search_placeholder') }}"
                        value="{{ request('search') }}"
                        class="border border-gray-300 dark:border-gray-700 p-3 rounded-lg w-full bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                </div>

                <select name="kategori"
                    class="border border-gray-300 dark:border-gray-700 p-3 rounded-lg w-full md:w-3/12 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500">
                    <option value="">{{ __('app.all_categories') }}</option>
                    @foreach ($kategori as $kat)
                    <option value="{{ $kat->id }}" {{ request('kategori')==$kat->id ? 'selected' : '' }}>
                        {{ $kat->nama }}
                    </option>
                    @endforeach
                </select>

                <select name="perpage"
                    class="border border-gray-300 dark:border-gray-700 p-3 rounded-lg w-full md:w-2/12 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500">
                    @php
                    $perPageOptions = [5, 10, 20];
                    $currentPerPage = request('perpage', 12); // Default 12 dari Controller
                    @endphp
                    <option disabled>{{ __('app.products_per_page') }}</option>
                    @foreach ($perPageOptions as $option)
                    <option value="{{ $option }}" {{ $currentPerPage==$option ? 'selected' : '' }}>
                        {{ $option }} {{ __('app.products') }}
                    </option>
                    @endforeach
                </select>

                <button type="submit"
                    class="bg-pink-600 text-white px-5 py-3 rounded-lg hover:bg-pink-700 w-full md:w-2/12 transition duration-200 font-semibold">
                    {{ __('app.filter') }}
                </button>
            </div>

            {{-- LAYOUT 2 KOLOM --}}
            <div class="flex flex-col md:flex-row gap-8" x-data="{ showFilters: false }">

                {{-- KOLOM 1: SIDEBAR FILTER --}}
                <aside class="fixed inset-0 z-40 bg-white bg-opacity-95 dark:bg-gray-900 dark:bg-opacity-90 p-6 overflow-y-auto transform -translate-x-full transition-transform duration-300 ease-in-out
                                     md:relative md:inset-auto md:z-auto md:p-0 md:overflow-y-visible md:bg-transparent md:bg-opacity-100
                                     md:block w-full md:w-1/4 lg:w-1/5 md:translate-x-0"
                    :class="{'translate-x-0': showFilters, '-translate-x-full': !showFilters}"
                    @click.away="showFilters = false" x-cloak>

                    <button type="button" @click="showFilters = false"
                        class="md:hidden text-gray-800 dark:text-white mb-4 absolute top-4 right-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>

                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg md:sticky md:top-24"> 
                        {{-- FITUR SORTING - TIDAK ADA DI PERSYARATAN --}}
                        <h3
                            class="text-xl font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                            {{ __('app.sort_by') }}</h3>
                        <div class="space-y-4">
                            <label for="sort_by" class="block text-sm font-medium text-gray-600 dark:text-gray-400">{{
                                __('app.sort_by') }}</label>

                            <select name="sort_by" id="sort_by"
                                class="border border-gray-300 dark:border-gray-700 p-3 rounded-lg w-full bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500"
                                onchange="this.form.submit()">

                                @php
                                $currentSort = request('sort_by', 'created_at_desc');
                                @endphp

                                @foreach ($sortOptions as $value => $label)
                                <option value="{{ $value }}" {{ $currentSort==$value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <h3
                            class="text-xl font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                            {{ __('app.filter') }}</h3>

                        {{-- FITUR FILTER RATING - TIDAK ADA DI PERSYARATAN --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Filter Rating</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="radio" id="rating_all" name="rating_filter" value="" {{ !request('rating_filter') ? 'checked' : '' }}
                                        onchange="this.form.submit()" class="h-4 w-4 text-pink-600 border-gray-300">
                                    <label for="rating_all" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Semua Rating</label>
                                </div>
                                @for($i = 5; $i >= 4; $i--)
                                <div class="flex items-center">
                                    <input type="radio" id="rating_{{ $i }}" name="rating_filter" value="{{ $i }}"
                                        {{ request('rating_filter') == $i ? 'checked' : '' }} onchange="this.form.submit()"
                                        class="h-4 w-4 text-pink-600 border-gray-300">
                                    <label for="rating_{{ $i }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $i }}+ <span class="text-yellow-400">★</span>
                                    </label>
                                </div>
                                @endfor
                            </div>
                        </div>

                        {{-- FITUR FILTER FAVORIT - TIDAK ADA DI PERSYARATAN --}}
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('app.show')
                                }}</label>

                            <div class="flex items-center">
                                <input id="filter_semua" name="filter_favorit" type="radio" value="" {{
                                    !request('filter_favorit') ? 'checked' : '' }} onchange="this.form.submit()"
                                    class="h-5 w-5 text-pink-600 border-gray-300 dark:border-gray-600 focus:ring-pink-500 bg-white dark:bg-gray-900">
                                <label for="filter_semua"
                                    class="ml-3 block text-sm font-medium text-gray-900 dark:text-white cursor-pointer">
                                    {{ __('app.all_products') }}
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input id="filter_favorit" name="filter_favorit" type="radio" value="true" {{
                                    request('filter_favorit')=='true' ? 'checked' : '' }} onchange="this.form.submit()"
                                    class="h-5 w-5 text-pink-600 border-gray-300 dark:border-gray-600 focus:ring-pink-500 bg-white dark:bg-gray-900">
                                <label for="filter_favorit"
                                    class="ml-3 block text-sm font-medium text-gray-900 dark:text-white cursor-pointer">
                                    {{ __('app.my_favorites') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </aside>
                {{-- KOLOM 2: DAFTAR PRODUK --}}
                <main class="w-full md:flex-1">

                    {{-- PRODUCT GRID --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @forelse ($produk as $item)

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden 
                                    transition-all transform duration-300 ease-in-out 
                                    hover:scale-[1.03] hover:shadow-xl 
                                    border border-gray-200 dark:border-gray-700 flex flex-col js-product-card"
                        data-name="{{ strtolower($item->nama) }}"
                        data-kategori="{{ strtolower($item->kategori->nama) }}"
                        data-desc="{{ strtolower(strip_tags($item->deskripsi ?? '')) }}">

                            {{-- BLOK 1: GAMBAR --}}
                            <div class="relative">
                                <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama }}"
                                    class="w-full h-40 sm:h-48 object-cover">

                                {{-- FITUR FAVORIT/LIKE - TIDAK ADA DI PERSYARATAN --}}
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
                                        ❤️
                                    </span>

                                    <span class="like-count text-sm font-bold 
                                                     transition-all duration-200 ease-in-out 
                                                     text-gray-600 dark:text-gray-300 
                                                     group-[.active]:text-red-700 dark:group-[.active]:text-pink-300 
                                                     group-[.active]:font-extrabold">
                                        {{ $item->favorited_by_count }}
                                    </span>
                                </button>

                                @php
                                $wishlistItemId = $wishlistItems->get($item->id);
                                @endphp

                                {{-- FITUR WISHLIST - TIDAK ADA DI PERSYARATAN --}}
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
                            </div>

                            {{-- BLOK 2: KONTEN TEKS --}}
                            <div class="p-5 flex flex-col flex-grow">
                                <span class="text-xs font-semibold text-pink-600 uppercase mb-1">{{
                                    $item->kategori->nama }}</span>
                                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-1 flex-grow">{{
                                    $item->nama }}</h2>

                                {{-- FITUR RATING, ULASAN, DAN PENJUALAN - TIDAK ADA DI PERSYARATAN --}}
                                <div class="space-y-2 mb-3">
                                    <div class="flex items-center space-x-2">
                                        @php
                                        $rating = round($item->average_rating ?? 0, 1);
                                        $reviewsCount = $item->reviews_count ?? 0;
                                        $maxStars = 5;
                                        @endphp
                                        <div class="flex text-yellow-400">
                                            @for ($i = 1; $i <= $maxStars; $i++) @if ($rating>= $i)
                                            <span>★</span>
                                            @elseif ($rating > ($i - 1))
                                            <span class="half-star">★</span>
                                            @else
                                            <span class="text-gray-300 dark:text-gray-600">★</span>
                                            @endif
                                            @endfor
                                        </div>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $rating
                                            }}</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">({{ $reviewsCount }} {{
                                            __('app.reviews') }})</span>
                                    </div>
                                    
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        <span>Terjual {{ $item->transaction_items_sum_jumlah ?? 0 }} unit</span>
                                    </div>
                                </div>
                                
                                {{-- FITUR TOMBOL LIHAT DETAIL/ULASAN - TIDAK ADA DI PERSYARATAN --}}
                                <button type="button"
                                    class="text-sm text-pink-600 hover:text-pink-800 dark:hover:text-pink-400 font-medium mb-3 self-start hover:underline"
                                    onclick='showProductDetails({{ $item->id }}, {{ json_encode($item->nama) }}, {{ json_encode($item->deskripsi) }})'>
                                    {{ __('app.details_reviews') }}
                                </button>
                                

                                <div class="flex justify-between items-center mb-4">
                                    <p class="text-xl text-gray-900 dark:text-white font-extrabold">Rp {{
                                        number_format($item->harga, 0,
                                        ',', '.') }}</p>
                                    <span
                                        class="text-sm font-medium {{ $item->stok > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        Stok: {{ $item->stok }}
                                    </span>
                                </div>

                                {{-- Tombol Keranjang --}}
                                <div class="mt-auto">
                                    @if ($item->stok > 0)
                                    <button type="button" onclick="addToCart({{ $item->id }})" class="bg-gray-800 text-white 
                                                         hover:bg-pink-700 
                                                         dark:bg-gray-600 dark:hover:bg-pink-600 
                                                         px-4 py-2 rounded-lg w-full transition duration-200 font-semibold">
                                        {{ __('app.add_to_cart') }}
                                    </button>
                                    @else
                                    <button type="button" disabled
                                        class="bg-gray-300 text-gray-500 dark:bg-gray-700 dark:text-gray-400 px-4 py-2 rounded-lg w-full cursor-not-allowed font-semibold">
                                        {{ __('app.out_of_stock') }}
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @empty
                        <p
                            class="col-span-1 sm:col-span-2 lg:col-span-3 xl:col-span-4 text-pink-600 dark:text-pink-400 text-center text-xl p-10">
                            {{ __('app.no_products_found') }}
                        </p>
                        @endforelse
                    </div>

                    {{-- PAGINATION LINKS --}}
                    <div class="mt-10 flex justify-center">
                        {{ $produk->links() }}
                    </div>
                </main>

            </div> {{-- End of 2-column layout wrapper --}}

        </form> {{-- AKHIR DARI FORM FILTER UTAMA --}}

    </div>
</div>

<style>
    /* ... CSS Paginasi (Sama seperti kode Anda) ... */
    .dark .pagination span[aria-current="page"] span,
    .dark .pagination a[rel="next"],
    .dark .pagination a[rel="prev"],
    .dark .pagination a[aria-label^="Go to page"] {
        background-color: #374151;
        /* bg-gray-700 */
        color: #f3f4f6;
        /* text-gray-100 */
        border-color: #4b5563;
        /* border-gray-600 */
    }

    .dark .pagination span[aria-disabled="true"] span {
        background-color: #4b5563;
        /* bg-gray-600 */
        color: #9ca3af;
        /* text-gray-400 */
    }

    .dark .pagination a[rel="next"]:hover,
    .dark .pagination a[rel="prev"]:hover,
    .dark .pagination a[aria-label^="Go to page"]:hover {
        background-color: #4b5563;
        /* bg-gray-600 */
    }

    .dark .pagination span[aria-current="page"] span {
        background-color: #DB2777;
        /* bg-pink-600 */
        border-color: #DB2777;
        color: white;
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
    }
    

    // FITUR WISHLIST - TIDAK ADA DI PERSYARATAN
    
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
    

    // FITUR ADD TO CART - SESUAI PERSYARATAN
    function addToCart(produkId) {
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

    // *******************************************************************
    // MAIN DOM CONTENT LOADED
    // *******************************************************************

    document.addEventListener('DOMContentLoaded', function() {
        
        // --- CHAT NOTIFICATION LOGIC - SESUAI PERSYARATAN (Contact to Admin) ---
        const chatBadge = document.getElementById('chat-badge');
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

        // FITUR FAVORIT - TIDAK ADA DI PERSYARATAN
    
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
                        Tidak menemukan buku
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