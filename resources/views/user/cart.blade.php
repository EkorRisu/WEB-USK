@extends('layouts.user')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-yellow-900 dark:text-yellow-100 mb-6">🛒 Keranjang Pesanan</h1>

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
    <div class="bg-yellow-50 dark:bg-gray-800 p-6 rounded-xl shadow-md transition-all">
        <table class="w-full text-left text-sm">
            <thead class="text-yellow-800 dark:text-yellow-300 uppercase border-b border-yellow-200 dark:border-gray-700">
                <tr>
                    <th class="py-3">☕ Menu & Topping</th>
                    <th class="py-3">💵 Harga Satuan</th>
                    <th class="py-3 text-center">Jumlah</th>
                    <th class="py-3">Total Harga</th>
                    <th class="py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @foreach ($items as $item)
                @php
                    // Hitung harga topping
                    $toppingTotal = 0;
                    $toppingDetails = [];
                    if($item->toppings && is_array($item->toppings) && count($item->toppings)) {
                        $selectedToppings = \App\Models\Topping::whereIn('id', $item->toppings)->get();
                        foreach($selectedToppings as $topping) {
                            $toppingTotal += $topping->price;
                            $toppingDetails[] = [
                                'name' => $topping->name,
                                'price' => $topping->price
                            ];
                        }
                    }
                    
                    // Total harga per item (produk + topping) * jumlah
                    $itemBasePrice = $item->produk->harga + $toppingTotal;
                    $itemSubtotal = $itemBasePrice * $item->jumlah;
                    $grandTotal += $itemSubtotal;
                @endphp
                
                {{-- Menu Utama --}}
                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                    <td class="py-4 font-medium text-gray-800 dark:text-gray-100">
                        <div class="flex items-center">
                            <span class="text-yellow-600 mr-2">☕</span>
                            <div>
                                <div class="font-semibold">{{ $item->produk->nama }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Menu Utama</div>
                            </div>
                        </div>
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
                    <td class="py-4 text-gray-700 dark:text-gray-300 font-semibold">
                        Rp {{ number_format($itemSubtotal, 0, ',', '.') }}
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
                
                {{-- Topping Details (jika ada) --}}
                @if(count($toppingDetails) > 0)
                    @foreach($toppingDetails as $topping)
                    <tr class="bg-yellow-50 dark:bg-gray-750 border-b border-yellow-100 dark:border-gray-600">
                        <td class="py-2 pl-8 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex items-center">
                                <span class="text-yellow-500 mr-2">🔸</span>
                                <div>
                                    <div class="italic">{{ $topping['name'] }}</div>
                                    <div class="text-xs text-yellow-600 dark:text-yellow-400">Extra Topping</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-2 text-sm text-yellow-600 dark:text-yellow-400">
                            +Rp {{ number_format($topping['price'], 0, ',', '.') }}
                        </td>
                        <td class="py-2 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ $item->jumlah }}
                        </td>
                        <td class="py-2 text-sm text-yellow-600 dark:text-yellow-400">
                            +Rp {{ number_format($topping['price'] * $item->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="py-2"></td>
                    </tr>
                    @endforeach
                    
                    {{-- Summary row untuk menunjukkan total item ini --}}
                    <tr class="bg-yellow-100 dark:bg-gray-700 border-b-2 border-yellow-300 dark:border-gray-500">
                        <td class="py-2 pl-4 text-sm font-medium text-gray-700 dark:text-gray-300">
                            <div class="flex items-center">
                                <span class="text-yellow-600 mr-2">📋</span>
                                <span>Total untuk {{ $item->produk->nama }}</span>
                            </div>
                        </td>
                        <td class="py-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ count($toppingDetails) + 1 }} item
                        </td>
                        <td class="py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $item->jumlah }}
                        </td>
                        <td class="py-2 text-sm font-bold text-yellow-700 dark:text-yellow-300">
                            Rp {{ number_format($itemSubtotal, 0, ',', '.') }}
                        </td>
                        <td class="py-2"></td>
                    </tr>
                    
                    {{-- Spacer --}}
                    <tr>
                        <td colspan="5" class="py-2"></td>
                    </tr>
                @else
                    {{-- Jika tidak ada topping, tambahkan spacer --}}
                    <tr>
                        <td colspan="5" class="py-1">
                            <div class="border-t border-gray-100 dark:border-gray-700"></div>
                        </td>
                    </tr>
                @endif
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
                    class="w-full md:w-auto flex items-center justify-center space-x-2 bg-yellow-800 hover:bg-yellow-900 dark:bg-yellow-700 dark:hover:bg-yellow-800 text-white font-bold py-3 px-6 rounded-full shadow-lg transition-all transform hover:scale-105">
                    <span>Checkout Sekarang</span>
                    {{-- Ikon panah untuk tampilan modern --}}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
        </div>
    @else
    <div class="text-center text-gray-600 dark:text-gray-400 text-lg font-medium mt-10">
        🛒 Keranjangmu masih kosong — ayo tambahkan kopi favoritmu!
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