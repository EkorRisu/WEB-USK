@extends('layouts.user')

@section('content')
<div class="bg-gray-900 shadow-2xl p-6 md:p-10 max-w-7xl mx-auto my-8 rounded-xl border border-gray-700">
    <div class="header-section mb-8 border-b border-gray-700 pb-4">
        {{-- IMPROVEMENT: Ukuran font lebih kecil di mobile --}}
        <h1 class="text-2xl md:text-3xl font-extrabold text-white mb-2">👋 Welcome, {{ auth()->user()->name }}</h1>
        <p class="text-gray-400">Jelajahi dan temukan produk terbaik kami.</p>
    </div>

    {{-- NAVIGATION TABS --}}
    {{-- IMPROVEMENT: Menggunakan 'grid' agar lebih rapi di mobile --}}
    <div class="grid grid-cols-3 gap-3 mb-8">
        <a href="{{ route('user.cart') }}"
            class="flex items-center justify-center space-x-2 bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700 transition duration-200 shadow-md">
            <span>🛒</span>
            <span class="hidden sm:inline">Keranjang</span>
        </a>
        <a href="{{ route('user.transactions') }}"
            class="flex items-center justify-center space-x-2 bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
            <span>📦</span>
            <span class="hidden sm:inline">Transaksi</span>
        </a>
        <a href="{{ route('user.chat') }}" id="chat-link"
            class="relative flex items-center justify-center space-x-2 bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
            <span>💬</span>
            <span class="hidden sm:inline">Chat Admin</span>
            <span id="chat-badge"
                class="absolute top-0 right-0 -mt-2 -mr-2 px-2 py-0.5 text-xs font-bold leading-none text-red-100 transform bg-red-600 rounded-full hidden">0</span>
        </a>
    </div>

    {{-- ================================================================== --}}
    {{-- MULAI FORM FILTER UTAMA --}}
    {{-- ================================================================== --}}
    <form method="GET" action="{{ route('user.dashboard') }}">

        {{-- FILTER AND SEARCH FORM (ATAS) --}}
        <div class="mb-8 flex flex-col md:flex-row gap-4 items-center bg-gray-800 p-4 rounded-lg">
            <input type="text" name="search" placeholder="Cari produk..." value="{{ request('search') }}"
                class="border border-gray-700 p-3 rounded-lg w-full md:w-5/12 bg-gray-900 text-white focus:ring-2 focus:ring-pink-500 focus:border-pink-500">

            <select name="kategori"
                class="border border-gray-700 p-3 rounded-lg w-full md:w-3/12 bg-gray-900 text-white focus:ring-2 focus:ring-pink-500">
                <option value="">Semua Kategori</option>
                @foreach ($kategori as $kat)
                <option value="{{ $kat->id }}" {{ request('kategori')==$kat->id ? 'selected' : '' }}>
                    {{ $kat->nama }}
                </option>
                @endforeach
            </select>

            <select name="perpage"
                class="border border-gray-700 p-3 rounded-lg w-full md:w-2/12 bg-gray-900 text-white focus:ring-2 focus:ring-pink-500">
                @php
                $perPageOptions = [5, 10, 20];
                $currentPerPage = request('perpage', 10);
                @endphp
                <option disabled>Produk Per Halaman</option>
                @foreach ($perPageOptions as $option)
                <option value="{{ $option }}" {{ $currentPerPage==$option ? 'selected' : '' }}>
                    {{ $option }} Produk
                </option>
                @endforeach
            </select>

            <button type="submit"
                class="bg-pink-600 text-white px-5 py-3 rounded-lg hover:bg-pink-700 w-full md:w-2/12 transition duration-200 font-semibold">
                Filter
            </button>
        </div>

        {{-- ================================================================== --}}
        {{-- LAYOUT 2 KOLOM (DIBUNGKUS DENGAN ALPINE.JS) --}}
        {{-- ================================================================== --}}

        <div class="flex flex-col md:flex-row gap-8" x-data="{ showFilters: false }">

            {{-- Tombol Hamburger (Hanya tampil di mobile) --}}
            <div class="md:hidden">
                <button type="button" @click="showFilters = true"
                    class="flex items-center justify-between w-full bg-gray-800 text-white p-3 rounded-lg">
                    <span class="font-semibold">Tampilkan Filter & Urutkan</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>

            {{-- ====================================================== --}}
            {{-- KOLOM 1: SIDEBAR FILTER --}}
            {{-- ====================================================== --}}
            <aside 
                class="fixed inset-0 z-40 bg-gray-900 bg-opacity-90 p-6 overflow-y-auto transform -translate-x-full transition-transform duration-300 ease-in-out
                       md:relative md:inset-auto md:z-auto md:p-0 md:overflow-y-visible md:bg-transparent md:bg-opacity-100
                       md:block w-full md:w-1/4 lg:w-1/5 md:translate-x-0"
                :class="{'translate-x-0': showFilters, '-translate-x-full': !showFilters}"
                @click.away="showFilters = false" 
                x-cloak
            >
                {{-- Tombol Close (Hanya tampil di mobile di dalam sidebar) --}}
                <button type="button" @click="showFilters = false" class="md:hidden text-white mb-4 absolute top-4 right-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>

                {{-- Konten Sidebar --}}
                <div class="bg-gray-800 p-4 rounded-lg md:sticky md:top-24"> {{-- Ubah md:top-8 menjadi md:top-24 agar di bawah navbar --}}
                    <h3 class="text-xl font-bold text-white mb-4 border-b border-gray-700 pb-2">Urutkan</h3>
                    <div class="space-y-4">
                        <label for="sort_by" class="block text-sm font-medium text-gray-400">Sortir Berdasarkan</label>
                        <select name="sort_by" id="sort_by"
                            class="border border-gray-700 p-3 rounded-lg w-full bg-gray-900 text-white focus:ring-2 focus:ring-pink-500"
                            onchange="this.form.submit()">
                            @php
                            $currentSort = request('sort_by', 'created_at_desc');
                            $sortOptions = [
                            'created_at_desc' => 'Produk Terbaru',
                            'created_at_asc' => 'Produk Terlama',
                            'harga_asc' => 'Harga: Termurah',
                            'harga_desc' => 'Harga: Termahal',
                            'nama_asc' => 'Nama: A-Z',
                            'nama_desc' => 'Nama: Z-A',
                            ];
                            @endphp
                            @foreach ($sortOptions as $value => $label)
                            <option value="{{ $value }}" {{ $currentSort==$value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </aside>
            {{-- ====================================================== --}}
            {{-- AKHIR DARI SIDEBAR FILTER --}}
            {{-- ====================================================== --}}

            {{-- KOLOM 2: KONTEN UTAMA (PRODUK GRID & PAGINATION) --}}
            <main class="w-full md:w-3/4 lg:w-4/5">

                {{-- PRODUCT GRID --}}
                {{-- IMPROVEMENT: Menambah xl:grid-cols-4 untuk layar super lebar --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
                    @forelse ($produk as $item)
                    <div
                        class="bg-white rounded-xl shadow-lg overflow-hidden transition transform hover:scale-[1.02] duration-300 border-t-4 border-pink-600 flex flex-col">

                        {{-- IMPROVEMENT: Gambar sedikit lebih pendek di mobile --}}
                        <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama }}"
                            class="w-full h-40 sm:h-48 object-cover">

                        <div class="p-5 flex flex-col flex-grow">
                            <span class="text-xs font-semibold text-pink-600 uppercase mb-1">{{ $item->kategori->nama }}</span>
                            <h2 class="text-lg font-bold text-gray-900 mb-2 flex-grow">{{ $item->nama }}</h2>

                            <div class="flex justify-between items-center mb-4">
                                <p class="text-xl text-gray-900 font-extrabold">Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
                                <span class="text-sm font-medium {{ $item->stok > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Stok: {{ $item->stok }}
                                </span>
                            </div>

                            <form action="{{ route('user.cart.add', $item->id) }}" method="POST" class="mt-auto">
                                @csrf
                                @if ($item->stok > 0)
                                <button type="submit"
                                    class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 w-full transition duration-200 font-semibold">
                                    Tambah ke Keranjang
                                </button>
                                @else
                                <button type="button" disabled
                                    class="bg-gray-400 text-gray-200 px-4 py-2 rounded-lg w-full cursor-not-allowed font-semibold">
                                    Stok Habis
                                </button>
                                @endif
                            </form>
                        </div>
                    </div>
                    @empty
                    <p class="col-span-full text-pink-400 text-center text-xl p-10">Produk tidak ditemukan untuk filter
                        ini. 😔</p>
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

{{-- Script notifikasi chat (tidak berubah) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ... (kode polling chat Anda sudah benar) ...
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
                            Swal.fire({
                                title: 'Pesan Baru!',
                                text: `Anda memiliki ${count} pesan yang belum dibaca dari Admin.`,
                                icon: 'info',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 5000,
                                timerProgressBar: true
                            });
                        }
                    } else {
                        chatBadge.classList.add('hidden');
                    }
                    lastKnownCount = count;
                    localStorage.setItem('last_unread_chat_count', count);
                })
                .catch(error => console.error('Error fetching unread chat count:', error));
        }

        checkUnreadChats();
        setInterval(checkUnreadChats, 5000); 
    });
</script>
@endsection