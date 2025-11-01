@extends('layouts.admin')

@section('content')
<div class="transaction-dashboard">
    <div class="dashboard-header">
        <div class="header-content">
            <div class="header-info">
                <h1 class="page-title">
                    <span class="title-icon">📋</span>
                    Transaction Management
                </h1>
                <p class="page-subtitle">Monitor and manage all user transactions</p>
            </div>
            <div class="header-stats">
                <div class="stat-card">
                    <div class="stat-value">{{ $transactions->where('status', 'pending')->count() }}</div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $transactions->where('status', 'dikirim')->count() }}</div>
                    <div class="stat-label">Shipped</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $transactions->where('status', 'selesai')->count() }}</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
        </div>
    </div>

    <div class="filters-section">
        <div class="filter-tabs">
            <button class="filter-tab active" data-status="all">All Transactions</button>
            <button class="filter-tab" data-status="pending">Pending</button>
            <button class="filter-tab" data-status="dikirim">Shipped</button>
            <button class="filter-tab" data-status="selesai">Completed</button>
        </div>
        <div class="search-box">
            <input type="text" placeholder="Search transactions..." class="search-input">
            <button class="search-btn">🔍</button>
        </div>
    </div>

    {{-- MODIFIKASI DIMULAI DI SINI: PAGINATION DAN DETAIL PRODUK RINGKAS --}}

    @php
    $groupedTransactions = $transactions->groupBy('user_id');
    $trxPerPage = 5;
    @endphp

    <div class="user-transactions-container">
        @forelse ($groupedTransactions as $userId => $userTransactions)
        @php
        $user = $userTransactions->first()->user;

        // --- KODE PAGINATION INTERNAL ---
        $trxPageName = 'user_' . $userId . '_page';
        $currentTrxPage = \Illuminate\Pagination\Paginator::resolveCurrentPage($trxPageName);
        $totalTrxCount = $userTransactions->count();

        $paginatedTrx = new \Illuminate\Pagination\LengthAwarePaginator(
            $userTransactions->slice(($currentTrxPage - 1) * $trxPerPage, $trxPerPage),
            $totalTrxCount,
            $trxPerPage,
            $currentTrxPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'pageName' => $trxPageName]
        );
        @endphp
        <div class="user-group-card" data-user-id="{{ $userId }}">
            {{-- Bagian Kiri: Informasi User --}}
            <div class="user-info-section">
                <div class="user-avatar-lg">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <h2 class="user-group-name">{{ $user->name }}</h2>
                <p class="user-group-email">{{ $user->email }}</p>
                <p class="user-group-stats">Total Transaksi: **{{ $totalTrxCount }}**</p>
            </div>

            {{-- Bagian Kanan: Daftar Transaksi User --}}
            <div class="user-transactions-list">
                @foreach ($paginatedTrx as $trx) 
                <div class="transaction-card" data-status="{{ $trx->status }}" data-user-id="{{ $user->id }}">
                    
                    {{-- HEADER TRANSAKSI --}}
                    <div class="card-header">
                        <div class="invoice-number-compact">#{{ str_pad($trx->id, 6, '0', STR_PAD_LEFT) }}</div>
                        <div class="transaction-date-compact">
                            {{ \Carbon\Carbon::parse($trx->created_at)->format('d M Y H:i') }}
                        </div>
                        <div class="status-badge status-{{ $trx->status }}">
                            <span class="status-icon">
                                @if($trx->status === 'pending') ⏳
                                @elseif($trx->status === 'dikirim') 🚚
                                @elseif($trx->status === 'selesai') ✅
                                @endif
                            </span>
                            {{ strtoupper($trx->status) }}
                        </div>
                    </div>

                    {{-- DETAIL BARANG RINGKAS --}}
                   <div class="card-body">
                        <p class="product-title">
                            @if (isset($trx->items) && $trx->items->count())
                                📦 **{{ $trx->items->count() }}** jenis produk: 
                                
                                {{-- 💡 PERBAIKAN: Mengambil nama produk pertama dengan pengecekan aman --}}
                                @php
                                    // Ambil item pertama
                                    $firstItem = $trx->items->first();
                                    
                                    // Coba properti yang umum (misal: name, product->name, product_name)
                                    // Jika 'product_name' gagal, Anda harus menggunakan nama properti yang benar dari objek $firstItem.
                                    $productName = $firstItem->product_name ?? $firstItem->name ?? ($firstItem->product->name ?? 'NAMA PRODUK TIDAK DITEMUKAN');
                                @endphp

                                <span class="item-name-summary">
                                    {{ $productName }}
                                    @if ($trx->items->count() > 1)
                                        dan {{ $trx->items->count() - 1 }} lainnya...
                                    @endif
                                </span>
                                
                                {{-- ❗ DEBUG: Hapus ini setelah Anda menemukan nama properti yang benar ❗
                                @if($productName === 'NAMA PRODUK TIDAK DITEMUKAN')
                                    @dd($firstItem) 
                                @endif
                                --}}
                            @else
                                ❌ **Gagal Memuat Detail Produk.** (Pastikan relasi `items` dimuat di Controller: `Transaction::with(['user', 'items'])->get()`)
                            @endif
                        </p>
                    </div>
                    {{-- FOOTER & ACTIONS --}}
                    <div class="card-footer">
                        <div class="total-amount-compact">
                            **Total: Rp {{ number_format($trx->total, 0, ',', '.') }}**
                        </div>
                        <div class="card-actions-compact">
                            @if ($trx->status === 'pending')
                            <form method="POST" action="{{ route('admin.transactions.konfirmasi', $trx->id) }}"
                                class="confirm-form-compact">
                                @csrf
                                <button type="submit" class="action-btn btn-confirm-compact">
                                    Confirm
                                </button>
                            </form>
                            @endif
                            @if ($trx->status === 'dikirim')
                            <form method="POST" action="{{ route('admin.transactions.complete', $trx->id) }}" class="confirm-form-compact">
                                @csrf
                                <button type="submit" class="action-btn btn-complete-compact">
                                    Complete
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            {{-- 🚩 TAUTAN PAGINASI UNTUK TRANSAKSI INTERNAL --}}
            @if ($paginatedTrx->hasPages())
            <div class="user-trx-pagination-links">
                {{ $paginatedTrx->links() }}
            </div>
            @endif

        </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <h3>No Transactions Found</h3>
                <p>There are no transactions to display at the moment.</p>
            </div>
        @endforelse
    </div>

    {{-- MODIFIKASI BERAKHIR DI SINI --}}

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter functionality
        const filterTabs = document.querySelectorAll('.filter-tab');
        const userGroupCards = document.querySelectorAll('.user-group-card');

        filterTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                filterTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                const status = this.dataset.status;

                userGroupCards.forEach(group => {
                    group.style.display = 'grid'; 
                
                    group.querySelectorAll('.transaction-card').forEach(card => {
                        if (status === 'all' || card.dataset.status === status) {
                            card.style.display = 'flex';
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    const visibleCards = group.querySelectorAll('.transaction-card[style*="display: flex"]');
                    if (status !== 'all' && visibleCards.length === 0) {
                        group.style.display = 'none';
                    } else {
                        group.style.display = 'grid';
                    }
                });
            });
        });

        // Search functionality
        const searchInput = document.querySelector('.search-input');
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            userGroupCards.forEach(group => {
                let groupHasMatch = false;
                
                group.querySelectorAll('.transaction-card').forEach(card => {
                    const text = card.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        card.style.display = 'flex';
                        groupHasMatch = true;
                    } else {
                        card.style.display = 'none';
                    }
                });

                const userInfoSection = group.querySelector('.user-info-section');
                const userInfoText = userInfoSection ? userInfoSection.textContent.toLowerCase() : '';
                
                if (userInfoText.includes(searchTerm)) {
                    groupHasMatch = true;
                    group.querySelectorAll('.transaction-card').forEach(card => {
                        card.style.display = 'flex';
                    });
                }
                
                if (groupHasMatch) {
                    group.style.display = 'grid';
                } else {
                    group.style.display = 'none';
                }
            });
        });

        // Memperbaiki Konfirmasi Form agar menampilkan loading state
        const confirmFormsCompact = document.querySelectorAll('.confirm-form-compact');
        confirmFormsCompact.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.innerHTML = 'Processing... ⏳'; 
                submitBtn.disabled = true;

                setTimeout(() => {
                    this.submit(); 
                }, 500); 
            });
        });

        // Card hover effects
        const userGroupCards = document.querySelectorAll('.user-group-card');
        userGroupCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>
@endpush

<style>
    /* ================================
    Transaction Dashboard Styles
    ================================ */

    .transaction-dashboard {
        background: linear-gradient(135deg, #18181b 0%, #27272a 100%);
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
    .header-content { display: flex; justify-content: space-between; align-items: center; }
    .page-title { font-size: 2.5rem; font-weight: 800; color: #fff; }
    .page-subtitle { color: #64748b; }
    .header-stats { display: flex; gap: 1rem; }
    .stat-card { background: rgba(255, 255, 255, 0.8); border-radius: 12px; padding: 1rem 1.5rem; text-align: center; }
    .stat-value { font-size: 1.5rem; font-weight: 700; color: #667eea; }
    .stat-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 600; }


    /* Filters Section */
    .filters-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; gap: 2rem; }
    .filter-tabs { display: flex; gap: 0.5rem; }
    .filter-tab.active, .filter-tab:hover { background: #2e2e2e; color: white; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(163, 163, 163, 0.3); }

    /* ================================
        MODIFIED User Group Styles
        ================================ */

    .user-transactions-container {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .user-group-card {
        display: grid;
        grid-template-columns: 250px 1fr;
        grid-template-rows: auto auto; 
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .user-info-section {
        grid-row: 1 / span 2; 
        background: linear-gradient(180deg, #f1f5f9 0%, #e2e8f0 100%);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        border-right: 1px solid #cbd5e1;
    }

    .user-transactions-list {
        grid-column: 2; 
        grid-row: 1;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        max-height: none; 
        overflow-y: visible; 
    }
    
    /* Style untuk Paginasi Internal */
    .user-trx-pagination-links {
        grid-column: 2; 
        grid-row: 2;
        padding: 0.5rem 1rem 1rem 1rem;
        display: flex;
        justify-content: flex-end; 
        background: #ffffff;
        border-top: 1px solid #e2e8f0; 
    }
    .user-trx-pagination-links .pagination { margin: 0; padding: 0; }
    .user-trx-pagination-links .pagination .page-item { font-size: 0.75rem; }
    
    /* ================================
    NEW TRANSACTION CARD STYLE
    ================================ */
    .transaction-card {
        display: flex;
        flex-direction: column;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
        border: 1px solid #e2e8f0;
        margin-bottom: 5px; 
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 1rem; /* Padding lebih kecil */
        border-bottom: 1px solid #e2e8f0;
        background: #fdfdfd;
        border-radius: 8px 8px 0 0;
        font-size: 0.9rem;
    }

    /* DETAIL BARANG (Card Body Ringkas) */
    .card-body {
        padding: 0.5rem 1rem; /* Padding lebih kecil */
    }
    .product-title {
        font-weight: 500; /* Lebih ringan */
        color: #1e293b;
        margin: 0;
        font-size: 0.85rem; /* Lebih kecil */
    }
    .item-name-summary {
        font-weight: 600;
        color: #3b82f6;
    }

    /* FOOTER & ACTIONS */
    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 1rem; /* Padding lebih kecil */
        background: #f9fafb;
        border-top: 1px solid #e2e8f0;
        border-radius: 0 0 8px 8px;
    }

    .total-amount-compact {
        font-size: 1rem; /* Lebih ringkas */
        font-weight: 700;
        color: #059669;
    }
    
    /* STATUS BADGES */
    .status-badge { padding: 0.2rem 0.5rem; font-size: 0.6rem; }
    
    .status-pending { background: #fef3c7; color: #d97706; }
    .status-dikirim { background: #dbeafe; color: #2563eb; }
    .status-selesai { background: #d1fae5; color: #059669; }

    /* ACTIONS */
    .btn-confirm-compact, .btn-complete-compact {
        font-size: 0.7rem; /* Lebih kecil */
        padding: 0.4rem 0.6rem;
    }

    /* Remove old compact style */
    .transaction-card-compact { display: none !important; }

    /* Responsive Overrides */
    @media (max-width: 992px) {
        .user-group-card { grid-template-columns: 1fr; grid-template-rows: auto auto auto; }
        .user-info-section { grid-row: 1; border-right: none; border-bottom: 1px solid #cbd5e1; }
        .user-transactions-list { grid-column: 1; grid-row: 2; max-height: none; overflow-y: visible; }
        .user-trx-pagination-links { grid-column: 1; grid-row: 3; justify-content: center; border-radius: 0 0 16px 16px; border-top: 1px solid #e2e8f0; }
        .card-header, .card-footer { flex-wrap: wrap; }
        .total-amount-compact { order: 2; width: 100%; text-align: right; margin-top: 0.5rem; }
    }
</style>
@endsection