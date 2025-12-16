@extends('layouts.admin')

@section('content')
<div class="bg-yellow-50 dark:bg-gray-900 min-h-screen p-6">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="bg-yellow-100 dark:bg-gray-800 rounded-lg p-6 mb-6 border border-yellow-200 dark:border-gray-700">
            <h1 class="text-3xl font-bold text-yellow-900 dark:text-yellow-100 mb-2">🆕 Tambah Topping Baru</h1>
            <p class="text-yellow-700 dark:text-yellow-300">Tambahkan topping baru untuk menu kopi dan minuman</p>
        </div>

        <!-- Form -->
        <div class="bg-yellow-100 dark:bg-gray-800 rounded-lg p-6 border border-yellow-200 dark:border-gray-700">
            <form action="{{ route('admin.topping.store') }}" method="POST">
                @csrf
                
                <!-- Nama Topping -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-semibold text-yellow-900 dark:text-yellow-100 mb-2">
                        Nama Topping *
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full px-4 py-3 border border-yellow-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-yellow-900 dark:text-yellow-100 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                           placeholder="Contoh: Extra Shot, Whipped Cream"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga -->
                <div class="mb-6">
                    <label for="price" class="block text-sm font-semibold text-yellow-900 dark:text-yellow-100 mb-2">
                        Harga (Rp) *
                    </label>
                    <input type="number" 
                           id="price" 
                           name="price" 
                           value="{{ old('price') }}"
                           min="0"
                           step="500"
                           class="w-full px-4 py-3 border border-yellow-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-yellow-900 dark:text-yellow-100 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                           placeholder="5000"
                           required>
                    @error('price')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>



                <!-- Action Buttons -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('admin.topping.index') }}" 
                       class="px-6 py-2 border border-yellow-300 dark:border-gray-600 text-yellow-700 dark:text-yellow-300 rounded-lg hover:bg-yellow-50 dark:hover:bg-gray-700 transition-colors">
                        ← Kembali
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-yellow-800 hover:bg-yellow-900 text-white rounded-lg transition-colors">
                        💾 Simpan Topping
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
