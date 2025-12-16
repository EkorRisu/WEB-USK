@extends('layouts.navbar')

@section('content')
{{-- Debug: Check if variables exist --}}
@php
    $produk = $produk ?? collect([]);
    $kategori = $kategori ?? collect([]);
@endphp

{{-- Pass data to JavaScript --}}a
<script>
    window.allProducts = @json($produk);
    window.allCategories = @json($kategori);
</script>

<section
    class="min-h-screen overflow-hidden bg-[url(https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80)] bg-cover bg-center bg-fixed bg-no-repeat">
    <div
        class="min-h-screen bg-gradient-to-b from-black/70 to-black/50 px-4 md:px-64 flex flex-col justify-center items-center">
        <!-- Hero Content -->
        <div class="text-center space-y-6">
            <h1 class="text-4xl md:text-7xl font-bold text-white mt-20 animate-fade-in">
                Welcome To Coffee Shop
            </h1>
            <p class="text-lg md:text-xl text-gray-200 max-w-2xl mx-auto">
                Anti bad day. Cuma perlu ngopi
            </p>

            <!-- Buttons -->
            <div class="flex gap-4 justify-center mt-8">
                <button id="welcomeLoginBtn"
                        class="hover-slide px-8 py-3 bg-white text-black rounded-lg font-semibold
                               border border-black transition-all duration-300">
                    Login
                </button>

                <button id="welcomeRegisterBtn"
                        class="hover-slide px-8 py-3 bg-white text-black rounded-lg font-semibold
                               border border-black transition-all duration-300">
                    Register
                </button>
            </div>
        </div>

        <!-- Features Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">
            <div class="bg-white/10 backdrop-blur-sm p-6 rounded-lg text-white">
                <h3 class="text-xl font-semibold mb-2">Kopi Premium</h3>
                <p>Biji kopi berkualitas tinggi yang dipetik langsung dari perkebunan terbaik Indonesia. Setiap cangkir memberikan cita rasa autentik dan aroma yang tak terlupakan.</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm p-6 rounded-lg text-white">
                <h3 class="text-xl font-semibold mb-2">Menu Variatif</h3>
                <p>Dari espresso klasik hingga kreasi modern, lengkap dengan beragam topping dan add-on yang memanjakan lidah. Temukan kombinasi sempurna sesuai selera Anda.</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm p-6 rounded-lg text-white">
                <h3 class="text-xl font-semibold mb-2">Pemesanan Praktis</h3>
                <p>Sistem pemesanan online yang cepat dan mudah. Cukup beberapa klik, kopi favorit Anda akan segera disiapkan dengan penuh perhatian dan diantar ke tempat Anda.</p>
            </div>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="py-16 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Menu Favorit Kami
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-300">
                Cicipi berbagai varian kopi dan minuman spesial pilihan terbaik
            </p>
        </div>

        @if(isset($produk) && $produk->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($produk as $product)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative overflow-hidden rounded-t-xl">
                    @if($product->foto)
                        <img src="{{ asset('storage/' . $product->foto) }}" 
                             alt="{{ $product->nama }}"
                             class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105"
                             loading="lazy">
                    @else
                        <div class="w-full h-48 bg-gradient-to-br from-amber-100 to-amber-200 dark:from-amber-800 dark:to-amber-900 flex items-center justify-center">
                            <span class="text-4xl">☕</span>
                        </div>
                    @endif
                    <div class="absolute top-4 left-4">
                        <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            {{ $product->kategori->nama ?? 'Menu' }}
                        </span>
                    </div>
                </div>
                
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ $product->nama }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-2">
                        {{ $product->deskripsi }}
                    </p>
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                            Rp {{ number_format($product->harga, 0, ',', '.') }}
                        </span>
                        <div class="flex gap-2">
                            @auth
                                <button onclick="addToCart({{ $product->id }}, '{{ $product->nama }}', {{ $product->harga }})"
                                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                                    Pesan
                                </button>
                            @else
                                <button onclick="document.getElementById('loginModal')?.classList.remove('hidden')"
                                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                                    Login untuk Pesan
                                </button>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-12">
            @auth
                <a href="{{ url('/user/dashboard') }}" 
                   class="inline-flex items-center px-8 py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition-colors duration-200">
                    Lihat Semua Menu
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            @else
                <button onclick="document.getElementById('loginModal')?.classList.remove('hidden')"
                        class="inline-flex items-center px-8 py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition-colors duration-200">
                    Login untuk Lihat Menu
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            @endauth
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-500 dark:text-gray-400 text-lg">
                Belum ada produk tersedia.
            </p>
        </div>
        @endif
    </div>
</section>



@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const loginModal = document.getElementById('loginModal');
        const registerModal = document.getElementById('registerModal');
        
        // Welcome page buttons
        document.getElementById('welcomeLoginBtn')?.addEventListener('click', () => {
            loginModal?.classList.remove('hidden');
        });
        
        document.getElementById('welcomeRegisterBtn')?.addEventListener('click', () => {
            registerModal?.classList.remove('hidden');
        });
    });

    // Cart functions for welcome page
    function addToCart(productId, productName, productPrice) {
        // Add to localStorage cart
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        // Check if product already exists
        let existingItem = cart.find(item => item.id == productId);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                id: productId,
                nama: productName,
                harga: productPrice,
                quantity: 1
            });
        }
        
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Show success message
        showToast(`${productName} ditambahkan ke keranjang!`, 'success');
        
        // Sync with server if logged in
        @auth
        syncCartWithServer();
        @endauth
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white font-medium transition-all duration-300 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }

    @auth
    async function syncCartWithServer() {
        try {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const response = await fetch('/cart/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ cart: cart })
            });
            
            if (response.ok) {
                console.log('Cart synced successfully');
            }
        } catch (error) {
            console.error('Failed to sync cart:', error);
        }
    }
    @endauth

    // Category filter functions
    async function filterByCategory(categoryId, categoryName) {
        try {
            // First try to use existing data
            if (window.allProducts && window.allProducts.length > 0) {
                const filteredProducts = window.allProducts.filter(product => 
                    product.kategori_id == categoryId
                );
                displayCategoryProducts(filteredProducts, categoryName);
            } else {
                // Fallback to API call
                const response = await fetch(`/api/products/category/${categoryId}`);
                const products = await response.json();
                displayCategoryProducts(products, categoryName);
            }
            
            // Scroll to category products section
            document.getElementById('categoryProducts').scrollIntoView({ 
                behavior: 'smooth' 
            });
        } catch (error) {
            console.error('Failed to fetch category products:', error);
        }
    }

    function displayCategoryProducts(products, categoryName) {
        const section = document.getElementById('categoryProducts');
        const title = document.getElementById('categoryTitle');
        const grid = document.getElementById('categoryProductGrid');
        
        title.textContent = `Menu ${categoryName}`;
        section.classList.remove('hidden');
        
        if (products.length === 0) {
            grid.innerHTML = '<div class="col-span-full text-center py-12"><p class="text-gray-500 dark:text-gray-400 text-lg">Tidak ada produk dalam kategori ini.</p></div>';
            return;
        }
        
        grid.innerHTML = products.map(product => `
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative overflow-hidden rounded-t-xl">
                    ${product.foto ? 
                        `<img src="{{ asset('storage/') }}/${product.foto}" 
                             alt="${product.nama}"
                             class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105"
                             loading="lazy">` :
                        `<div class="w-full h-48 bg-gradient-to-br from-amber-100 to-amber-200 dark:from-amber-800 dark:to-amber-900 flex items-center justify-center">
                            <span class="text-4xl">☕</span>
                        </div>`
                    }
                    <div class="absolute top-4 left-4">
                        <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            ${product.kategori ? product.kategori.nama : 'Menu'}
                        </span>
                    </div>
                </div>
                
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                        ${product.nama}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-2">
                        ${product.deskripsi || ''}
                    </p>
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                            Rp ${new Intl.NumberFormat('id-ID').format(product.harga)}
                        </span>
                        <div class="flex gap-2">
                            @auth
                                <button onclick="addToCart(${product.id}, '${product.nama}', ${product.harga})"
                                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                                    Pesan
                                </button>
                            @else
                                <button onclick="document.getElementById('loginModal')?.classList.remove('hidden')"
                                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                                    Login untuk Pesan
                                </button>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function showAllProducts() {
        hideCategoryProducts();
        document.getElementById('categoryProducts').scrollIntoView({ 
            behavior: 'smooth' 
        });
    }

    function hideCategoryProducts() {
        document.getElementById('categoryProducts').classList.add('hidden');
    }
</script>
@endpush
@endsection