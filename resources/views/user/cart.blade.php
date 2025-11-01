@extends('layouts.user')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">🛍️ Keranjang Belanja</h1>

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
    <div class="bg-white p-6 rounded-xl shadow-md transition-all">
        <table class="w-full text-left text-sm">
            <thead class="text-gray-600 uppercase border-b">
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
                <tr class="border-b hover:bg-gray-50 transition-all">
                    <td class="py-4 font-medium text-gray-800">
                        {{ $item->produk->nama }}
                    </td>
                    <td class="py-4 text-gray-600">
                        Rp {{ number_format($item->produk->harga, 0, ',', '.') }}
                    </td>
                    <td class="py-4 text-center">
                        <div class="inline-flex items-center space-x-2">
                            <form method="POST" action="{{ route('user.cart.update') }}">
                                @csrf
                                <button type="submit" name="decrease" value="{{ $item->id }}"
                                    class="px-2 py-1 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-full transition">
                                    &minus;
                                </button>
                            </form>
                            <span class="w-6 text-center text-gray-800">{{ $item->jumlah }}</span>
                            <form method="POST" action="{{ route('user.cart.update') }}">
                                @csrf
                                <button type="submit" name="increase" value="{{ $item->id }}"
                                    class="px-2 py-1 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-full transition disabled:bg-red-400" {{ $item->jumlah >= $item->produk->stok ? 'disabled' : '' }}>
                                    &#43;
                                </button>
                            </form>
                        </div>
                    </td>
                    <td class="py-4 text-gray-700">
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

        {{-- Total Section --}}
        <div class="mt-6 flex justify-between items-center border-t pt-4">
            <div class="text-lg font-semibold text-gray-800">
                Total: <span class="text-blue-600">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
            </div>
            <a href="{{ route('user.checkout.form') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full shadow transition">
                ✅ Checkout Sekarang
            </a>
        </div>
    </div>
    @else
    <div class="text-center text-gray-600 text-lg font-medium mt-10">
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
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
</script>
@endsection