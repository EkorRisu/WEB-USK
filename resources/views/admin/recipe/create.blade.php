@extends('layouts.admin')

@section('title', 'Tambah Resep Produk')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-amber-800">
            <i class="fas fa-plus mr-2"></i>Tambah Resep Produk
        </h1>
        
        <a href="{{ route('admin.recipe.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-300 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.recipe.store') }}" method="POST" id="recipeForm">
            @csrf
            
            <!-- Produk -->
            <div class="mb-6">
                <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Produk <span class="text-red-500">*</span>
                </label>
                <select name="product_id" id="product_id" required
                        class="text-black bg-white w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('product_id') border-red-500 @enderror"
                        style="color: #000 !important; background-color: #fff !important;">
                    <option value="">Pilih Produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->nama }} - Rp {{ number_format($product->harga, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bahan Baku -->
            <div class="mb-6">
                <label for="inventory_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Bahan Baku <span class="text-red-500">*</span>
                </label>
                <select name="inventory_id" id="inventory_id" required
                        class="text-black bg-white w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('inventory_id') border-red-500 @enderror"
                        style="color: #000 !important; background-color: #fff !important;">
                    <option value="">Pilih Bahan Baku</option>
                    @foreach($inventories->groupBy('kategori_bahan') as $kategori => $items)
                        <optgroup label="{{ ucfirst($kategori) }}">
                            @foreach($items as $inventory)
                                <option value="{{ $inventory->id }}" 
                                        data-stock="{{ $inventory->stok_tersedia }}"
                                        data-unit="{{ $inventory->satuan }}"
                                        {{ old('inventory_id') == $inventory->id ? 'selected' : '' }}>
                                    {{ $inventory->nama_bahan }} 
                                    (Stok: {{ number_format($inventory->stok_tersedia, 2) }} {{ $inventory->satuan }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('inventory_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Jumlah Kebutuhan -->
                <div>
                    <label for="quantity_needed" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah Kebutuhan <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="quantity_needed" id="quantity_needed" 
                           step="0.01" min="0.01" required
                           value="{{ old('quantity_needed') }}"
                           class="text-black bg-white w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('quantity_needed') border-red-500 @enderror"
                           placeholder="Contoh: 10.5"
                           style="color: #000 !important; background-color: #fff !important;">
                    @error('quantity_needed')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-1">Jumlah bahan yang dibutuhkan untuk 1 unit produk</p>
                </div>

                <!-- Satuan -->
                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">
                        Satuan <span class="text-red-500">*</span>
                    </label>
                    <select name="unit" id="unit" required
                            class="text-black bg-white w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('unit') border-red-500 @enderror"
                            style="color: #000 !important; background-color: #fff !important;">
                        <option value="">Pilih Satuan</option>
                        <option value="gram" {{ old('unit') == 'gram' ? 'selected' : '' }}>Gram</option>
                        <option value="ml" {{ old('unit') == 'ml' ? 'selected' : '' }}>Mililiter (ml)</option>
                        <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                        <option value="sendok" {{ old('unit') == 'sendok' ? 'selected' : '' }}>Sendok</option>
                        <option value="cup" {{ old('unit') == 'cup' ? 'selected' : '' }}>Cup</option>
                        <option value="shot" {{ old('unit') == 'shot' ? 'selected' : '' }}>Shot</option>
                        <option value="lainnya" {{ old('unit') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('unit')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Catatan -->
            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan
                </label>
                <textarea name="notes" id="notes" rows="3" 
                          class="text-black bg-white w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('notes') border-red-500 @enderror"
                          placeholder="Catatan tambahan tentang penggunaan bahan ini..."
                          style="color: #000 !important; background-color: #fff !important;">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Preview Perhitungan -->
            <div id="productionPreview" class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg hidden">
                <h3 class="font-medium text-amber-800 mb-2">Preview Perhitungan Produksi</h3>
                <div class="text-sm text-amber-700">
                    <p>Dengan stok bahan saat ini: <span id="currentStock">-</span></p>
                    <p>Kebutuhan per produk: <span id="needPerProduct">-</span></p>
                    <p class="font-bold text-lg mt-2">Maksimal produksi: <span id="maxProduction">-</span> unit produk</p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('admin.recipe.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition duration-300">
                    Batal
                </a>
                <button type="submit" 
                        class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2 rounded-lg transition duration-300 flex items-center">
                    <i class="fas fa-save mr-2"></i>Simpan Resep
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inventorySelect = document.getElementById('inventory_id');
    const quantityInput = document.getElementById('quantity_needed');
    const unitSelect = document.getElementById('unit');
    const preview = document.getElementById('productionPreview');
    
    // Ensure number input works properly
    quantityInput.addEventListener('focus', function() {
        this.style.color = '#000';
        this.style.backgroundColor = '#fff';
    });
    
    // Allow decimal input
    quantityInput.addEventListener('keypress', function(e) {
        // Allow: backspace, delete, tab, escape, enter
        if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
            // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            (e.keyCode === 65 && e.ctrlKey === true) ||
            (e.keyCode === 67 && e.ctrlKey === true) ||
            (e.keyCode === 86 && e.ctrlKey === true) ||
            (e.keyCode === 88 && e.ctrlKey === true) ||
            // Allow: decimal point
            (e.keyCode === 190) ||
            (e.keyCode === 110)) {
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    function updatePreview() {
        const selectedInventory = inventorySelect.options[inventorySelect.selectedIndex];
        const quantity = parseFloat(quantityInput.value) || 0;
        
        if (selectedInventory.value && quantity > 0) {
            const stock = parseFloat(selectedInventory.dataset.stock) || 0;
            const inventoryUnit = selectedInventory.dataset.unit;
            const recipeUnit = unitSelect.value;
            
            // Konversi stok inventory ke satuan resep
            const convertedStock = convertUnit(stock, inventoryUnit, recipeUnit);
            let maxProduction = 0;
            
            if (convertedStock !== null) {
                maxProduction = Math.floor(convertedStock / quantity);
            }
            
            document.getElementById('currentStock').textContent = stock.toFixed(2) + ' ' + inventoryUnit;
            document.getElementById('needPerProduct').textContent = quantity.toFixed(2) + ' ' + (recipeUnit || inventoryUnit);
            document.getElementById('maxProduction').textContent = maxProduction;
            
            // Tambahkan info konversi jika berbeda satuan
            if (inventoryUnit !== recipeUnit && convertedStock !== null) {
                document.getElementById('currentStock').textContent += ` (${convertedStock.toFixed(2)} ${recipeUnit})`;
            }
            
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    }

    // Helper function untuk konversi satuan (sama dengan PHP)
    function convertUnit(value, fromUnit, toUnit) {
        if (fromUnit === toUnit) {
            return value;
        }

        // Konversi berat
        const weightConversions = {
            'kg': { 'gram': 1000 },
            'gram': { 'kg': 0.001 }
        };

        // Konversi volume
        const volumeConversions = {
            'liter': { 'ml': 1000 },
            'ml': { 'liter': 0.001 }
        };

        // Cek konversi berat
        if (weightConversions[fromUnit] && weightConversions[fromUnit][toUnit]) {
            return value * weightConversions[fromUnit][toUnit];
        }

        // Cek konversi volume
        if (volumeConversions[fromUnit] && volumeConversions[fromUnit][toUnit]) {
            return value * volumeConversions[fromUnit][toUnit];
        }

        // Jika tidak ada konversi yang cocok, return null
        return null;
    }

    // Auto-fill unit berdasarkan inventory yang dipilih
    inventorySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value && selectedOption.dataset.unit) {
            const inventoryUnit = selectedOption.dataset.unit;
            
            // Map unit inventory ke unit resep yang umum digunakan
            const unitMapping = {
                'kg': 'gram',        // kg inventory -> gram untuk resep (lebih presisi)
                'liter': 'ml',       // liter inventory -> ml untuk resep (lebih presisi)
                'gram': 'gram',      // gram tetap gram
                'ml': 'ml',          // ml tetap ml
                'pcs': 'pcs'         // pieces tetap pieces
            };
            
            const mappedUnit = unitMapping[inventoryUnit] || inventoryUnit;
            
            // Set unit jika ada mapping
            for (let option of unitSelect.options) {
                if (option.value === mappedUnit) {
                    unitSelect.value = mappedUnit;
                    break;
                }
            }
        }
        updatePreview();
    });

    quantityInput.addEventListener('input', updatePreview);
    unitSelect.addEventListener('change', updatePreview);
});
</script>
@endpush
@endsection