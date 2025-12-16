@extends('layouts.user')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-6xl"> 
    <h1 class="text-3xl font-bold text-yellow-900 dark:text-yellow-100 mb-6">Pesanan Anda</h1>

    
    
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
                    <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Ringkasan Pesanan</h2>

                    <ul class="divide-y divide-gray-200 dark:divide-gray-700 mb-4">
                        @php $total = 0; @endphp
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
                                $total += $itemSubtotal;
                            @endphp
                            <li class="py-3">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $item->produk->nama }}</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            Jumlah: {{ $item->jumlah }} x Rp {{ number_format($item->produk->harga, 0, ',', '.') }}
                                        </div>
                                        
                                        @if(count($toppingDetails) > 0)
                                            <div class="mt-1 text-xs text-yellow-600 dark:text-yellow-400">
                                                <strong>+ Topping:</strong>
                                                @foreach($toppingDetails as $index => $topping)
                                                    {{ $topping['name'] }} (+Rp {{ number_format($topping['price'], 0, ',', '.') }}){{ $index < count($toppingDetails) - 1 ? ', ' : '' }}
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="font-bold text-right text-gray-800 dark:text-gray-200 ml-4">
                                        Rp {{ number_format($itemSubtotal, 0, ',', '.') }}
                                    </div>
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
                    class="bg-yellow-50 dark:bg-gray-800 p-6 rounded-xl shadow-lg space-y-5"
                    id="checkoutForm">
                    @csrf

                    <h2 class="text-2xl font-bold text-yellow-900 dark:text-yellow-100 mb-6">
                        Detail Pesanan & Pembayaran
                    </h2>

                    {{-- Informasi User --}}
                    <div class="mb-8">
                        <div class="bg-white dark:bg-gray-700 p-6 rounded-xl border border-gray-200 dark:border-gray-600 shadow-sm">
                            <label for="nama_customer" class="block text-lg font-semibold mb-3 text-yellow-900 dark:text-yellow-100">
                                Informasi Pelanggan
                            </label>
                            <input type="text" name="nama_customer" id="nama_customer" required
                                class="w-full px-5 py-3 border @error('nama_customer') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-xl focus:outline-none focus:ring-3 focus:ring-yellow-400/50 focus:border-yellow-500 dark:bg-gray-800 dark:text-gray-200 dark:placeholder-gray-400 transition-all duration-200 text-lg"
                                placeholder="Masukkan nama lengkap Anda" value="{{ old('nama_customer') }}">
                            @error('nama_customer')<p class="text-red-500 text-sm mt-2">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div class="mb-8">
                        <label class="block text-lg font-semibold mb-6 text-yellow-900 dark:text-yellow-100">
                            Pilih Metode Pembayaran
                        </label>
                        <div class="grid grid-cols-1 @if(config('fitur.qris_payment')) md:grid-cols-2 @endif gap-4">
                            
                            @if(config('fitur.qris_payment'))
                            {{-- QRIS Option --}}
                            <div class="payment-option">
                                <input type="radio" name="metode_pembayaran" value="qris" class="sr-only" id="qris_option">
                                <div class="qris-card cursor-pointer border-2 border-gray-300 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 dark:border-gray-600 rounded-xl p-6 transition-all duration-300 hover:shadow-lg hover:border-blue-400 hover:scale-105 transform">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                                            <span class="text-white font-bold text-xl">QR</span>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">QRIS DANA</h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Scan & bayar instant</p>
                                            <div class="flex items-center mt-1">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Cash Option --}}
                            <div class="payment-option">
                                <input type="radio" name="metode_pembayaran" value="cash" class="sr-only" id="cash_option">
                                <div class="cash-card cursor-pointer border-2 border-gray-300 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-700 dark:to-gray-800 dark:border-gray-600 rounded-xl p-6 transition-all duration-300 hover:shadow-lg hover:border-green-400 hover:scale-105 transform">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">CASH</span>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900 ">Bayar Tunai</h3>
                                            <p class="text-sm text-gray-800 ">Bayar langsung di kasir</p>
                                            <div class="flex items-center mt-1">
                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                   
                    @if(config('fitur.qris_payment'))
                    {{-- QRIS Section (only for QRIS) --}}
                    <div id="qrisSection" style="display: none;">
                        <div class="mt-6 text-center">
                            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 p-8 rounded-2xl inline-block shadow-2xl border-2 border-blue-200 dark:border-blue-700">
                                <h3 class="text-xl font-bold text-blue-800 dark:text-blue-300 mb-4">
                                    Scan QR Code untuk Bayar
                                </h3>
                                <div class="relative">
                                    <img src="{{ asset('images/qris-sample.jpeg') }}" alt="QRIS Code" 
                                         class="w-56 h-56 mx-auto mb-4 border-4 border-blue-300 dark:border-blue-600 rounded-xl shadow-lg">
                                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm">✓</span>
                                    </div>
                                </div>
                                <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-xl mt-4">
                                    <p class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-2">
                                        Buka aplikasi DANA dan scan QR Code
                                    </p>
                                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                        Total: Rp {{ number_format($total, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Cash Section (only for Cash) --}}
                    <div id="cashSection" style="display: none;">
                        <div class="mt-6">
                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-2 border-green-200 dark:border-green-700 rounded-xl p-8 shadow-lg">
                                <h3 class="text-xl font-bold text-green-800 dark:text-green-300 mb-6">
                                    Detail Pembayaran Tunai
                                </h3>
                                
                                {{-- Total Pesanan --}}
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">
                                        Total yang Harus Dibayar
                                    </label>
                                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border">
                                        <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                                            Rp <span id="totalAmount">{{ number_format($total, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Input Nominal Tunai --}}
                                <div class="mb-6">
                                    <label for="cash_amount" class="block text-sm font-semibold mb-3 text-gray-700 dark:text-gray-300">
                                        Masukkan Nominal Uang Tunai
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-4 text-gray-500 font-bold text-lg">Rp</span>
                                        <input type="number" name="cash_amount" id="cash_amount" 
                                               min="{{ $total }}" step="1000"
                                               class="w-full pl-12 pr-4 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-3 focus:ring-green-400/50 focus:border-green-500 dark:bg-gray-700 dark:text-gray-200 text-lg font-semibold transition-all duration-200"
                                               placeholder="0"
                                               oninput="calculateChange()">
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                        Minimal: Rp {{ number_format($total, 0, ',', '.') }}
                                    </p>
                                </div>
                                
                                {{-- Perhitungan Kembalian --}}
                                <div id="changeSection" class="mt-6 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border-2 border-blue-200 dark:border-blue-700 shadow-md" style="display: none;">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-semibold text-gray-700 dark:text-gray-300">
                                            Kembalian Anda:
                                        </span>
                                        <span id="changeAmount" class="text-2xl font-bold text-blue-600 dark:text-blue-400">Rp 0</span>
                                    </div>
                                </div>
                                
                                {{-- Error untuk nominal kurang --}}
                                <div id="cashError" class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 rounded-r-lg text-red-700 dark:text-red-400" style="display: none;">
                                    <div class="flex items-center">
                                        <span class="font-medium">Nominal uang tunai tidak boleh kurang dari total pesanan!</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>





                   

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-600">
                        <button type="button" id="cancelButton"
                            class="w-full sm:w-auto flex-1 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-4 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl focus:outline-none focus:ring-3 focus:ring-gray-400/50 transform hover:scale-105 active:scale-95"
                            onclick="cancelOrder()">
                            Batal Pesanan
                        </button>
                        <button type="submit" id="submitButton"
                            class="w-full sm:w-auto flex-2 bg-gradient-to-r from-yellow-600 to-yellow-700 hover:from-yellow-700 hover:to-yellow-800 text-white font-bold py-4 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl focus:outline-none focus:ring-3 focus:ring-yellow-400/50 transform hover:scale-105 active:scale-95">
                            Konfirmasi dan Proses Pesanan
                        </button>
                    </div>
                </form>
            </div>
            
        </div>
        
    @else
        <div class="bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200 p-4 rounded shadow text-center">
            Keranjang kosong. Silakan tambahkan produk terlebih dahulu.
        </div>
    @endif
</div>

{{-- Custom CSS for enhanced UI --}}
<style>
    /* Payment Card Animations */
    .payment-option .qris-card.selected {
        border-color: #3b82f6 !important;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%) !important;
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3), 0 0 0 3px rgba(59, 130, 246, 0.1);
        transform: translateY(-2px);
    }
    
    .payment-option .cash-card.selected {
        border-color: #10b981 !important;
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%) !important;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3), 0 0 0 3px rgba(16, 185, 129, 0.1);
        transform: translateY(-2px);
    }
    
    /* Input Focus Animations */
    input:focus {
        transform: translateY(-1px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    /* Button Pulse Animation for Submit */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    
    .payment-selected {
        animation: pulse 2s infinite;
    }
    
    /* Smooth Transitions */
    * {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    }
    
    .payment-option:hover {
        transform: translateY(-1px);
    }
    
    .payment-option:active {
        transform: translateY(0);
    }
    
    /* Cancel Button Styling */
    #cancelButton:hover {
        transform: translateY(-1px);
    }
    
    #cancelButton:active {
        transform: translateY(0);
    }
    
    /* Responsive button layout */
    @media (min-width: 640px) {
        .flex-2 {
            flex: 2;
        }
        .flex-1 {
            flex: 1;
        }
    }
