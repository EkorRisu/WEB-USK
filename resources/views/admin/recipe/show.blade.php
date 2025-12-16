@extends('layouts.admin')

@section('title', 'Detail Resep Produk')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-amber-800">
            <i class="fas fa-eye mr-2"></i>Detail Resep: {{ $product->nama }}
        </h1>
        
        <div class="flex space-x-2">
            <a href="{{ route('admin.recipe.create') }}?product_id={{ $product->id }}" 
               class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-plus mr-2"></i>Tambah Bahan
            </a>
            <a href="{{ route('admin.recipe.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Product Info -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center space-x-4">
            @if($product->foto)
                <img src="{{ asset('storage/' . $product->foto) }}" 
                     alt="{{ $product->nama }}"
                     class="w-20 h-20 rounded-lg object-cover">
            @endif
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900">{{ $product->nama }}</h2>
                <p class="text-amber-600 font-semibold text-lg">Rp {{ number_format($product->harga, 0, ',', '.') }}</p>
                <p class="text-gray-600">{{ $product->kategori->nama ?? 'N/A' }}</p>
                @if($product->deskripsi)
                    <p class="text-gray-500 text-sm mt-2">{{ $product->deskripsi }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Production Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Stok Produk</p>
                    <p class="text-3xl font-bold">{{ $product->stok }}</p>
                </div>
                <i class="fas fa-boxes text-4xl text-green-200"></i>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Bahan</p>
                    <p class="text-3xl font-bold">{{ $product->recipes->count() }}</p>
                </div>
                <i class="fas fa-clipboard-list text-4xl text-blue-200"></i>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Max Produksi</p>
                    <p class="text-3xl font-bold">{{ $product->max_production ?? 0 }}</p>
                </div>
                <i class="fas fa-industry text-4xl text-purple-200"></i>
            </div>
        </div>
    </div>

    <!-- Recipe List -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Bahan yang Dibutuhkan</h3>
        </div>

        @if($product->recipes->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 uppercase">Bahan Baku</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 uppercase">Kebutuhan</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 uppercase">Stok Tersedia</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 uppercase">Max Produksi</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($product->recipes as $recipe)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $recipe->inventory->nama_bahan }}</p>
                                        <p class="text-sm text-gray-500">
                                            <span class="inline-block bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">
                                                {{ ucfirst($recipe->inventory->kategori_bahan) }}
                                            </span>
                                        </p>
                                        @if($recipe->notes)
                                            <p class="text-xs text-gray-500 mt-1">{{ $recipe->notes }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $recipe->formatted_quantity }}</p>
                                    <p class="text-xs text-gray-500">per unit produk</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900">
                                        {{ number_format($recipe->inventory->stok_tersedia, 2) }} {{ $recipe->inventory->satuan }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-lg font-bold 
                                        @if($recipe->max_production > 10) text-green-600
                                        @elseif($recipe->max_production > 0) text-yellow-600
                                        @else text-red-600 @endif">
                                        {{ $recipe->max_production }}
                                    </p>
                                    <p class="text-xs text-gray-500">unit produk</p>
                                </td>
                                <td class="px-6 py-4">
                                    @if($recipe->max_production > 10)
                                        <span class="inline-block px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Stok Aman
                                        </span>
                                    @elseif($recipe->max_production > 0)
                                        <span class="inline-block px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Stok Menipis
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>Stok Habis
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('admin.recipe.edit', $recipe->id) }}" 
                                           class="text-amber-600 hover:text-amber-900 transition duration-300" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.recipe.destroy', $recipe->id) }}" 
                                              method="POST" 
                                              class="inline-block delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    class="text-red-600 hover:text-red-900 transition duration-300 delete-btn" 
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-12 text-center">
                <div class="text-gray-500">
                    <i class="fas fa-clipboard-list text-4xl mb-4"></i>
                    <p class="text-lg">Belum ada resep untuk produk ini</p>
                    <p class="text-sm mb-4">Tambahkan bahan-bahan yang dibutuhkan untuk membuat produk ini</p>
                    <a href="{{ route('admin.recipe.create') }}?product_id={{ $product->id }}" 
                       class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg transition duration-300 inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i>Tambah Bahan Pertama
                    </a>
                </div>
            </div>
        @endif
    </div>

    @if($product->recipes->count() > 0)
        <!-- Production Planning -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-calculator mr-2"></i>Perencanaan Produksi
            </h3>
            
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Berdasarkan stok bahan saat ini:</p>
                        <p class="text-2xl font-bold text-amber-600">
                            {{ $product->max_production }} unit produk
                        </p>
                        <p class="text-xs text-gray-500">dapat diproduksi sekarang</p>
                    </div>
                    
                    @php
                        $limitingFactor = $product->recipes->sortBy('max_production')->first();
                    @endphp
                    
                    @if($limitingFactor)
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Faktor pembatas:</p>
                            <p class="text-lg font-medium text-red-600">
                                {{ $limitingFactor->inventory->nama_bahan }}
                            </p>
                            <p class="text-xs text-gray-500">
                                ({{ $limitingFactor->max_production }} unit maksimal)
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
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
                title: 'Hapus Bahan dari Resep?',
                text: 'Bahan ini akan dihapus dari resep produk!',
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