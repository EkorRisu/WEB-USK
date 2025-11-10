@extends('layouts.user')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-6xl"> 
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">💳 Checkout</h1>

    
    
    {{-- SECTION: Pesan Error Flash Session (untuk error dari Controller/catch) --}}
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg dark:bg-red-900 dark:text-red-200">
            <p>{{ session('error') }}</p>
        </div>
    @endif
    {{-- SECTION: Pesan Error Validasi Sisi Server (jika $errors->any()) --}}
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg dark:bg-red-900 dark:text-red-200">
            <p class="font-bold">Gagal memproses pesanan:</p>
            <ul class="list-disc ml-5 mt-2 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{-- END SECTION --}}

    @if ($items->count() > 0)
        {{-- FIX UI: Layout Grid 3 kolom untuk Desktop --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-8">

            {{-- Kolom Ringkasan (1/3 di Desktop, tampil pertama di Mobile) --}}
            <div class="lg:col-span-1 mb-6 lg:mb-0">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">📦 Ringkasan Pesanan</h2>

                    <ul class="divide-y divide-gray-200 dark:divide-gray-700 mb-4">
                        @php $total = 0; @endphp
                        @foreach ($items as $item)
                            @php
                                $subtotal = $item->produk->harga * $item->jumlah;
                                $total += $subtotal;
                            @endphp
                            <li class="py-3 flex justify-between items-center">
                                <div>
                                    <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $item->produk->nama }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Jumlah: {{ $item->jumlah }} x Rp
                                        {{ number_format($item->produk->harga, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="font-bold text-right text-gray-800 dark:text-gray-200">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Total --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex justify-between items-center text-lg font-bold text-gray-900 dark:text-white">
                            <span>Total:</span>
                            <span class="text-blue-600 dark:text-blue-400">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Form (2/3 di Desktop) --}}
            <div class="lg:col-span-2">
                <form method="POST" action="{{ route('user.checkout.process') }}"
                    class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg space-y-5"
                    id="checkoutForm">
                    @csrf

                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Detail Pengiriman & Pembayaran</h2>

                    <div>
                        <label for="alamat" class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">📍 Alamat Pengiriman</label>
                        <textarea name="alamat" id="alamat" rows="3" required
                            class="w-full px-4 py-2 border @error('alamat') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-200 dark:placeholder-gray-400">{{ old('alamat') }}</textarea>
                        @error('alamat')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="telepon" class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">📞 Nomor Telepon</label>
                        <input type="text" name="telepon" id="telepon" required
                            value="{{ old('telepon') }}"
                            placeholder="Contoh: 081234567890"
                            pattern="08[0-9]{7,13}" 
                            title="Nomor telepon harus diawali 08 dan hanya mengandung angka."
                            class="w-full px-4 py-2 border @error('telepon') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:placeholder-gray-400">
                        <p id="telepon-error" class="text-red-500 text-xs italic mt-1 hidden">Nomor harus diawali '08' dan hanya mengandung angka (min 9 digit total).</p>
                        @error('telepon')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
                    </div>

                    @php
                        $metodes = ['Transfer Bank (BCA)', 'OVO', 'Dana', 'Gopay'];
                    @endphp

                    <input type="hidden" name="metode_pembayaran" id="metode_pembayaran" value="{{ old('metode_pembayaran', 'Transfer Bank (BCA)') }}">

                    <div class="relative">
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">💰 Metode Pembayaran</label>

                        <button id="dropdownButton" data-dropdown-toggle="dropdownMetode"
                            class="w-full px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-left text-gray-900 dark:text-gray-200 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 flex justify-between items-center"
                            type="button">
                            <span id="selectedMetode">{{ old('metode_pembayaran', 'Transfer Bank (BCA)') }}</span>
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div id="dropdownMetode" class="z-10 hidden bg-white dark:bg-gray-700 divide-y divide-gray-100 dark:divide-gray-600 rounded-lg shadow w-full absolute mt-1 border dark:border-gray-600">
                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownButton">
                                @foreach ($metodes as $metode)
                                    <li>
                                        <button type="button" class="block px-4 py-2 w-full text-left hover:bg-gray-100 dark:hover:bg-gray-600"
                                            onclick="selectMetode('{{ $metode }}')">
                                            {{ $metode }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    {{-- Info Pembayaran --}}
                    <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Silakan transfer ke nomor berikut:</p>
                        <p class="text-lg font-bold text-blue-700 dark:text-blue-400 mt-1">0831 2313 2235</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Atas nama: Admin Toko Buku</p>
                    </div>

                    <button type="submit" id="submitButton"
                        class="w-full bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold py-3 rounded-full transition shadow-md focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                        🛒 Konfirmasi dan Proses Pesanan
                    </button>
                </form>
            </div>
            
        </div>
        
    @else
        <div class="bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200 p-4 rounded shadow text-center">
            Keranjang kosong. Silakan tambahkan produk terlebih dahulu.
        </div>
    @endif
</div>

{{-- JavaScript untuk Dropdown dan Validasi Kustom Telepon & Dark Mode --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownButton = document.getElementById('dropdownButton');
        const dropdownMetode = document.getElementById('dropdownMetode');
        const teleponInput = document.getElementById('telepon');
        const teleponError = document.getElementById('telepon-error');
        const checkoutForm = document.getElementById('checkoutForm');
        const submitButton = document.getElementById('submitButton');
        const darkModeToggle = document.getElementById('darkModeToggle');
        const htmlElement = document.documentElement;

        // Inisialisasi Dark Mode
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            htmlElement.classList.add('dark');
            document.querySelector('.light-icon').classList.add('hidden');
            document.querySelector('.dark-icon').classList.remove('hidden');
        } else {
            htmlElement.classList.remove('dark');
            document.querySelector('.light-icon').classList.remove('hidden');
            document.querySelector('.dark-icon').classList.add('hidden');
        }

        // Toggle Dark Mode Event Listener
        darkModeToggle.addEventListener('click', function() {
            if (htmlElement.classList.contains('dark')) {
                htmlElement.classList.remove('dark');
                localStorage.theme = 'light';
                document.querySelector('.light-icon').classList.remove('hidden');
                document.querySelector('.dark-icon').classList.add('hidden');
            } else {
                htmlElement.classList.add('dark');
                localStorage.theme = 'dark';
                document.querySelector('.light-icon').classList.add('hidden');
                document.querySelector('.dark-icon').classList.remove('hidden');
            }
        });

        // Toggle Dropdown (Logika tetap sama)
        dropdownButton.addEventListener('click', function(e) {
            e.preventDefault(); 
            dropdownMetode.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside (Logika tetap sama)
        document.addEventListener('click', function(e) {
            if (!dropdownButton.contains(e.target) && !dropdownMetode.contains(e.target)) {
                dropdownMetode.classList.add('hidden');
            }
        });

        // Kustom Validasi Telepon (Client-side - Logika tetap sama)
        teleponInput.addEventListener('input', function() {
            const regex = /^08\d{7,13}$/; 
            if (this.value === "") {
                teleponError.classList.add('hidden');
                this.classList.remove('border-red-500', 'border-green-500');
            } else if (regex.test(this.value)) {
                teleponError.classList.add('hidden');
                this.classList.remove('border-red-500', 'border-gray-300', 'dark:border-gray-600');
                this.classList.add('border-green-500'); 
            } else {
                teleponError.classList.remove('hidden');
                this.classList.remove('border-green-500', 'border-gray-300', 'dark:border-gray-600');
                this.classList.add('border-red-500'); 
            }
        });

        // Pastikan form tidak terkirim jika validasi custom gagal (Logika tetap sama)
        checkoutForm.addEventListener('submit', function(e) {
            const regex = /^08\d{7,13}$/; 
            let isValid = true;

            // Cek validasi telepon
            if (!regex.test(teleponInput.value)) {
                e.preventDefault(); 
                teleponError.classList.remove('hidden');
                teleponInput.focus();
                isValid = false;
            }

            // Cek Alamat (Tambahkan validasi minimal panjang di client-side jika belum)
            const alamatInput = document.getElementById('alamat');
            if (alamatInput.value.trim().length < 10) {
                 e.preventDefault();
                 alamatInput.classList.add('border-red-500');
                 alamatInput.focus();
                 isValid = false;
            }
            
            if (!isValid) {
                 alert('Mohon periksa kembali form Anda. Pastikan Alamat dan Nomor Telepon valid.');
            } else {
                submitButton.disabled = true;
                submitButton.innerText = ' sedang memproses...';
            }
        });
    });
    
    // Fungsi untuk memilih metode pembayaran (Logika tetap sama)
    function selectMetode(value) {
        document.getElementById('selectedMetode').innerText = value;
        document.getElementById('metode_pembayaran').value = value;
        document.getElementById('dropdownMetode').classList.add('hidden');
    }
</script>
@endsection