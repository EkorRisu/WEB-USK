@extends('layouts.navbar')

@section('content')

{{-- Bagian CSS & Style --}}
<style>
    /* Animasi Fade In */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in {
        animation: fadeIn 0.8s ease-in-out forwards;
    }

    /* Style Pop-up Read More */
    .read-more-popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        padding: 20px;
        align-items: center;
        justify-content: center;
    }

    .popup-content {
        background-color: white;
        padding: 30px;
        border-radius: 12px;
        max-width: 600px;
        width: 90%;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        max-height: 80vh;
        overflow-y: auto;
    }

    /* Tambahan CSS untuk Visi Misi agar gambar proporsional */
    .visi-misi-card .image-container {
        width: 100%;
        height: 250px;
        /* Tinggi tetap untuk gambar */
        overflow: hidden;
    }

    .visi-misi-card .image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* Memastikan gambar menutupi area tanpa distorsi */
    }
</style>
{{-- PERBAIKAN DI SINI: max-w-6xl diganti menjadi max-w-7xl atau max-w-full --}}
<div class="container mx-auto max-w-7xl py-4 px-4 sm:px-6 lg:px-8 fade-in">

    {{-- Perhatikan bahwa Anda masih memiliki padding horizontal (px-4, sm:px-6, lg:px-8) di container.
    Jika Anda ingin konten benar-benar memenuhi lebar, hapus padding horizontal di sini
    dan tambahkan padding yang lebih besar di dalam section.

    Berdasarkan SS, mari kita buat max-w-full agar lebih lebar, dan tambahkan padding vertikal.
    --}}

    {{-- KODE YANG LEBIH BAIK: Menghapus max-w dan mengatur padding di dalamnya --}}
    <div class="mx-auto w-full px-4 sm:px-6 lg:px-10 py-4 fade-in">

        {{-- 1. Nama Toko dan Visi Misi --}}
        <section class="mb-10 bg-white shadow-xl rounded-xl overflow-hidden border-t-4 border-green-600">

            {{-- Banner Hijau AZUR BOOK --}}
            <div class="relative h-28 md:h-28 bg-green-800 flex items-center justify-center">
                <h1 class="text-4xl sm:text-6xl font-extrabold text-white uppercase tracking-widest z-10">
                    <span class=" bg-opacity-30 p-2 rounded">AZUR BOOK</span>
                </h1>
            </div>

            {{-- Konten Visi Misi --}}
            <div
                class="p-6 md:p-8 flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-8 visi-misi-card">

                {{-- Gambar Perusahaan --}}
                <div class="md:w-1/3 image-container">
                    <img src="https://png.pngtree.com/thumb_back/fw800/background/20210902/pngtree-photographs-of-tall-buildings-standing-outdoors-in-the-city-during-the-image_790832.jpg"
                        alt="Gambar Perusahaan" class="rounded-lg shadow-md transition duration-300 hover:shadow-xl">
                </div>

                <div class="md:w-2/3 text-center md:text-left">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Visi & Misi Kami</h2>
                    <p class="text-gray-600 text-lg italic">
                        "Menciptakan jembatan pengetahuan untuk setiap pembaca, menghadirkan koleksi buku terbaik
                        yang menginspirasi dan mencerahkan masa depan."
                    </p>
                </div>
            </div>
        </section>

        {{-- 2. About Us Perusahaan (Ringkasan + Pop-up Read More) --}}
        <section class="mb-10 bg-white p-6 md:p-10 shadow-xl rounded-xl">
            <h2 class="text-3xl font-bold text-green-700 mb-4 border-b-2 pb-2">📚 Tentang Kami</h2>

            <p class="text-gray-700 leading-relaxed mb-4">
                **Azur Book** didirikan pada tahun 2023 dengan visi untuk memudahkan akses ke dunia literasi. Kami
                percaya
                bahwa buku adalah jendela dunia, dan setiap individu berhak mendapatkan sumber daya yang berkualitas.
                Toko kami menyediakan ribuan judul, mulai dari fiksi klasik hingga literatur ilmiah terbaru.
                Kami berkomitmen untuk memberikan pengalaman belanja yang menyenangkan dan informatif.
                Kepuasan pelanggan adalah prioritas utama kami dalam setiap layanan.
            </p>

            {{-- Button Read More --}}
            <button id="readMoreBtn"
                class="mt-2 text-green-600 hover:text-green-800 font-semibold transition duration-200 focus:outline-none">
                Baca Selengkapnya...
            </button>

        </section>

        {{-- Pop-up Read More Detail --}}
        <div id="readMorePopup" class="read-more-popup">
            <div class="popup-content">
                <h3 class="text-2xl font-bold text-green-700 mb-4">Sejarah & Komitmen Kami</h3>
                <p class=" leading-relaxed space-y-4">
                <p>Azur Book didirikan pada tahun 2023 di tengah revolusi digital, dengan tujuan utama menjembatani
                    kesenjangan antara pembaca dan pengetahuan berkualitas. Kami memulai dari koleksi kecil, dan kini
                    telah
                    berkembang pesat menjadi sumber daya literasi daring yang dipercaya.</p>
                <p>Kami menyadari pentingnya edukasi dan hiburan melalui buku. Oleh karena itu, tim kami berdedikasi
                    untuk
                    melakukan kurasi ketat pada setiap judul yang kami tawarkan. Toko kami menyediakan ribuan judul,
                    mulai
                    dari fiksi klasik hingga literatur ilmiah terbaru.</p>
                <p>Komitmen kami adalah untuk memberikan pengalaman belanja yang mulus dan informatif. Kami menjamin
                    kecepatan pengiriman, keamanan transaksi, dan dukungan pelanggan yang responsif.</p>
                <p>Kami juga menjalankan program sosial 'Satu Buku untuk Masa Depan', di mana sebagian profit kami
                    didonasikan untuk pembelian buku sekolah di daerah terpencil. Dengan Azur Book, Anda tidak hanya
                    membeli
                    buku, tetapi juga berinvestasi pada masa depan literasi bangsa.</p>
                </p>
                <button id="closePopupBtn"
                    class="mt-6 px-4 py-2 bg-red-500 text-white font-semibold rounded hover:bg-red-600 transition">
                    Tutup
                </button>
            </div>
        </div>


        {{-- 3. Produk Terbaru --}}
        <section class="mb-10 bg-white p-6 md:p-10 shadow-xl rounded-xl">
            <h2 class="text-3xl font-bold text-blue-700 mb-6 border-b-2 pb-2">✨ Produk Terbaru Kami</h2>

            {{-- Logika Pengambilan Data dari $latestProducts --}}
            @php
            $latestProducts = $latestProducts ?? collect();
            @endphp

            @if ($latestProducts->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
                {{-- Iterasi Produk --}}
                @foreach ($latestProducts as $produk)
                <div
                    class="bg-gray-50 p-3 rounded-lg shadow-lg hover:shadow-xl transition duration-300 transform hover:-translate-y-1">

                    {{-- Gambar Produk (Diatur tinggi h-48 agar tidak terlalu besar) --}}
                    <img src="{{ asset('storage/' . ($produk->foto ?? 'default.jpg')) }}"
                        alt="{{ $produk->nama ?? 'Produk Tanpa Nama' }}"
                        class="w-full h-48 object-cover rounded-md mb-3 border border-gray-200">

                    {{-- Detail Produk --}}
                    <h4 class="text-base font-semibold text-gray-800 truncate"
                        title="{{ $produk->nama ?? 'Judul Tidak Diketahui' }}">{{ $produk->nama ?? 'Judul Tidak
                        Diketahui'
                        }}</h4>
                    {{-- Mengakses relasi kategori --}}
                    <p class="text-sm text-gray-500">{{ $produk->kategori->nama ?? 'Kategori Tidak Diketahui' }}</p>
                    <a href="{{ route('user.dashboard', ['search' => $produk->nama]) }}"
                        class="text-xs mt-2 inline-block text-blue-500 hover:text-blue-700 font-medium">Lihat Detail</a>
                </div>
                @endforeach
            </div>

            {{-- 4. Pagination untuk Produk Terbaru --}}
            <div class="mt-8 flex justify-center">
                {{ $latestProducts->links() }}
            </div>
            @else
            <p class="text-gray-500 text-center py-10">Belum ada produk terbaru untuk ditampilkan saat ini.</p>
            @endif
        </section>

    </div>

    {{-- Bagian JavaScript --}}
    <script>
        // ------------------------------------------------------------------
    // LOGIKA POP-UP READ MORE
    // ------------------------------------------------------------------
    const readMoreBtn = document.getElementById('readMoreBtn');
    const closePopupBtn = document.getElementById('closePopupBtn');
    const readMorePopup = document.getElementById('readMorePopup');

    // Menampilkan pop-up
    readMoreBtn.addEventListener('click', function() {
        readMorePopup.style.display = 'flex';
    });

    // Menyembunyikan pop-up dengan tombol Tutup
    closePopupBtn.addEventListener('click', function() {
        readMorePopup.style.display = 'none';
    });

    // Menyembunyikan pop-up dengan mengklik di luar konten
    readMorePopup.addEventListener('click', function(e) {
        if (e.target === readMorePopup) {
            readMorePopup.style.display = 'none';
        }
    });

    // Menyembunyikan pop-up dengan tombol ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && readMorePopup.style.display === 'flex') {
            readMorePopup.style.display = 'none';
        }
    });
    </script>
    @endsection