</style>

{{-- JavaScript untuk Validasi Form & Dark Mode --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkoutForm = document.getElementById('checkoutForm');
        const submitButton = document.getElementById('submitButton');
        const darkModeToggle = document.getElementById('darkModeToggle');
        const htmlElement = document.documentElement;

        // Inisialisasi Dark Mode
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            htmlElement.classList.add('dark');
            if (document.querySelector('.light-icon')) {
                document.querySelector('.light-icon').classList.add('hidden');
                document.querySelector('.dark-icon').classList.remove('hidden');
            }
        } else {
            htmlElement.classList.remove('dark');
            if (document.querySelector('.light-icon')) {
                document.querySelector('.light-icon').classList.remove('hidden');
                document.querySelector('.dark-icon').classList.add('hidden');
            }
        }

        // Toggle Dark Mode Event Listener
        if (darkModeToggle) {
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
        }



        // Pastikan form tidak terkirim jika validasi custom gagal
        checkoutForm.addEventListener('submit', function(e) {
            let isValid = true;

            // Cek Alamat (hanya untuk takeaway)
            const alamatInput = document.getElementById('alamat');
            const orderType = document.querySelector('input[name="order_type"]:checked').value;
            
            if (orderType === 'takeaway' && alamatInput.value.trim().length < 10) {
                 e.preventDefault();
                 alamatInput.classList.add('border-red-500');
                 alamatInput.focus();
                 alert('Alamat harus diisi minimal 10 karakter untuk takeaway.');
                 isValid = false;
            }
            
            // Validasi nama pelanggan untuk dine-in
            if (orderType === 'dine_in') {
                const customerNameInput = document.getElementById('customer_name');
                if (customerNameInput.value.trim().length < 2) {
                    e.preventDefault();
                    customerNameInput.classList.add('border-red-500');
                    customerNameInput.focus();
                    alert('Nama pelanggan harus diisi untuk dine-in.');
                    isValid = false;
                }
            }
            
            // Validasi pembayaran tunai
            const paymentMethod = document.querySelector('input[name="metode_pembayaran"]:checked').value;
            if (paymentMethod === 'cash') {
                const cashAmountInput = document.getElementById('cash_amount');
                const cashAmount = parseFloat(cashAmountInput.value) || 0;
                const totalAmount = {{ $total }};
                
                if (cashAmount < totalAmount) {
                    e.preventDefault();
                    cashAmountInput.classList.add('border-red-500');
                    cashAmountInput.focus();
                    alert('Nominal uang tunai tidak boleh kurang dari total pesanan!');
                    isValid = false;
                }
            }
            
            if (!isValid) {
                 alert('Mohon periksa kembali form Anda. Pastikan semua data telah diisi dengan benar.');
            } else {
                submitButton.disabled = true;
                submitButton.innerText = ' sedang memproses...';
            }
        });

        // Handle order type changes
        document.querySelectorAll('input[name="order_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const alamatSection = document.getElementById('alamatSection');
                const dineInSection = document.getElementById('dineInSection');
                const alamatInput = document.getElementById('alamat');
                const takeawayCard = document.querySelector('.takeaway-card');
                const dineInCard = document.querySelector('.dinein-card');
                
                if (this.value === 'dine_in') {
                    // Show dine-in section, hide alamat
                    alamatSection.style.display = 'none';
                    dineInSection.style.display = 'block';
                    alamatInput.removeAttribute('required');
                    
                    // Update card styles
                    takeawayCard.classList.remove('border-yellow-300', 'bg-yellow-50');
                    takeawayCard.classList.add('border-gray-300', 'bg-gray-50');
                    dineInCard.classList.remove('border-gray-300', 'bg-gray-50');
                    dineInCard.classList.add('border-yellow-300', 'bg-yellow-50');
                } else {
                    // Show alamat section, hide dine-in
                    alamatSection.style.display = 'block';
                    dineInSection.style.display = 'none';
                    alamatInput.setAttribute('required', 'required');
                    
                    // Update card styles
                    dineInCard.classList.remove('border-yellow-300', 'bg-yellow-50');
                    dineInCard.classList.add('border-gray-300', 'bg-gray-50');
                    takeawayCard.classList.remove('border-gray-300', 'bg-gray-50');
                    takeawayCard.classList.add('border-yellow-300', 'bg-yellow-50');
                }
            });
        });

        // EVENT LISTENER: Payment Method Selection
        console.log('Setting up payment method handlers...');
        
        // Get elements
        const qrisRadio = document.getElementById('qris_option');
        const cashRadio = document.getElementById('cash_option');
        const qrisCard = document.querySelector('.qris-card');
        const cashCard = document.querySelector('.cash-card');
        const qrisSection = document.getElementById('qrisSection');
        const cashSection = document.getElementById('cashSection');
        const cashAmountInput = document.getElementById('cash_amount');
        
        console.log('Elements:', {
            qrisRadio: !!qrisRadio,
            cashRadio: !!cashRadio,
            qrisCard: !!qrisCard,
            cashCard: !!cashCard,
            qrisSection: !!qrisSection,
            cashSection: !!cashSection
        });
        

        
        // Function to switch to QRIS
        function selectQRIS() {
            console.log('Selecting QRIS payment...');
            if (qrisRadio) qrisRadio.checked = true;
            if (cashRadio) cashRadio.checked = false;
            
            if (qrisSection) qrisSection.style.display = 'block';
            if (cashSection) cashSection.style.display = 'none';
            
            // Update card styles with enhanced selected class
            if (qrisCard) {
                qrisCard.classList.add('selected');
                // Add pulse animation to submit button
                if (submitButton) submitButton.classList.add('payment-selected');
            }
            if (cashCard) {
                cashCard.classList.remove('selected');
            }
        }
        
        // Function to switch to Cash
        function selectCash() {
            console.log('Selecting Cash payment...');
            if (cashRadio) cashRadio.checked = true;
            if (qrisRadio) qrisRadio.checked = false;
            
            if (qrisSection) qrisSection.style.display = 'none';
            if (cashSection) cashSection.style.display = 'block';
            
            // Add required to cash field
            if (cashAmountInput) cashAmountInput.setAttribute('required', 'required');
            
            // Update card styles with enhanced selected class
            if (cashCard) {
                cashCard.classList.add('selected');
                // Add pulse animation to submit button
                if (submitButton) submitButton.classList.add('payment-selected');
            }
            if (qrisCard) {
                qrisCard.classList.remove('selected');
            }
        }
        
        // Add click handlers to cards with visual feedback
        
        if (qrisCard) {
            qrisCard.addEventListener('click', function(e) {
                console.log('QRIS card clicked!');
                e.preventDefault();
                e.stopPropagation();
                selectQRIS();
                // Visual feedback
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 100);
            });
        }
        
        if (cashCard) {
            cashCard.addEventListener('click', function(e) {
                console.log('Cash card clicked!');
                e.preventDefault();
                e.stopPropagation();
                selectCash();
                // Visual feedback
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 100);
            });
        }
        
        // Add change handlers to radio buttons
        if (qrisRadio) {
            qrisRadio.addEventListener('change', function() {
                if (this.checked) selectQRIS();
            });
        }
        
        if (cashRadio) {
            cashRadio.addEventListener('change', function() {
                if (this.checked) selectCash();
            });
        }
        
        // Initialize with default selection
        @if(config('fitur.qris_payment'))
        selectQRIS();
        @else
        selectCash();
        @endif
        
        // Calculate Change Function
        window.calculateChange = function() {
            const totalAmount = {{ $total }};
            const cashAmount = parseFloat(document.getElementById('cash_amount').value) || 0;
            const changeSection = document.getElementById('changeSection');
            const changeAmountDisplay = document.getElementById('changeAmount');
            const cashError = document.getElementById('cashError');
            
            if (cashAmount >= totalAmount) {
                const change = cashAmount - totalAmount;
                changeAmountDisplay.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(change);
                changeSection.style.display = 'block';
                cashError.style.display = 'none';
                document.getElementById('cash_amount').classList.remove('border-red-500');
                document.getElementById('cash_amount').classList.add('border-green-500');
            } else if (cashAmount > 0) {
                changeSection.style.display = 'none';
                cashError.style.display = 'block';
                document.getElementById('cash_amount').classList.remove('border-green-500');
                document.getElementById('cash_amount').classList.add('border-red-500');
            } else {
                changeSection.style.display = 'none';
                cashError.style.display = 'none';
                document.getElementById('cash_amount').classList.remove('border-red-500', 'border-green-500');
            }
        };
        
        // Cancel Order Function
        window.cancelOrder = function() {
            // Use SweetAlert if available, otherwise use confirm
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Batal Pesanan?',
                    text: 'Anda akan kembali ke menu utama. Keranjang tidak akan dihapus.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#6B7280',
                    cancelButtonColor: '#EAB308', 
                    confirmButtonText: 'Ya, Kembali ke Menu',
                    cancelButtonText: 'Lanjut Checkout',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Kembali ke Menu...',
                            text: 'Keranjang akan dimuat kembali',
                            icon: 'info',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Redirect after short delay with parameter to indicate return from checkout
                        setTimeout(() => {
                            window.location.href = '{{ route("user.dashboard") }}?from=checkout_cancel';
                        }, 1000);
                    }
                });
            } else {
                // Fallback to native confirm
                if (confirm('Batal pesanan dan kembali ke menu? Keranjang tidak akan dihapus.')) {
                    window.location.href = '{{ route("user.dashboard") }}?from=checkout_cancel';
                }
            }
        };
    });
</script>
@endsection