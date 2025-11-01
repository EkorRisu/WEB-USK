@extends('layouts.user')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <h1 class="text-3xl font-bold text-black mb-6">💳 Checkout</h1>

    {{-- SECTION: Pesan Error Validasi Sisi Server (jika ada) --}}
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg">
            <p class="font-bold">Gagal memproses pesanan:</p>
            <ul class="list-disc ml-5 mt-2 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{-- END SECTION: Pesan Error Validasi Sisi Server --}}

    @if ($items->count() > 0)
        {{-- Ringkasan Keranjang --}}
        <div class="bg-white rounded-xl shadow-md p-6 mb-6 text-black">
            <h2 class="text-xl font-semibold mb-4">📦 Ringkasan Pesanan</h2>

            <ul class="divide-y divide-gray-200 mb-4">
                @php $total = 0; @endphp
                @foreach ($items as $item)
                    @php
                        $subtotal = $item->produk->harga * $item->jumlah;
                        $total += $subtotal;
                    @endphp
                    <li class="py-2 flex justify-between items-center">
                        <div>
                            <div class="font-semibold">{{ $item->produk->nama }}</div>
                            <div class="text-sm text-gray-600">Jumlah: {{ $item->jumlah }} x Rp
                                {{ number_format($item->produk->harga, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="font-bold text-right">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </div>
                    </li>
                @endforeach

            </ul>

            <div class="text-right text-lg font-bold text-green-800">
                Total: Rp {{ number_format($total, 0, ',', '.') }}
            </div>

        </div>

        {{-- Form Checkout --}}
        <form method="POST" action="{{ route('user.checkout.process') }}"
            class="bg-white p-6 rounded-xl shadow space-y-4 text-black"
            id="checkoutForm"> {{-- Tambahkan ID untuk JavaScript --}}
            @csrf

            <div>
                <label for="alamat" class="block text-sm font-medium mb-1">📍 Alamat Pengiriman</label>
                <textarea name="alamat" id="alamat" rows="3" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-green-500">{{ old('alamat') }}</textarea>
                @error('alamat')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="telepon" class="block text-sm font-medium mb-1">📞 Nomor Telepon</label>
                {{-- Tambahkan pattern untuk validasi client-side --}}
                <input type="text" name="telepon" id="telepon" required
                    value="{{ old('telepon') }}"
                    placeholder="Contoh: 081234567890"
                    pattern="08[0-9]{7,13}" 
                    title="Nomor telepon harus diawali 08 dan hanya mengandung angka."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-green-500">
                <p id="telepon-error" class="text-red-500 text-xs italic mt-1 hidden">Nomor harus diawali '08' dan hanya mengandung angka (min 9 digit total).</p>
                @error('telepon')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
            </div>

            @php
                $metodes = ['Transfer Bank (BCA)', 'OVO', 'Dana', 'Gopay'];
            @endphp

            <input type="hidden" name="metode_pembayaran" id="metode_pembayaran" value="{{ old('metode_pembayaran', 'Transfer Bank (BCA)') }}">

            <div class="relative">
                <label class="block text-sm font-medium mb-1">💰 Metode Pembayaran</label>

                <button id="dropdownButton" data-dropdown-toggle="dropdownMetode"
                    class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-left text-black font-medium focus:outline-none focus:ring focus:border-green-500 flex justify-between items-center"
                    type="button">
                    <span id="selectedMetode">{{ old('metode_pembayaran', 'Transfer Bank (BCA)') }}</span>
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div id="dropdownMetode" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-full absolute mt-1">
                    <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownButton">
                        @foreach ($metodes as $metode)
                            <li>
                                <button type="button" class="block px-4 py-2 w-full text-left hover:bg-gray-100"
                                    onclick="selectMetode('{{ $metode }}')">
                                    {{ $metode }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="bg-gray-100 p-4 rounded-lg">
                <p class="text-sm font-medium">Silakan transfer ke nomor berikut:</p>
                <p class="text-lg font-bold text-green-700 mt-1">0831 2313 2235</p>
                <p class="text-sm text-gray-600">Atas nama: Admin Toko Buku</p>
            </div>

            <button type="submit" id="submitButton"
                class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-semibold py-2 rounded-full transition shadow-md">
                🛒 Konfirmasi dan Proses Pesanan
            </button>
        </form>

    @else
        <div class="bg-red-100 text-red-700 p-4 rounded shadow text-center">
            Keranjang kosong. Silakan tambahkan produk terlebih dahulu.
        </div>
    @endif
</div>

{{-- JavaScript untuk Dropdown dan Validasi Kustom Telepon --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownButton = document.getElementById('dropdownButton');
        const dropdownMetode = document.getElementById('dropdownMetode');
        const teleponInput = document.getElementById('telepon');
        const teleponError = document.getElementById('telepon-error');
        const checkoutForm = document.getElementById('checkoutForm');

        // Toggle Dropdown
        dropdownButton.addEventListener('click', function(e) {
            e.preventDefault(); 
            dropdownMetode.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownButton.contains(e.target) && !dropdownMetode.contains(e.target)) {
                dropdownMetode.classList.add('hidden');
            }
        });

        // Kustom Validasi Telepon (Client-side)
        teleponInput.addEventListener('input', function() {
            // Regex di sini mencerminkan regex di Controller: harus diawali 08, dan sisanya angka (minimal 7 angka setelah 08)
            const regex = /^08\d{7,13}$/; 
            if (regex.test(this.value)) {
                teleponError.classList.add('hidden');
                this.classList.remove('border-red-500');
                this.classList.add('border-green-500');
            } else {
                teleponError.classList.remove('hidden');
                this.classList.add('border-red-500');
                this.classList.remove('border-green-500');
            }
        });

        // Pastikan form tidak terkirim jika validasi custom gagal
        checkoutForm.addEventListener('submit', function(e) {
            const regex = /^08\d{7,13}$/; 
            if (!regex.test(teleponInput.value)) {
                e.preventDefault(); 
                teleponError.classList.remove('hidden');
                teleponInput.focus();
                alert('Mohon perbaiki Nomor Telepon. Harus diawali 08 dan hanya mengandung angka.'); // Gunakan alert sederhana
            }
        });
    });
    
    // Fungsi untuk memilih metode pembayaran
    function selectMetode(value) {
        document.getElementById('selectedMetode').innerText = value;
        document.getElementById('metode_pembayaran').value = value;
        document.getElementById('dropdownMetode').classList.add('hidden');
    }
</script>
@endsection
