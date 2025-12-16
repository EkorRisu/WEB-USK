@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <div class="mb-6">
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ route('admin.inventory.index') }}" 
               class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-200">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold text-yellow-900 dark:text-yellow-100">📦 Edit Bahan</h1>
        </div>
        <p class="text-gray-600 dark:text-gray-400">Edit informasi bahan: {{ $inventory->nama_bahan }}</p>
    </div>

    <form action="{{ route('admin.inventory.update', $inventory->id) }}" method="POST" 
          class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 space-y-6">
        @csrf
        @method('PUT')

        <!-- Informasi Dasar -->
        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
            <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-4">Informasi Dasar</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nama_bahan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nama Bahan *
                    </label>
                    <input type="text" 
                           id="nama_bahan" 
                           name="nama_bahan" 
                           value="{{ old('nama_bahan', $inventory->nama_bahan) }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                           placeholder="Contoh: Biji Kopi Arabica, Susu Full Cream"
                           required>
                    @error('nama_bahan')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="kategori_bahan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Kategori Bahan *
                    </label>
                    <select id="kategori_bahan" 
                            name="kategori_bahan" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                            required>
                        <option value="">Pilih Kategori</option>
                        <option value="biji_kopi" {{ old('kategori_bahan', $inventory->kategori_bahan) === 'biji_kopi' ? 'selected' : '' }}>☕ Biji Kopi</option>
                        <option value="susu" {{ old('kategori_bahan', $inventory->kategori_bahan) === 'susu' ? 'selected' : '' }}>🥛 Susu</option>
                        <option value="sirup" {{ old('kategori_bahan', $inventory->kategori_bahan) === 'sirup' ? 'selected' : '' }}>🍯 Sirup</option>
                        <option value="topping" {{ old('kategori_bahan', $inventory->kategori_bahan) === 'topping' ? 'selected' : '' }}>🧊 Topping</option>
                        <option value="kemasan" {{ old('kategori_bahan', $inventory->kategori_bahan) === 'kemasan' ? 'selected' : '' }}>📦 Kemasan</option>
                        <option value="lainnya" {{ old('kategori_bahan', $inventory->kategori_bahan) === 'lainnya' ? 'selected' : '' }}>📋 Lainnya</option>
                    </select>
                    @error('kategori_bahan')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Deskripsi
                </label>
                <textarea id="deskripsi" 
                          name="deskripsi" 
                          rows="3"
                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                          placeholder="Deskripsi singkat tentang bahan ini...">{{ old('deskripsi', $inventory->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Informasi Stok -->
        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
            <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-4">Informasi Stok</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="stok_tersedia" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Stok Tersedia *
                    </label>
                    <input type="number" 
                           id="stok_tersedia" 
                           name="stok_tersedia" 
                           value="{{ old('stok_tersedia', $inventory->stok_tersedia) }}"
                           step="0.01"
                           min="0"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                           placeholder="0.00"
                           required>
                    @error('stok_tersedia')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stok_minimum" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Stok Minimum *
                    </label>
                    <input type="number" 
                           id="stok_minimum" 
                           name="stok_minimum" 
                           value="{{ old('stok_minimum', $inventory->stok_minimum) }}"
                           step="0.01"
                           min="0"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                           placeholder="0.00"
                           required>
                    @error('stok_minimum')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Alert jika stok di bawah nilai ini</p>
                </div>

                <div>
                    <label for="satuan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Satuan *
                    </label>
                    <select id="satuan" 
                            name="satuan" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                            required>
                        <option value="">Pilih Satuan</option>
                        <option value="kg" {{ old('satuan', $inventory->satuan) === 'kg' ? 'selected' : '' }}>kg</option>
                        <option value="gram" {{ old('satuan', $inventory->satuan) === 'gram' ? 'selected' : '' }}>gram</option>
                        <option value="liter" {{ old('satuan', $inventory->satuan) === 'liter' ? 'selected' : '' }}>liter</option>
                        <option value="ml" {{ old('satuan', $inventory->satuan) === 'ml' ? 'selected' : '' }}>ml</option>
                        <option value="pcs" {{ old('satuan', $inventory->satuan) === 'pcs' ? 'selected' : '' }}>pcs</option>
                        <option value="pack" {{ old('satuan', $inventory->satuan) === 'pack' ? 'selected' : '' }}>pack</option>
                        <option value="botol" {{ old('satuan', $inventory->satuan) === 'botol' ? 'selected' : '' }}>botol</option>
                        <option value="cup" {{ old('satuan', $inventory->satuan) === 'cup' ? 'selected' : '' }}>cup</option>
                    </select>
                    @error('satuan')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Informasi Harga -->
        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
            <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-4">Informasi Harga</h3>
            
            <div>
                <label for="harga_per_satuan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Harga per Satuan *
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">Rp</span>
                    <input type="number" 
                           id="harga_per_satuan" 
                           name="harga_per_satuan" 
                           value="{{ old('harga_per_satuan', $inventory->harga_per_satuan) }}"
                           step="0.01"
                           min="0"
                           class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                           placeholder="0.00"
                           required>
                </div>
                @error('harga_per_satuan')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Informasi Tambahan -->
        <div>
            <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-4">Informasi Tambahan</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="supplier" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Supplier
                    </label>
                    <input type="text" 
                           id="supplier" 
                           name="supplier" 
                           value="{{ old('supplier', $inventory->supplier) }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                           placeholder="Nama supplier/distributor">
                    @error('supplier')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_kadaluarsa" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tanggal Kadaluarsa
                    </label>
                    <input type="date" 
                           id="tanggal_kadaluarsa" 
                           name="tanggal_kadaluarsa" 
                           value="{{ old('tanggal_kadaluarsa', $inventory->tanggal_kadaluarsa?->format('Y-m-d')) }}"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    @error('tanggal_kadaluarsa')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kosongkan jika tidak ada tanggal kadaluarsa</p>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-4 pt-6">
            <a href="{{ route('admin.inventory.index') }}" 
               class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-3 px-6 rounded-lg font-medium transition-colors text-center">
                Batal
            </a>
            <button type="submit" 
                    class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white py-3 px-6 rounded-lg font-medium transition-colors">
                <i class="fas fa-save mr-2"></i>Update Bahan
            </button>
        </div>
    </form>
</div>
@endsection