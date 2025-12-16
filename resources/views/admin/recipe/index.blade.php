@extends('layouts.admin')

@section('title', 'Resep Produk (BOM)')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-amber-800 mb-4 sm:mb-0">
            <i class="fas fa-clipboard-list mr-2"></i>Resep Produk (Bill of Materials)
        </h1>
        
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="{{ route('admin.recipe.create') }}" 
               class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-plus mr-2"></i>Tambah Resep
            </a>
        </div>
    </div>

    <!-- Filter dan Search -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Produk</label>
                <select name="product_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 text-black">
                    <option value="">Semua Produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Bahan</label>
                <select name="kategori_bahan" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 text-black">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriBahan as $kategori)
                        <option value="{{ $kategori }}" {{ request('kategori_bahan') == $kategori ? 'selected' : '' }}>
                            {{ ucfirst($kategori) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari produk/bahan..." 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 text-black">
            </div>

            <div class="flex items-end">
                <button type="submit" 
                        class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg transition duration-300 w-full">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Resep</p>
                    <p class="text-3xl font-bold">{{ $recipes->total() }}</p>
                </div>
                <i class="fas fa-clipboard-list text-4xl text-blue-200"></i>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Produk dengan Resep</p>
                    <p class="text-3xl font-bold">{{ $products->filter(function($p) { return $p->recipes && $p->recipes->count() > 0; })->count() }}</p>
                </div>
                <i class="fas fa-coffee text-4xl text-green-200"></i>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Bahan Digunakan</p>
                    <p class="text-3xl font-bold">{{ $recipes->pluck('inventory_id')->unique()->count() }}</p>
                </div>
                <i class="fas fa-boxes text-4xl text-purple-200"></i>
            </div>
        </div>
    </div>

    <!-- Tabel Resep dengan Layout Baru -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Daftar Resep Produk</h2>
        </div>

        <div class="overflow-x-auto">
            @php
                // Group recipes by product
                $groupedRecipes = $recipes->groupBy('product_id');
            @endphp
            
            <table class="w-full border-collapse">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 uppercase border">Nama Menu</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 uppercase border">Bahan</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500 uppercase border">Jumlah yang dapat diproduksi</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500 uppercase border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groupedRecipes as $productId => $productRecipes)
                        @php
                            $firstRecipe = $productRecipes->first();
                            $product = $firstRecipe->product;
                            // Hitung jumlah yang dapat diproduksi berdasarkan bahan yang paling terbatas
                            $maxProduction = $productRecipes->min('max_production');
                        @endphp
                        
                        <tr>
                            <!-- Nama Menu -->
                            <td class="px-6 py-4 border align-top" rowspan="{{ $productRecipes->count() + 1 }}">
                                <div class="flex items-center">
                                    @if($product->foto)
                                        <img src="{{ asset('storage/' . $product->foto) }}" 
                                             alt="{{ $product->nama }}"
                                             class="w-12 h-12 rounded-lg object-cover mr-3">
                                    @endif
                                    <div>
                                        <p class="font-bold text-gray-900 text-lg">{{ $product->nama }}</p>
                                        <p class="text-sm text-gray-500">{{ $product->kategori->nama ?? 'N/A' }}</p>
                                        <p class="text-xs text-amber-600 mt-1">{{ $productRecipes->count() }} bahan</p>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Header Bahan -->
                            <td class="px-6 py-2 bg-gray-100 border font-medium text-gray-700">
                                Bahan bahan
                            </td>
                            
                            <!-- Jumlah yang dapat diproduksi -->
                            <td class="px-6 py-4 border text-center align-middle" rowspan="{{ $productRecipes->count() + 1 }}">
                                <div class="bg-amber-50 rounded-lg p-4">
                                    <p class="text-3xl font-bold text-amber-600">{{ $maxProduction }}</p>
                                    <p class="text-sm text-gray-600 mt-1">unit produk</p>
                                    @if($maxProduction == 0)
                                        <p class="text-xs text-red-500 mt-2">Stok tidak mencukupi</p>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Aksi -->
                            <td class="px-6 py-4 border text-center align-middle" rowspan="{{ $productRecipes->count() + 1 }}">
                                <div class="flex flex-col space-y-2">
                                    <a href="{{ route('admin.recipe.show', $product->id) }}" 
                                       class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs transition">
                                        <i class="fas fa-eye mr-1"></i>Detail
                                    </a>
                                    <a href="{{ route('admin.recipe.create') }}?product_id={{ $product->id }}" 
                                       class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs transition">
                                        <i class="fas fa-plus mr-1"></i>Tambah
                                    </a>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Detail Bahan -->
                        @foreach($productRecipes as $recipe)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 border">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">{{ $recipe->inventory->nama_bahan }}</p>
                                            <div class="flex items-center space-x-4 mt-1">
                                                <span class="inline-block bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs">
                                                    {{ ucfirst($recipe->inventory->kategori_bahan) }}
                                                </span>
                                                
                                                <span class="text-xs text-gray-600">
                                                    Kebutuhan: <strong>{{ $recipe->formatted_quantity }}</strong>
                                                </span>
                                                
                                                <span class="text-xs text-gray-600">
                                                    Stok: <strong>{{ number_format($recipe->inventory->stok_tersedia, 2) }} {{ $recipe->inventory->satuan }}</strong>
                                                </span>
                                                
                                                <span class="inline-block px-2 py-1 rounded-full text-xs font-medium
                                                    @if($recipe->inventory->status == 'tersedia') bg-green-100 text-green-800
                                                    @elseif($recipe->inventory->status == 'menipis') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                    {{ ucfirst($recipe->inventory->status) }}
                                                </span>
                                            </div>
                                            
                                            @if($recipe->inventory->satuan !== $recipe->unit)
                                                <p class="text-xs text-blue-600 mt-1">
                                                    @php
                                                        $convertedStock = 0;
                                                        if($recipe->inventory->satuan === 'kg' && $recipe->unit === 'gram') {
                                                            $convertedStock = $recipe->inventory->stok_tersedia * 1000;
                                                        } elseif($recipe->inventory->satuan === 'liter' && $recipe->unit === 'ml') {
                                                            $convertedStock = $recipe->inventory->stok_tersedia * 1000;
                                                        }
                                                    @endphp
                                                    @if($convertedStock > 0)
                                                        = {{ number_format($convertedStock, 2) }} {{ $recipe->unit }}
                                                    @endif
                                                </p>
                                            @endif
                                            
                                            @if($recipe->notes)
                                                <p class="text-xs text-gray-500 mt-1 italic">{{ $recipe->notes }}</p>
                                            @endif
                                        </div>
                                        
                                        <div class="flex space-x-2 ml-4">
                                            <a href="{{ route('admin.recipe.edit', $recipe->id) }}" 
                                               class="text-amber-600 hover:text-amber-900 transition duration-300">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.recipe.destroy', $recipe->id) }}" 
                                                  method="POST" 
                                                  class="inline-block delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" 
                                                        class="text-red-600 hover:text-red-900 transition duration-300 delete-btn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center border">
                                <div class="text-gray-500">
                                    <i class="fas fa-clipboard-list text-4xl mb-4"></i>
                                    <p class="text-lg">Belum ada resep produk</p>
                                    <p class="text-sm">Tambahkan resep untuk menghubungkan produk dengan bahan baku</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recipes->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $recipes->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Delete confirmation
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.delete-form');
            
            Swal.fire({
                title: 'Hapus Resep?',
                text: 'Data resep ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection