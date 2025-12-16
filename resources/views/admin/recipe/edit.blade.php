@extends('layouts.admin')

@section('title', 'Edit Resep Produk')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-amber-800">
            <i class="fas fa-edit mr-2"></i>Edit Resep Produk
        </h1>
        
        <a href="{{ route('admin.recipe.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-300 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <!-- Current Recipe Info -->
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
        <h3 class="font-medium text-amber-800 mb-2">Resep Saat Ini</h3>
        <p class="text-amber-700">
            <strong>{{ $recipe->product->nama }}</strong> membutuhkan 
            <strong>{{ $recipe->formatted_quantity }}</strong> dari 
            <strong>{{ $recipe->inventory->nama_bahan }}</strong>
        </p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.recipe.update', $recipe->id) }}" method="POST" id="recipeForm">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Produk -->
                <div>
                    <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Produk <span class="text-red-500">*</span>
                    </label>
                    <select name="product_id" id="product_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 @error('product_id') border-red-500 @enderror">
                        <option value="">Pilih Produk</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                    {{ (old('product_id', $recipe->product_id) == $product->id) ? 'selected' : '' }}>
                                {{ $product->nama }} - Rp {{ number_format($product->harga, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bahan Baku -->
                <div>
                    <label for="inventory_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Bahan Baku <span class="text-red-500">*</span>
                    </label>
                    <select name="inventory_id" id="inventory_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 @error('inventory_id') border-red-500 @enderror">
                        <option value="">Pilih Bahan Baku</option>
                        @foreach($inventories->groupBy('kategori_bahan') as $kategori => $items)
                            <optgroup label="{{ ucfirst($kategori) }}">
                                @foreach($items as $inventory)
                                    <option value="{{ $inventory->id }}" 
                                            data-stock="{{ $inventory->stok_tersedia }}"
                                            data-unit="{{ $inventory->satuan }}"
                                            {{ (old('inventory_id', $recipe->inventory_id) == $inventory->id) ? 'selected' : '' }}>
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
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Jumlah Kebutuhan -->
                <div>
                    <label for="quantity_needed" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah Kebutuhan <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="quantity_needed" id="quantity_needed" 
                           step="0.01" min="0.01" required
                           value="{{ old('quantity_needed', $recipe->quantity_needed) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 @error('quantity_needed') border-red-500 @enderror"
                           placeholder="Contoh: 10.5">
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
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 @error('unit') border-red-500 @enderror">
                        <option value="">Pilih Satuan</option>
                        <option value="gram" {{ old('unit', $recipe->unit) == 'gram' ? 'selected' : '' }}>Gram</option>
                        <option value="ml" {{ old('unit', $recipe->unit) == 'ml' ? 'selected' : '' }}>Mililiter (ml)</option>
                        <option value="pcs" {{ old('unit', $recipe->unit) == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                        <option value="sendok" {{ old('unit', $recipe->unit) == 'sendok' ? 'selected' : '' }}>Sendok</option>
                        <option value="cup" {{ old('unit', $recipe->unit) == 'cup' ? 'selected' : '' }}>Cup</option>
                        <option value="shot" {{ old('unit', $recipe->unit) == 'shot' ? 'selected' : '' }}>Shot</option>
                        <option value="lainnya" {{ old('unit', $recipe->unit) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
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
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 @error('notes') border-red-500 @enderror"
                          placeholder="Catatan tambahan tentang penggunaan bahan ini...">{{ old('notes', $recipe->notes) }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Preview Perhitungan -->
            <div id="productionPreview" class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                <h3 class="font-medium text-amber-800 mb-2">Preview Perhitungan Produksi</h3>
                <div class="text-sm text-amber-700">
                    <p>Dengan stok bahan saat ini: <span id="currentStock">{{ number_format($recipe->inventory->stok_tersedia, 2) }} {{ $recipe->inventory->satuan }}</span></p>
                    <p>Kebutuhan per produk: <span id="needPerProduct">{{ $recipe->formatted_quantity }}</span></p>
                    <p class="font-bold text-lg mt-2">Maksimal produksi: <span id="maxProduction">{{ $recipe->max_production }}</span> unit produk</p>
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
                    <i class="fas fa-save mr-2"></i>Update Resep
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

    function updatePreview() {
        const selectedInventory = inventorySelect.options[inventorySelect.selectedIndex];
        const quantity = parseFloat(quantityInput.value) || 0;
        
        if (selectedInventory.value && quantity > 0) {
            const stock = parseFloat(selectedInventory.dataset.stock) || 0;
            const unit = selectedInventory.dataset.unit;
            const selectedUnit = unitSelect.value;
            
            const maxProduction = Math.floor(stock / quantity);
            
            document.getElementById('currentStock').textContent = stock.toFixed(2) + ' ' + unit;
            document.getElementById('needPerProduct').textContent = quantity.toFixed(2) + ' ' + (selectedUnit || unit);
            document.getElementById('maxProduction').textContent = maxProduction;
            
            preview.classList.remove('hidden');
        }
    }

    // Auto-fill unit berdasarkan inventory yang dipilih
    inventorySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value && selectedOption.dataset.unit) {
            const inventoryUnit = selectedOption.dataset.unit;
            
            // Map unit inventory ke unit resep
            const unitMapping = {
                'kg': 'gram',
                'liter': 'ml',
                'pcs': 'pcs'
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
    
    // Initial load
    updatePreview();
});
</script>
@endpush
@endsection