@extends('layouts.admin')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="bg-yellow-50 dark:bg-gray-900 min-h-screen p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-yellow-100 dark:bg-gray-800 rounded-lg p-6 mb-6 border border-yellow-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-yellow-900 dark:text-yellow-100 mb-2">🧊 Manajemen Topping</h1>
                    <p class="text-yellow-700 dark:text-yellow-300">Kelola topping untuk menu kopi dan minuman</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-yellow-800 dark:text-yellow-200">{{ $toppings->total() }}</div>
                    <div class="text-sm text-yellow-600 dark:text-yellow-400">Total Toppings</div>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <input type="text" id="searchInput" placeholder="🔍 Cari topping..." 
                       class="px-4 py-2 border border-yellow-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-yellow-900 dark:text-yellow-100">
            </div>
            <a href="{{ route('admin.topping.create') }}" 
               class="bg-yellow-800 hover:bg-yellow-900 text-white px-6 py-2 rounded-lg inline-flex items-center gap-2 transition-colors">
                <i class="fas fa-plus"></i>
                Tambah Topping Baru
            </a>
        </div>

        <!-- Toppings Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="toppingsGrid">
            @forelse($toppings as $topping)
            <div class="topping-card bg-yellow-100 dark:bg-gray-800 rounded-lg shadow-lg border border-yellow-200 dark:border-gray-700 hover:shadow-xl transition-shadow"
                 data-name="{{ strtolower($topping->name) }}">
                <div class="p-6 relative">
                    <!-- Action Buttons -->
                    <div class="absolute top-4 right-4 flex gap-2">
                        <a href="{{ route('admin.topping.edit', $topping->id) }}" 
                           class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-full transition-colors">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="deleteTopping({{ $topping->id }})" 
                                class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    
                    <!-- Topping Icon -->
                    <div class="flex items-center mb-4">
                        <div class="bg-yellow-200 dark:bg-gray-700 p-3 rounded-full mr-4">
                            <i class="fas fa-cookie-bite text-2xl text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-yellow-900 dark:text-yellow-100">{{ $topping->name }}</h3>
                            <p class="text-2xl font-semibold text-yellow-800 dark:text-yellow-200">
                                Rp {{ number_format($topping->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Date -->
                    <div class="text-sm text-yellow-600 dark:text-yellow-400 flex items-center">
                        <i class="fas fa-calendar mr-2"></i>
                        Dibuat: {{ $topping->created_at->format('d M Y') }}
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-cookie-bite text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">Belum Ada Topping</h3>
                <p class="text-gray-500 dark:text-gray-500 mb-4">Mulai dengan menambahkan topping pertama Anda</p>
                <a href="{{ route('admin.topping.create') }}" 
                   class="bg-yellow-800 hover:bg-yellow-900 text-white px-6 py-2 rounded-lg inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Tambah Topping Pertama
                </a>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($toppings->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $toppings->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function deleteTopping(id) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Topping yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batalkan'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/topping/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        'Terhapus!',
                        data.message,
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan saat menghapus topping',
                        'error'
                    );
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire(
                    'Error!',
                    'Terjadi kesalahan saat menghapus topping',
                    'error'
                );
            });
        }
    });
}

// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const toppingCards = document.querySelectorAll('.topping-card');
    
    toppingCards.forEach(card => {
        const toppingName = card.getAttribute('data-name');
        if (toppingName.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});

// Success message
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 3000
    });
@endif
</script>
@endsection
