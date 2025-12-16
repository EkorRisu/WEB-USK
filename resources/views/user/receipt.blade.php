@extends('layouts.user')

@section('title', 'Struk Digital #' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT) . ' - Azur Coffee')

@push('meta')
<meta name="description" content="Struk digital pembayaran #{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }} - Azur Coffee. Total: Rp {{ number_format($transaction->total, 0, ',', '.') }}">
<meta property="og:title" content="Struk Digital - Azur Coffee">
<meta property="og:description" content="Struk pembayaran #{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }} - Total: Rp {{ number_format($transaction->total, 0, ',', '.') }}">
<meta property="og:type" content="article">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="Struk Digital - Azur Coffee">
<meta name="twitter:description" content="Struk pembayaran #{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }} - Total: Rp {{ number_format($transaction->total, 0, ',', '.') }}">
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    {{-- Web Receipt Notice --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6 no-print">
        <div class="flex items-center gap-3">
            <div class="text-2xl">💻</div>
            <div>
                <h3 class="font-semibold text-blue-900 dark:text-blue-100">Struk Digital Web</h3>
                <p class="text-sm text-blue-700 dark:text-blue-300">Anda dapat melihat struk ini kapan saja tanpa perlu download. Bookmark halaman ini untuk akses mudah!</p>
            </div>
        </div>
    </div>
    
    <div class="receipt-container bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden print:shadow-none print:rounded-none">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 text-white p-6 text-center">
            <h1 class="text-3xl font-bold mb-2">Azur Coffee</h1>
            <p class="text-yellow-100">Struk Digital Pembayaran</p>
            <div class="mt-4 space-y-2">
                <div>
                    <span class="bg-green-500 text-white px-4 py-2 rounded-full text-sm font-semibold">
                        ✅ PEMBAYARAN BERHASIL
                    </span>
                </div>
                <p class="text-yellow-100 text-sm">
                    Dibuat: {{ $transaction->created_at->locale('id')->diffForHumans() }}
                </p>
                <p class="text-yellow-200 text-xs">
                    Struk ini dapat diakses kapan saja di web tanpa perlu download
                </p>
            </div>
        </div>

        {{-- Transaction Info --}}
        <div class="p-6">
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No. Transaksi</p>
                    <p class="font-semibold text-lg">#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal</p>
                    <p class="font-semibold">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Customer</p>
                    <p class="font-semibold">{{ $transaction->customer_name ?? $transaction->user->name }}</p>
                </div>
                
            </div>

            {{-- Items --}}
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Detail Pesanan</h3>
                <div class="space-y-3">
                    @php $subtotal = 0; @endphp
                    @foreach($transaction->items as $item)
                        @php
                            $itemTotal = $item->harga * $item->jumlah;
                            $toppingTotal = 0;
                            if($item->toppings && is_array($item->toppings)) {
                                $selectedToppings = \App\Models\Topping::whereIn('id', $item->toppings)->get();
                                $toppingTotal = $selectedToppings->sum('price') * $item->jumlah;
                            }
                            $lineTotal = $itemTotal + $toppingTotal;
                            $subtotal += $lineTotal;
                        @endphp
                        
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $item->nama_barang }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item->jumlah }}x @ Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
                                
                                @if($item->toppings && is_array($item->toppings) && count($item->toppings) > 0)
                                    <div class="ml-4 mt-1">
                                        @foreach($selectedToppings as $topping)
                                            <p class="text-xs text-blue-600 dark:text-blue-400">+ {{ $topping->name }} (+Rp {{ number_format($topping->price, 0, ',', '.') }})</p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($lineTotal, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Payment Summary --}}
            <div class="border-t border-gray-200 dark:border-gray-700 mt-6 pt-4">
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <p class="text-gray-600 dark:text-gray-400">Subtotal</p>
                        <p class="font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                        <p class="text-gray-900 dark:text-gray-100">Total Pembayaran</p>
                        <p class="text-green-600">Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Payment Method --}}
            <div class="border-t border-gray-200 dark:border-gray-700 mt-6 pt-4">
                <div class="flex justify-between items-center">
                    <p class="text-gray-600 dark:text-gray-400">Metode Pembayaran</p>
                    <div class="text-right">
                        <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $transaction->metode_pembayaran }}</p>
                        
                        {{-- Display cash payment details --}}
                        @if($transaction->metode_pembayaran === 'Bayar Tunai' && isset($transaction->cash_amount))
                            <div class="mt-2 space-y-1">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Uang Tunai: Rp {{ number_format($transaction->cash_amount, 0, ',', '.') }}</p>
                                @if(isset($transaction->change_amount) && $transaction->change_amount > 0)
                                    <p class="text-sm text-green-600 dark:text-green-400 font-semibold">Kembalian: Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</p>
                                @endif
                            </div>
                        @endif
                        
                        @if(isset($transaction->bank_tujuan))
                            <p class="text-sm text-gray-500">Bank: {{ $transaction->bank_tujuan }}</p>
                        @endif
                        @if(isset($transaction->nama_pengirim))
                            <p class="text-sm text-gray-500">Pengirim: {{ $transaction->nama_pengirim }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="receipt-actions mt-8 space-y-4 no-print">
                {{-- Primary Actions --}}
                <div class="flex gap-3">
                    <button onclick="printReceipt()" 
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg text-center font-semibold transition duration-300 flex items-center justify-center gap-2">
                        🖨️ Print Struk
                    </button>
                </div>
                
                {{-- Secondary Actions --}}
                <div class="flex gap-3">
                    @if(config('fitur.pdf_invoice'))
                        <a href="{{ route('user.transaction.download', $transaction->id) }}" 
                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg text-center font-semibold transition duration-300 flex items-center justify-center gap-2">
                            📄 Download PDF
                        </a>
                    @else
                        {{-- Show disabled state if PDF feature is off --}}
                        {{-- <div class="flex-1 bg-gray-400 text-white py-3 px-4 rounded-lg text-center font-semibold flex items-center justify-center gap-2 opacity-50 cursor-not-allowed" 
                             title="Fitur download PDF tidak diaktifkan">
                            📄 Download PDF (Disabled)
                        </div> --}}
                    @endif
                    <a href="{{ route('user.transactions') }}" 
                       class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-3 px-4 rounded-lg text-center font-semibold transition duration-300 flex items-center justify-center gap-2">
                        📋 Semua Transaksi
                    </a>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="bg-gray-50 dark:bg-gray-700 p-6 text-center">
            <div class="mb-4">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Terima kasih telah berbelanja di Azur Coffee! ☕</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">Struk digital ini tersimpan permanen dan dapat diakses kapan saja</p>
            </div>
            
            {{-- Digital Receipt Benefits --}}
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 no-print">
                <h4 class="font-semibold text-green-800 dark:text-green-200 mb-2">✨ Keunggulan Struk Digital</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div class="flex items-center gap-2 text-green-700 dark:text-green-300">
                        🌱 <span>Ramah Lingkungan</span>
                    </div>
                    <div class="flex items-center gap-2 text-green-700 dark:text-green-300">
                        💾 <span>Tersimpan Permanen</span>
                    </div>
                    <div class="flex items-center gap-2 text-green-700 dark:text-green-300">
                        📱 <span>Akses Kapan Saja</span>
                    </div>
                </div>
                
                @if(!config('fitur.pdf_invoice'))
                    <div class="mt-3 pt-3 border-t border-green-200 dark:border-green-800">
                        <div class="flex items-center gap-2 text-blue-700 dark:text-blue-300">
                            ℹ️ <span class="text-xs">Mode Digital-Only: Struk tersedia di web, print manual tersedia</span>
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="mt-4 text-xs text-gray-400 dark:text-gray-500">
                <p>URL struk: <code class="bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded text-xs">{{ url()->current() }}</code></p>
                <p class="mt-1">Bookmark halaman ini untuk akses mudah di masa depan</p>
            </div>
        </div>
    </div>
</div>

{{-- Receipt Styles --}}
<style>
    /* Mobile optimizations */
    @media (max-width: 640px) {
        .receipt-container {
            margin: 0;
            border-radius: 0;
            min-height: 100vh;
        }
        .receipt-actions button,
        .receipt-actions a {
            font-size: 0.875rem;
            padding: 0.75rem 1rem;
        }
    }
    
    /* Print styles */
    @media print {
        .no-print { display: none !important; }
        body { 
            background: white !important;
            font-size: 12px;
        }
        .container { 
            max-width: none !important; 
            padding: 0 !important; 
            margin: 0 !important;
        }
        .receipt-container {
            box-shadow: none !important;
            border: none !important;
            border-radius: 0 !important;
        }
    }
    
    /* Enhanced animations */
    .receipt-actions button:hover,
    .receipt-actions a:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
</style>

<script>
    function printReceipt() {
        window.print();
    }
    

</script>
@endsection