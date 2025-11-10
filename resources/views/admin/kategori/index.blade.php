@extends('layouts.admin')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="category-management dark:text-gray-100">
    <!-- Header Section -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="header-info">
                <h1 class="page-title">
                    <span class="title-icon">📚</span>
                    Category Management
                </h1>
                <p class="page-subtitle">Manage book categories and organize your inventory efficiently</p>
            </div>
            <div class="header-stats">
                <div class="stat-card">
                    <div class="stat-value">{{ $kategoris->count() }}</div>
                    <div class="stat-label">Total Categories</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $kategoris->where('foto', '!=', null)->count() }}</div>
                    <div class="stat-label">With Images</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <div class="left-actions">
            <a href="{{ route('admin.dashboard') }}" class="action-btn btn-back">
                <span class="btn-icon">←</span>
                Back to Dashboard
            </a>
        </div>
        <div class="right-actions">
            <div class="search-box">
                <input type="text" placeholder="Search categories..." class="search-input" id="searchInput">
                <button class="search-btn">🔍</button>
            </div>
            <a href="{{ route('admin.kategori.create') }}" class="action-btn btn-add">
                <span class="btn-icon">+</span>
                Add Category
            </a>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="categories-container">
        @forelse ($kategoris as $index => $category)
        <div class="category-card" data-category="{{ strtolower($category->nama) }}">
            <div class="card-header">
                <div class="category-number">#{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="card-content">
                <h3 class="category-name">{{ $category->nama }}</h3>
                <p class="category-description">
                    {{ $category->deskripsi ? Str::limit($category->deskripsi, 80) : 'No description available' }}
                </p>
            </div>

            <div class="card-actions">
                <a href="{{ route('admin.kategori.edit', $category->id) }}" class="action-btn-small btn-edit">
                    <span class="btn-icon">✏️</span>
                    Edit
                </a>
                <form id="delete-form-{{ $category->id }}" action="{{ route('admin.kategori.destroy', $category->id) }}"
                    method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="action-btn-small btn-delete"
                        onclick="confirmDelete({{ $category->id }})">
                        <span class="btn-icon">🗑️</span>
                        Delete
                    </button>
                </form>
            </div>

            <div class="card-footer">
                <div class="created-date">
                    Created: {{ \Carbon\Carbon::parse($category->created_at)->format('M d, Y') }}
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon">📚</div>
            <h3>No Categories Found</h3>
            <p>Start by creating your first book category to organize your inventory.</p>
            <a href="{{ route('admin.kategori.create') }}" class="empty-action-btn">
                <span class="btn-icon">+</span>
                Create First Category
            </a>
        </div>
        @endforelse
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="image-modal">
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <img id="modalImage" src="" alt="">
        <div class="modal-caption"></div>
    </div>
</div>

@if (session('success'))
<script>
    Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000,
                    toast: true,
                    position: 'top-end'
                });
</script>
@endif

@if (session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Oops!',
        text: '{{ session('error') }}',
        confirmButtonColor: '#ef4444',
    });
</script>
@endif


@push('scripts')
<script>
    // Search functionality
                document.getElementById('searchInput').addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const categoryCards = document.querySelectorAll('.category-card');

                    categoryCards.forEach(card => {
                        const categoryName = card.dataset.category;
                        const categoryText = card.textContent.toLowerCase();

                        if (categoryName.includes(searchTerm) || categoryText.includes(searchTerm)) {
                            card.style.display = 'block';
                            card.style.animation = 'slideInUp 0.3s ease-out';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });

                // Menu dropdown functionality
                document.querySelectorAll('.menu-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const dropdown = this.nextElementSibling;

                        // Close all other dropdowns
                        document.querySelectorAll('.menu-dropdown').forEach(d => {
                            if (d !== dropdown) d.classList.remove('show');
                        });

                        dropdown.classList.toggle('show');
                    });
                });

                // Close dropdowns when clicking outside
                document.addEventListener('click', function() {
                    document.querySelectorAll('.menu-dropdown').forEach(dropdown => {
                        dropdown.classList.remove('show');
                    });
                });

                // Image modal functionality
                function viewImage(src, alt) {
                    const modal = document.getElementById('imageModal');
                    const modalImg = document.getElementById('modalImage');
                    const caption = document.querySelector('.modal-caption');

                    modal.style.display = 'block';
                    modalImg.src = src;
                    modalImg.alt = alt;
                    caption.textContent = alt;
                }

                // Close modal
                document.querySelector('.modal-close').addEventListener('click', function() {
                    document.getElementById('imageModal').style.display = 'none';
                });

                document.getElementById('imageModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.style.display = 'none';
                    }
                });

                // Delete confirmation
                function confirmDelete(id) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This category will be permanently deleted!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        backdrop: true,
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading state
                            Swal.fire({
                                title: 'Deleting...',
                                text: 'Please wait while we delete the category.',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            setTimeout(() => {
                                document.getElementById('delete-form-' + id).submit();
                            }, 1000);
                        }
                    });
                }

                // Card hover effects
                document.querySelectorAll('.category-card').forEach(card => {
                    card.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-8px) scale(1.02)';
                    });

                    card.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0) scale(1)';
                    });
                });
