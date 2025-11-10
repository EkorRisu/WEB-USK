@extends('layouts.user')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-6">🛍️ Keranjang Belanja</h1>

    @if (session('success'))
    <script>
        Swal.fire({
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            icon: 'success',
            timer: 1800,
            showConfirmButton: false
        });
    </script>
    @endif

    @if ($items->count())
    {{-- Card Utama --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md transition-all">
        <table class="w-full text-left text-sm">
            <thead class="text-gray-600 dark:text-gray-400 uppercase border-b dark:border-gray-700">
                <tr>
                    <th class="py-3">📘 Produk</th>
                    <th class="py-3">💵 Harga</th>
                    <th class="py-3 text-center">Jumlah</th>
                    <th class="py-3">Subtotal</th>
                    <th class="py-3 text-center">❌</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @foreach ($items as $item)
                @php
                $subtotal = $item->produk->harga * $item->jumlah;
                $grandTotal += $subtotal;
                @endphp
                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                    <td class="py-4 font-medium text-gray-800 dark:text-gray-100">
                        {{ $item->produk->nama }}
                    </td>
                    <td class="py-4 text-gray-600 dark:text-gray-300">
                        Rp {{ number_format($item->produk->harga, 0, ',', '.') }}
                    </td>
                    <td class="py-4 text-center">
                        <div class="inline-flex items-center space-x-2">
                            <form method="POST" action="{{ route('user.cart.update') }}">
                                @csrf
                                <button type="submit" name="decrease" value="{{ $item->id }}"
                                    class="px-2 py-1 text-gray-700 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 rounded-full transition">
                                    &minus;
                                </button>
                            </form>
                            <span class="w-6 text-center text-gray-800 dark:text-gray-200">{{ $item->jumlah }}</span>
                            <form method="POST" action="{{ route('user.cart.update') }}">
                                @csrf
                                <button type="submit" name="increase" value="{{ $item->id }}"
                                    class="px-2 py-1 text-gray-700 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 rounded-full transition disabled:bg-red-400 disabled:opacity-50 dark:disabled:bg-red-700"
                                    {{ $item->jumlah >= $item->produk->stok ? 'disabled' : '' }}>
                                    &#43;
                                </button>
                            </form>
                        </div>
                    </td>
                    <td class="py-4 text-gray-700 dark:text-gray-300">
                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                    </td>
                    <td class="py-4 text-center">
                        <form method="POST" action="{{ route('user.cart.remove', $item->id) }}"
                            class="delete-form inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                class="text-red-600 hover:text-red-800 font-bold transition delete-btn">
                                🗑️
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-6 border-t border-gray-200 dark:border-gray-700">
            {{-- Wrapper untuk responsivitas --}}
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pt-6">
                
                {{-- Info Total Harga --}}
                <div class="w-full md:w-auto text-left">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Grand Total</div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                    </div>
                </div>
                
                {{-- Tombol Checkout --}}
                <a href="{{ route('user.checkout.form') }}"
                    class="w-full md:w-auto flex items-center justify-center space-x-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-full shadow-lg transition-all transform hover:scale-105">
                    <span>Checkout Sekarang</span>
                    {{-- Ikon panah untuk tampilan modern --}}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
        </div>
    @else
    <div class="text-center text-gray-600 dark:text-gray-400 text-lg font-medium mt-10">
        🛒 Keranjangmu masih kosong, ayo tambahkan buku favoritmu!
    </div>
    @endif
</div>

{{-- SweetAlert2 untuk konfirmasi hapus --}}
<script>
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            const form = this.closest('form');
            Swal.fire({
                title: 'Hapus Produk?',
                text: "Produk akan dihapus dari keranjang!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
                // Menambahkan dukungan dark mode untuk SweetAlert
                // customClass: {
                //     popup: 'dark:bg-gray-800 dark:text-gray-200',
                //     title: 'dark:text-gray-200',
                //     content: 'dark:text-gray-300'
                // }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection