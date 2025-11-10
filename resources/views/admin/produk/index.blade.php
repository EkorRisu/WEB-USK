@extends('layouts.admin')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="page-container" id="pageContainer">
    <!-- Header Section -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-info">
                <h1 class="page-title">
                    <span class="title-icon">🛒</span>
                    Product Management
                </h1>
            </div>
            <div class="header-stats">
                <div class="stat-card">
                    <div class="stat-number">{{ $produks->count() }}</div>
                    <div class="stat-label">Total Products</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <div class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">
                <span class="breadcrumb-icon">🏠</span>
                Dashboard
            </a>
            <span class="breadcrumb-separator">→</span>
            <span class="breadcrumb-item active">Products</span>
        </div>

        <div class="action-buttons">
            <a href="{{ route('admin.produk.create') }}" class="btn btn-primary">
                <span class="btn-icon">➕</span>
                Add New Product
            </a>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="filter-section">
        <div class="search-wrapper">
            <div class="search-input-wrapper">
                <input type="text" id="searchInput" class="search-input" placeholder="Search products...">
                <div class="search-icon">🔍</div>
            </div>
        </div>
        <div class="filter-wrapper">
            <select id="categoryFilter" class="filter-select">
                <option value="">All Categories</option>
                @foreach($produks->unique('kategori.nama')->pluck('kategori.nama')->filter() as $category)
                <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
            </select>
            
            <select id="sortBy" class="filter-select">
                <option value="name">Sort by Name</option>
                <option value="price">Sort by Price</option>
                <option value="category">Sort by Category</option>
            </select>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="products-container">
        @forelse ($produks as $index => $produk)
        <div class="product-card" data-name="{{ strtolower($produk->nama) }}"
            data-category="{{ $produk->kategori->nama ?? '' }}" data-price="{{ $produk->harga }}">
            <!-- Product Image -->
            <div class="product-image-wrapper">
                @if ($produk->foto)
                <img src="{{ asset('storage/' . $produk->foto) }}" alt="{{ $produk->nama }}" class="product-image">
                @else
                <div class="product-image-placeholder">
                    <span class="placeholder-icon">📦</span>
                    <span class="placeholder-text">No Image</span>
                </div>
                @endif
                <div class="product-overlay">
                    <div class="product-actions">
                        <a href="{{ route('admin.produk.edit', $produk->id) }}" class="action-btn edit-btn"
                            title="Edit Product">
                            <span>✏️</span>
                        </a>
                        <button type="button" class="action-btn delete-btn" onclick="confirmDelete({{ $produk->id }})"
                            title="Delete Product">
                            <span>🗑️</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="product-info">
                <h3 class="product-name">{{ $produk->nama }}</h3>
                <div class="product-category">
                    <span class="category-icon">🏷️</span>
                    {{ $produk->kategori->nama ?? 'Uncategorized' }}
                </div>
                <div class="product-price">
                    <span class="price-currency">Rp</span>
                    <span class="price-amount">{{ number_format($produk->harga, 0, ',', '.') }}</span>
                </div>
                <div class="product-number">
                    <span class="number-label">#</span>
                    <span class="number-value">{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="product-stock" style="color: white">
                    <span class="number-label">Stock:</span>
                    <span class="number-value">{{ $produk->stok }}</span>
                </div>
            </div>

            <!-- Hidden Delete Form -->
            <form id="delete-form-{{ $produk->id }}" action="{{ route('admin.produk.destroy', $produk->id) }}"
                method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon">📦</div>
            <h3 class="empty-title">No Products Found</h3>
            <p class="empty-description">Start by adding your first product to the inventory</p>
            <a href="{{ route('admin.produk.create') }}" class="btn btn-primary">
                <span class="btn-icon">➕</span>
                Add First Product
            </a>
        </div>
        @endforelse
    </div>


    @if (session('error'))
    <script>
        Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '{{ session('error') }}',
        showConfirmButton: false,
        timer: 3000,
        toast: true,
        position: 'top-end'
    });
    </script>
    @endif

    @if (session('success'))
    <script>
        Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    position: 'top-end'
                });
    </script>
    @endif

    <script>
        function confirmDelete(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This product will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    backdrop: `rgba(0,0,0,0.8)`,
                    customClass: {
                        popup: 'swal-popup',
                        confirmButton: 'swal-confirm',
                        cancelButton: 'swal-cancel'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                })
            }

            // Search and Filter Functionality
            document.addEventListener('DOMContentLoaded', function() {
                const pageContainer = document.getElementById('pageContainer');
                
                // Sync with navbar theme
                const syncTheme = () => {
                    const isDark = document.documentElement.classList.contains('dark');
                    pageContainer.setAttribute('data-theme', isDark ? 'dark' : 'light');
                };

                // Initial sync
                syncTheme();

                // Listen for theme changes from navbar
                const observer = new MutationObserver(syncTheme);
                observer.observe(document.documentElement, { 
                    attributes: true, 
                    attributeFilter: ['class'] 
                });

                const searchInput = document.getElementById('searchInput');
                const categoryFilter = document.getElementById('categoryFilter');
                const sortBy = document.getElementById('sortBy');
                const productCards = document.querySelectorAll('.product-card');
                const productsContainer = document.querySelector('.products-container');

                function filterAndSearch() {
                    const searchTerm = searchInput.value.toLowerCase();
                    const selectedCategory = categoryFilter.value;

                    let visibleCards = [];

                    productCards.forEach(card => {
                        const name = card.dataset.name;
                        const category = card.dataset.category;

                        const matchesSearch = name.includes(searchTerm);
                        const matchesCategory = !selectedCategory || category === selectedCategory;

                        if (matchesSearch && matchesCategory) {
                            card.style.display = 'block';
                            visibleCards.push(card);
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    // Show empty state if no results
                    if (visibleCards.length === 0 && productCards.length > 0) {
                        if (!document.querySelector('.no-results')) {
                            const noResults = document.createElement('div');
                            noResults.className = 'no-results empty-state';
                            noResults.innerHTML = `
                                <div class="empty-icon">🔍</div>
                                <h3 class="empty-title">No Products Found</h3>
                                <p class="empty-description">Try adjusting your search or filters</p>
                            `;
                            productsContainer.appendChild(noResults);
                        }
                    } else {
                        const noResults = document.querySelector('.no-results');
                        if (noResults) {
                            noResults.remove();
                        }
                    }
                }

                // FITUR SORTING - TIDAK ADA DI REQUIREMENTS
                // function sortProducts() {
                //     const sortValue = sortBy.value;
                //     const cardsArray = Array.from(productCards);

                //     cardsArray.sort((a, b) => {
                //         switch(sortValue) {
                //             case 'name':
                //                 return a.dataset.name.localeCompare(b.dataset.name);
                //             case 'price':
                //                 return parseInt(a.dataset.price) - parseInt(b.dataset.price);
                //             case 'category':
                //                 return a.dataset.category.localeCompare(b.dataset.category);
                //             default:
                //                 return 0;
                //         }
                //     });

                //     cardsArray.forEach(card => {
                //         productsContainer.appendChild(card);
                //     });
                // }

                searchInput.addEventListener('input', filterAndSearch);
                categoryFilter.addEventListener('change', filterAndSearch);
                // sortBy.addEventListener('change', sortProducts);

                // Animate cards on load
                productCards.forEach((card, index) => {
                    card.style.animationDelay = `${index * 0.1}s`;
                });
            });
    </script>

    <style>
        /* Root Variables for Theming */
        :root {
            --bg-gradient-start: #18181b;
            --bg-gradient-end: #27272a;
            --card-bg: rgba(30, 30, 40, 0.85);
            --header-bg: #1e1e28d9;
            --text-primary: #f3f4f6;
            --text-secondary: #fff;
            --text-hint: #9ca3af;
            --border-color: #e5e7eb;
            --input-bg: white;
            --breadcrumb-bg: rgba(255, 255, 255, 0.1);
            --breadcrumb-text: rgba(255, 255, 255, 0.8);
            --stat-bg: rgba(40, 40, 50, 0.8);
            --stat-text: #ffff;
        }

        .page-container[data-theme="light"] {
            --bg-gradient-start: #f0f4ff;
            --bg-gradient-end: #e0e7ff;
            --card-bg: rgba(255, 255, 255, 0.95);
            --header-bg: rgba(255, 255, 255, 0.95);
            --text-primary: #1e293b;
            --text-secondary: #475569;
            --text-hint: #64748b;
            --border-color: #cbd5e1;
            --input-bg: #ffffff;
            --breadcrumb-bg: rgba(99, 102, 241, 0.1);
            --breadcrumb-text: #475569;
            --stat-bg: rgba(99, 102, 241, 0.1);
            --stat-text: #1e293b;
        }

        .page-container {
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
            min-height: 100vh;
            padding: 2rem;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            transition: background 0.5s ease;
        }

        /* Header Section */
        .page-header {
            background: var(--header-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: background 0.3s ease;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: color 0.3s ease;
        }

        .page-container[data-theme="light"] .page-title {
            background: linear-gradient(90deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .title-icon {
            font-size: 2rem;
        }

        .page-subtitle {
            color: #64748b;
            font-size: 1.1rem;
            margin: 0.5rem 0 0 0;
        }

        .header-stats {
            display: flex;
            gap: 1rem;
        }

        .stat-card {
            background: var(--stat-bg);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            text-align: center;
            min-width: 100px;
            transition: background 0.3s ease;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--stat-text);
            transition: color 0.3s ease;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--stat-text);
            font-weight: 500;
            transition: color 0.3s ease;
        }

        /* Action Bar */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .breadcrumb-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .breadcrumb-item {
            color: var(--breadcrumb-text);
            padding: 0.5rem 1rem;
            background: var(--breadcrumb-bg);
            border-radius: 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .breadcrumb-item:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
        }

        .page-container[data-theme="light"] .breadcrumb-item:hover {
            background: rgba(99, 102, 241, 0.2);
            color: #4c1d95;
        }

        .breadcrumb-item.active {
            background: rgba(255, 255, 255, 0.9);
            color: #374151;
        }

        .page-container[data-theme="light"] .breadcrumb-item.active {
            background: #667eea;
            color: white;
        }

        .breadcrumb-separator {
            color: rgba(255, 255, 255, 0.6);
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
        }

        /* Filter Section */
        .filter-section {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
            transition: background 0.3s ease;
        }

        .search-wrapper {
            flex: 1;
            max-width: 400px;
        }

        .search-input-wrapper {
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 3rem 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--input-bg);
            color: var(--text-primary);
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .filter-wrapper {
            display: flex;
            gap: 1rem;
        }

        .filter-select {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            background: var(--input-bg);
            color: var(--text-primary);
            font-size: 0.9rem;
            min-width: 150px;
            transition: all 0.3s ease;
        }

        .filter-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        /* Products Grid */
        .products-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .product-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            animation: slideInUp 0.6s ease-out;
            animation-fill-mode: both;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .product-image-wrapper {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-image-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
        }

        .placeholder-icon {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }

        .placeholder-text {
            font-size: 0.875rem;
            font-weight: 500;
        }

        .product-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-card:hover .product-overlay {
            opacity: 1;
        }

        .product-actions {
            display: flex;
            gap: 1rem;
        }

        .action-btn {
            width: 50px;
            height: 50px;
            border: none;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            color: #374151;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .action-btn:hover {
            background: white;
            transform: scale(1.1);
        }

        .edit-btn:hover {
            color: #10b981;
        }

        .delete-btn:hover {
            color: #ef4444;
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 0.75rem 0;
            line-height: 1.3;
            transition: color 0.3s ease;
        }

        .product-category {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-bottom: 1rem;
            transition: color 0.3s ease;
        }

        .category-icon {
            font-size: 1rem;
        }

        .product-price {
            display: flex;
            align-items: baseline;
            gap: 0.25rem;
            margin-bottom: 1rem;
        }

        .price-currency {
            font-size: 1rem;
            color: #fff;
            font-weight: 500;
        }

        .price-amount {
            font-size: 1.5rem;
            font-weight: 800;
            color: #10b981;
        }

        .product-number {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.875rem;
            color: #9ca3af;
        }

        .number-label {
            font-weight: 500;
        }

        .number-value {
            font-family: monospace;
            font-weight: 600;
        }

        /* Empty State */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 2rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .empty-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .empty-description {
            color: #6b7280;
            margin-bottom: 2rem;
        }


        /* Buttons */
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #171717, #404040);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #474747, #353436);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(163, 163, 163, 0.3);
        }

        .btn-icon {
            font-size: 1rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-container {
                padding: 1rem;
            }

            .header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .page-title {
                font-size: 2rem;
            }

            .action-bar {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .filter-section {
                flex-direction: column;
                gap: 1rem;
            }

            .filter-wrapper {
                flex-direction: column;
            }

            .products-container {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .stats-section {
                grid-template-columns: 1fr;
            }
        }

        /* Animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* SweetAlert Custom Styles */
        .swal-popup {
            border-radius: 16px;
        }

        .swal-confirm {
            border-radius: 8px;
        }

        .swal-cancel {
            border-radius: 8px;
        }
    </style>
    @endsection