</script>
@endpush

<style>
    .category-management {
        background: #ffffff; /* Light mode: plain white background */
        min-height: 100vh;
        padding: 2rem;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }


    /* Header Section */
    .dashboard-header {
        background: rgba(30, 30, 40, 0.85);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: #fff;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 1rem;
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
        background: rgba(255, 255, 255, 0.8);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        text-align: center;
        min-width: 100px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 600;
    }

    /* Action Bar */
    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        gap: 2rem;
    }

    .left-actions,
    .right-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .action-btn {
        background: rgba(255, 255, 255, 0.9);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .btn-back {
        background: #fbbf24;
        color: #92400e;
    }

    .btn-add {
        background: #10b981;
        color: white;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .search-box {
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 12px;
        padding: 0.5rem;
        backdrop-filter: blur(10px);
    }

    .search-input {
        border: none;
        outline: none;
        padding: 0.5rem 1rem;
        background: transparent;
        font-size: 0.9rem;
        width: 250px;
    }

    .search-btn {
        background: #667eea;
        border: none;
        border-radius: 8px;
        width: 36px;
        height: 36px;
        cursor: pointer;
        color: white;
        font-size: 0.9rem;
    }

    /* Categories Grid */
    .categories-container {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    /* Category Card jadi horizontal list */
    .category-card {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 1rem 1.5rem;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }

    .category-card:hover {
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .category-number {
        font-weight: 700;
        color: #667eea;
        font-size: 1.1rem;
    }

    .card-menu {
        position: relative;
    }

    .menu-btn {
        background: none;
        border: none;
        font-size: 1.2rem;
        color: #6b7280;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .menu-btn:hover {
        background: rgba(0, 0, 0, 0.05);
    }

    .menu-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        min-width: 150px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.2s ease;
    }

    .menu-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .menu-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        color: #374151;
        text-decoration: none;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        font-size: 0.875rem;
        transition: background 0.2s ease;
    }

    .menu-item:hover {
        background: #f3f4f6;
    }

    .delete-item:hover {
        background: #fef2f2;
        color: #dc2626;
    }

    .card-image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .category-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .category-card:hover .category-image {
        transform: scale(1.05);
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .category-card:hover .image-overlay {
        opacity: 1;
    }

    .view-image-btn {
        background: rgba(255, 255, 255, 0.9);
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .view-image-btn:hover {
        background: white;
        transform: scale(1.05);
    }

    .no-image {
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        color: #9ca3af;
    }

    .no-image-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .card-content {
        padding: 1.5rem;
    }

    .category-name {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 0.5rem 0;
    }

    .category-description {
        color: #6b7280;
        font-size: 0.9rem;
        line-height: 1.5;
        margin: 0;
    }

    .card-actions {
        padding: 0 1.5rem 1rem 1.5rem;
        display: flex;
        gap: 0.75rem;
    }

    .action-btn-small {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        flex: 1;
        justify-content: center;
    }

    .btn-edit {
        background: #3b82f6;
        color: white;
    }

    .btn-delete {
        background: #ef4444;
        color: white;
    }

    .action-btn-small:hover {
        transform: translateY(-2px);
    }

    .card-footer {
        padding: 0.75rem 1.5rem;
        background: #f9fafb;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .created-date {
        font-size: 0.75rem;
        color: #9ca3af;
        text-align: center;
    }

    /* Empty State */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4rem 2rem;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 16px;
        backdrop-filter: blur(10px);
    }

    .empty-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #6b7280;
        font-size: 1rem;
        margin-bottom: 2rem;
    }

    .empty-action-btn {
        background: #10b981;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .empty-action-btn:hover {
        background: #059669;
        transform: translateY(-2px);
    }

    /* Image Modal */
    .image-modal {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(5px);
    }

    .modal-content {
        position: relative;
        margin: auto;
        padding: 20px;
        width: 90%;
        max-width: 700px;
        top: 50%;
        transform: translateY(-50%);
        text-align: center;
    }

    .modal-close {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        z-index: 2001;
    }

    .modal-close:hover {
        color: #667eea;
    }

    #modalImage {
        max-width: 100%;
        max-height: 80vh;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .modal-caption {
        color: white;
        font-size: 1.1rem;
        font-weight: 600;
        margin-top: 1rem;
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

    .category-card {
        animation: slideInUp 0.4s ease-out;
        animation-fill-mode: both;
    }

    .category-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .category-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .category-card:nth-child(3) {
        animation-delay: 0.3s;
    }

    .category-card:nth-child(4) {
        animation-delay: 0.4s;
    }

    .category-card:nth-child(5) {
        animation-delay: 0.5s;
    }

    .category-card:nth-child(6) {
        animation-delay: 0.6s;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .category-management {
            padding: 1rem;
        }

        .header-content {
            flex-direction: column;
            gap: 1.5rem;
            text-align: center;
        }

        .page-title {
            font-size: 2rem;
        }

        .action-bar {
            flex-direction: column;
            gap: 1rem;
        }

        .right-actions {
            width: 100%;
            justify-content: center;
        }

        .search-input {
            width: 200px;
        }

        .categories-container {
            grid-template-columns: 1fr;
        }

        .card-actions {
            flex-direction: column;
        }
    }

    /* === Dark Mode Overrides (Tailwind .dark class applied on <html>) === */
    .dark .category-management {
        background: linear-gradient(135deg, #0f0f12 0%, #1d1f24 100%);
    }
    .dark .dashboard-header {
        background: rgba(255, 255, 255, 0.06);
        box-shadow: 0 10px 30px rgba(0,0,0,0.4);
    }
    .dark .page-subtitle { color: #94a3b8; }
    .dark .stat-card {
        background: rgba(30, 32, 38, 0.85);
        box-shadow: 0 4px 15px rgba(0,0,0,0.4);
    }
    .dark .stat-value { color: #818cf8; }
    .dark .stat-label { color: #64748b; }
    .dark .action-bar { color: #e2e8f0; }
    .dark .action-btn {
        background: rgba(45, 48, 56, 0.85);
        color: #e2e8f0;
    }
    .dark .btn-back { background: #fbbf24; color: #1e293b; }
    .dark .btn-add { background: #10b981; color: #f0fdf4; }
    .dark .action-btn:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.6); }
    .dark .search-box { background: rgba(45,48,56,0.85); }
    .dark .search-input { color: #f1f5f9; }
    .dark .search-btn { background: #4f46e5; }
    .dark .categories-container { color: #f1f5f9; }
    .dark .category-card {
        background: rgba(35, 38, 45, 0.95);
        border: 1px solid rgba(255,255,255,0.08);
    }
    .dark .category-card:hover { box-shadow: 0 15px 40px rgba(0,0,0,0.6); }
    .dark .category-number { color: #818cf8; }
    .dark .category-name { color: #f8fafc; }
    .dark .category-description { color: #cbd5e1; }
    .dark .btn-edit { background: #2563eb; }
    .dark .btn-delete { background: #dc2626; }
    .dark .card-footer { background: #1e293b; }
    .dark .created-date { color: #94a3b8; }
    .dark .empty-state { background: rgba(45,48,56,0.9); color: #e2e8f0; }
    .dark .empty-state h3 { color: #f1f5f9; }
    .dark .empty-state p { color: #cbd5e1; }
    .dark .empty-action-btn { background: #059669; }
    .dark .empty-action-btn:hover { background: #047857; }
    .dark .image-modal { background-color: rgba(0,0,0,0.85); }
    .dark .modal-close { color: #f1f5f9; }
    .dark .modal-close:hover { color: #818cf8; }
    .dark .modal-caption { color: #f1f5f9; }
    .dark #modalImage { box-shadow: 0 10px 30px rgba(0,0,0,0.6); }
    .dark .view-image-btn { background: rgba(255,255,255,0.15); color:#f1f5f9; }
    .dark .view-image-btn:hover { background: rgba(255,255,255,0.25); }
    .dark .no-image { background: linear-gradient(135deg,#1e293b,#334155); color:#64748b; }
</style>
@endsection