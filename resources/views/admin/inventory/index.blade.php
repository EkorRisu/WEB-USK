@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-yellow-900 dark:text-yellow-100">📦 Manajemen Stok Bahan</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola inventori bahan baku coffee shop</p>
        </div>
        <a href="{{ route('admin.inventory.create') }}" 
           class="bg-yellow-800 hover:bg-yellow-900 text-white px-6 py-2 rounded-lg inline-flex items-center gap-2 transition-colors">
            <i class="fas fa-plus"></i>
            Tambah Bahan
        </a>
    </div>

    <!-- Alert untuk bahan yang menipis -->
    @php
        $lowStockItems = App\Models\Inventory::menipis()->get();
    @endphp
    @if($lowStockItems->count() > 0)
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg dark:bg-red-900 dark:text-red-200">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <div>
                <h4 class="font-bold">Perhatian! {{ $lowStockItems->count() }} bahan stoknya menipis:</h4>
                <ul class="list-disc list-inside mt-1 text-sm">
                    @foreach($lowStockItems->take(3) as $item)
                        <li>{{ $item->nama_bahan }} ({{ $item->stok_tersedia }} {{ $item->satuan }})</li>
                    @endforeach
                    @if($lowStockItems->count() > 3)
                        <li>dan {{ $lowStockItems->count() - 3 }} bahan lainnya...</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Filter dan Search -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama bahan..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-gray-200">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                <select name="kategori" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-gray-200">
                    <option value="">Semua Kategori</option>
                    <option value="biji_kopi" {{ request('kategori') === 'biji_kopi' ? 'selected' : '' }}>Biji Kopi</option>
                    <option value="susu" {{ request('kategori') === 'susu' ? 'selected' : '' }}>Susu</option>
                    <option value="sirup" {{ request('kategori') === 'sirup' ? 'selected' : '' }}>Sirup</option>
                    <option value="topping" {{ request('kategori') === 'topping' ? 'selected' : '' }}>Topping</option>
                    <option value="kemasan" {{ request('kategori') === 'kemasan' ? 'selected' : '' }}>Kemasan</option>
                    <option value="lainnya" {{ request('kategori') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-gray-200">
                    <option value="">Semua Status</option>
                    <option value="tersedia" {{ request('status') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="menipis" {{ request('status') === 'menipis' ? 'selected' : '' }}>Menipis</option>
                    <option value="habis" {{ request('status') === 'habis' ? 'selected' : '' }}>Habis</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Inventory Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="inventoryGrid">
        @forelse($inventories as $inventory)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow">
            <div class="p-6">
                <!-- Status Badge -->
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center">
                        @switch($inventory->kategori_bahan)
                            @case('biji_kopi')
                                <span class="text-2xl mr-2">☕</span>
                                @break
                            @case('susu')
                                <span class="text-2xl mr-2">🥛</span>
                                @break
                            @case('sirup')
                                <span class="text-2xl mr-2">🍯</span>
                                @break
                            @case('topping')
                                <span class="text-2xl mr-2">🧊</span>
                                @break
                            @case('kemasan')
                                <span class="text-2xl mr-2">📦</span>
                                @break
                            @default
                                <span class="text-2xl mr-2">📋</span>
                        @endswitch
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $inventory->nama_bahan }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 capitalize">{{ str_replace('_', ' ', $inventory->kategori_bahan) }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        @if($inventory->status === 'tersedia') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200
                        @elseif($inventory->status === 'menipis') bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200
                        @else bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200 @endif">
                        {{ ucfirst($inventory->status) }}
                    </span>
                </div>

                <!-- Stock Info -->
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Stok Tersedia</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            {{ $inventory->stok_tersedia }} {{ $inventory->satuan }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        @php
                            $percentage = min(100, ($inventory->stok_tersedia / max($inventory->stok_minimum * 2, 1)) * 100);
                        @endphp
                        <div class="h-2 rounded-full transition-all duration-300
                            @if($percentage > 50) bg-green-500
                            @elseif($percentage > 25) bg-yellow-500
                            @else bg-red-500 @endif" 
                            style="width: {{ $percentage }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <span>Min: {{ $inventory->stok_minimum }} {{ $inventory->satuan }}</span>
                        <span>{{ $inventory->formatted_harga }} per {{ $inventory->satuan }}</span>
                    </div>
                </div>

                @if($inventory->deskripsi)
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ Str::limit($inventory->deskripsi, 80) }}</p>
                @endif

                <!-- Supplier and expiry -->
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                    @if($inventory->supplier)
                        <div class="flex items-center mb-1">
                            <i class="fas fa-truck mr-1"></i>
                            {{ $inventory->supplier }}
                        </div>
                    @endif
                    @if($inventory->tanggal_kadaluarsa)
                        <div class="flex items-center">
                            <i class="fas fa-calendar mr-1"></i>
                            Exp: {{ $inventory->tanggal_kadaluarsa->format('d M Y') }}
                        </div>
                    @endif
                </div>

                <!-- Action buttons -->
                <div class="flex gap-2">
                    <a href="{{ route('admin.inventory.edit', $inventory->id) }}" 
                       class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-center transition-colors text-sm">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    <button onclick="adjustStock({{ $inventory->id }})" 
                            class="flex-1 bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-lg transition-colors text-sm">
                        <i class="fas fa-plus-minus mr-1"></i>Adjust
                    </button>
                    <button onclick="deleteInventory({{ $inventory->id }})" 
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg transition-colors text-sm">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <i class="fas fa-box-open text-6xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">Belum Ada Data Stok</h3>
            <p class="text-gray-500 dark:text-gray-500 mb-4">Mulai dengan menambahkan bahan pertama Anda</p>
            <a href="{{ route('admin.inventory.create') }}" 
               class="bg-yellow-800 hover:bg-yellow-900 text-white px-6 py-2 rounded-lg inline-flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Tambah Bahan Pertama
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($inventories->hasPages())
    <div class="mt-8">
        {{ $inventories->withQueryString()->links() }}
    </div>
    @endif
</div>

<!-- Modals untuk Adjust Stock -->
<div id="adjustStockModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-md w-full">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Adjust Stok Bahan</h3>
            <form id="adjustStockForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipe Adjustment</label>
                    <select id="adjustmentType" name="type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-gray-200">
                        <option value="add">Tambah Stok</option>
                        <option value="subtract">Kurangi Stok</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jumlah</label>
                    <input type="number" id="adjustmentAmount" name="adjustment" step="0.01" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-gray-200" 
                           required>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Keterangan (opsional)</label>
                    <textarea id="adjustmentNote" name="keterangan" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-gray-200"
                              placeholder="Contoh: Restock dari supplier, pemakaian produksi, dll"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeAdjustModal()" 
                            class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentInventoryId = null;

function adjustStock(inventoryId) {
    currentInventoryId = inventoryId;
    document.getElementById('adjustStockModal').classList.remove('hidden');
    document.getElementById('adjustmentAmount').focus();
}

function closeAdjustModal() {
    document.getElementById('adjustStockModal').classList.add('hidden');
    document.getElementById('adjustStockForm').reset();
    currentInventoryId = null;
}

document.getElementById('adjustStockForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/admin/inventory/${currentInventoryId}/adjust`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Berhasil!',
                text: data.message,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        }
        closeAdjustModal();
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
    });
});

function deleteInventory(inventoryId) {
    Swal.fire({
        title: 'Hapus Bahan?',
        text: "Data ini akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/inventory/${inventoryId}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Close modal when clicking outside
document.getElementById('adjustStockModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAdjustModal();
    }
});
</script>
@endsection