@extends('layouts.admin')

@section('content')
{{-- Kontainer Utama & Alpine Data --}}
<div id="transactionContainer" class="transaction-dashboard min-h-screen p-8" 
     x-data="{ 
        openModal: false, 
        currentTrxId: null,
        currentStatus: '',
        currentNote: ''
     }">
    <div class="max-w-7xl mx-auto">

        {{-- Dashboard Header --}}
        <div class="dashboard-header rounded-2xl p-6 mb-8 shadow-xl">
            <div class="header-content flex flex-wrap justify-between items-center gap-4">
                <div class="header-info">
                    <h1 class="page-title text-3xl font-extrabold">
                        <span class="title-icon">☕</span>
                        Manajemen Pesanan Coffee
                    </h1>
                    <p class="page-subtitle">Pantau dan kelola semua pesanan pelanggan</p>
                </div>
                <div class="header-stats flex gap-3">
                    <div class="stat-card rounded-lg p-3 text-center min-w-[100px]">
                        <div class="stat-value text-xl font-bold">{{ $transactions->where('status', 'pending')->count() }}</div>
                        <div class="stat-label text-xs uppercase font-semibold">Pending</div>
                    </div>
                    <div class="stat-card rounded-lg p-3 text-center min-w-[100px]">
                        <div class="stat-value text-xl font-bold">{{ $transactions->where('status', 'paid')->count() }}</div>
                        <div class="stat-label text-xs uppercase font-semibold">Paid</div>
                    </div>
                    <div class="stat-card rounded-lg p-3 text-center min-w-[100px]">
                        <div class="stat-value text-xl font-bold">{{ $transactions->where('status', 'dikirim')->count() }}</div>
                        <div class="stat-label text-xs uppercase font-semibold">Shipped</div>
                    </div>
                    <div class="stat-card rounded-lg p-3 text-center min-w-[100px]">
                        <div class="stat-value text-xl font-bold">{{ $transactions->where('status', 'selesai')->count() }}</div>
                        <div class="stat-label text-xs uppercase font-semibold">Completed</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="filters-section flex flex-wrap justify-between items-center mb-6 gap-4">
            <div class="filter-tabs flex gap-2">
                <button class="filter-tab active px-3 py-1.5 rounded-lg text-sm font-medium" data-status="all">All Transactions</button>
                <button class="filter-tab px-3 py-1.5 rounded-lg text-sm font-medium" data-status="pending">Pending</button>
                <button class="filter-tab px-3 py-1.5 rounded-lg text-sm font-medium" data-status="paid">Paid</button>
                <button class="filter-tab px-3 py-1.5 rounded-lg text-sm font-medium" data-status="dikirim">Shipped</button>
                <button class="filter-tab px-3 py-1.5 rounded-lg text-sm font-medium" data-status="selesai">Completed</button>
                <button class="filter-tab px-3 py-1.5 rounded-lg text-sm font-medium" data-status="dibatalkan">Cancelled</button>
            </div>
            <div class="search-box flex">
                <input type="text" placeholder="Cari transaksi..." class="search-input p-2 rounded-l-lg">
                <button class="search-btn p-2 rounded-r-lg">🔍</button>
            </div>
        </div>

        {{-- ================================================
        DAFTAR TRANSAKSI
        ================================================ --}}

        @php
        $groupedTransactions = $transactions->groupBy('user_id');
        $trxPerPage = 5;
        @endphp

        <div class="user-transactions-container space-y-6">
            @forelse ($groupedTransactions as $userId => $userTransactions)
            @php
            $user = $userTransactions->first()->user;
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
            <div class="user-group-card rounded-xl shadow-lg overflow-hidden transition-transform duration-300" data-user-id="{{ $userId }}">
                
                {{-- Bagian Kiri: Informasi User --}}
                <div class="user-info-section p-6 flex flex-col items-center text-center">
                    <div class="user-avatar-lg w-20 h-20 rounded-full bg-blue-600 text-white flex items-center justify-center text-2xl font-bold mb-3 border-4">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <h2 class="user-group-name text-lg font-bold">{{ $user->name }}</h2>
                    <p class="user-group-email text-sm">{{ $user->email }}</p>
                    <p class="user-group-stats text-sm mt-2">Total Transaksi: <strong>{{ $totalTrxCount }}</strong></p>
                </div>

                {{-- Bagian Kanan: Daftar Transaksi User --}}
                <div class="user-transactions-list p-4 flex flex-col gap-2">
                    @foreach ($paginatedTrx as $trx)
                    <div class="transaction-card rounded-lg" data-status="{{ $trx->status }}" data-user-id="{{ $user->id }}" x-data="{ expanded: false }">

                        {{-- Header (Klik untuk expand) --}}
                        <div class="card-header flex justify-between items-center p-3 cursor-pointer select-none" @click="expanded = !expanded">
                            <div class="invoice-number-compact font-semibold">#{{ str_pad($trx->id, 6, '0', STR_PAD_LEFT) }}</div>
                            <div class="transaction-date-compact text-sm">{{ \Carbon\Carbon::parse($trx->created_at)->format('d M Y H:i') }}</div>
                            <div class="transaction-date-compact text-sm">{{ \Carbon\Carbon::parse($trx->created_at)->format('d M Y H:i') }}</div>
                            <div class="status-badge status-{{ $trx->status }} px-2 py-1 rounded-full text-xs font-semibold">
                                <span class="status-icon mr-1">
                                    @if($trx->status === 'pending') ⏳
                                    @elseif($trx->status === 'paid') 💳
                                    @elseif($trx->status === 'dikirim') 🚚
                                    @elseif($trx->status === 'selesai') ✅
                                    @endif
                                </span>
                                {{ strtoupper($trx->status) }}
                            </div>
                            {{-- Panah Accordion --}}
                            <div class="accordion-arrow transform transition-transform duration-200" :class="{'rotate-180': expanded}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z" /></svg>
                            </div>
                        </div>

                        {{-- Konten Expand (Items & Review) --}}
                        <div x-show="expanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-screen" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 max-h-screen" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden" style="display: none;">
                            
                            {{-- Items List --}}
                            <div class="item-details-list p-4">
                                
                                
                                <div class="item-details-header pb-3 mb-4 border-b">
                                    <h4 class="text-sm font-semibold uppercase tracking-wide">Daftar Produk</h4>
                                </div>

                                @if (isset($trx->items) && $trx->items->count())
                                @foreach ($trx->items as $item)
                                <div class="item-detail-row border-b border-gray-200 dark:border-gray-600 py-3 last:border-b-0">
                                    <div class="item-info flex items-start gap-4">
                                        <img src="{{ $item->produk ? asset('storage/' . $item->produk->foto) : 'https://via.placeholder.com/60' }}" alt="{{ $item->nama_barang }}" class="item-image w-12 h-12 object-cover rounded-lg shrink-0 shadow-sm">
                                        <div class="flex-1 min-w-0">
                                            <div class="item-name font-semibold text-base mb-1">{{ $item->produk ? $item->produk->nama : $item->nama_barang }}</div>
                                            <div class="item-meta text-sm space-x-3">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                                    {{ $item->jumlah }}x
                                                </span>
                                                <span class="font-semibold text-green-600 dark:text-green-400">
                                                    Rp {{ number_format($item->harga, 0, ',', '.') }}
                                                </span>
                                                @if(!empty($item->toppings) && is_array($item->toppings))
                                                <div class="mt-2">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Toppings:</span>
                                                    @php $names = \App\Models\Topping::whereIn('id', $item->toppings)->pluck('name')->toArray(); @endphp
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        @foreach($names as $topping)
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                                                {{ $topping }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @else
                                <div class="item-review-none p-2">Gagal memuat item.</div>
                                @endif
                            </div>

                            {{-- Form Aksi dan Total (Sederhana) --}}
                            <div class="card-footer p-4 flex justify-between items-center">
                                <div class="total-amount-compact font-bold text-lg">
                                    Total: Rp {{ number_format($trx->total, 0, ',', '.') }}
                                </div>
                                
                                {{-- Action Button Sederhana --}}
                                <div class="card-actions-compact flex gap-2 items-center">
                                    
                                    @if(config('fitur.order_preparation'))
                                        {{-- Tombol Aksi Status Otomatis --}}
                                        @if ($trx->status === 'pending')
                                            {{-- Tombol Kirim Pesanan (Konfirmasi) --}}
                                            <form method="POST" action="{{ route('admin.transactions.konfirmasi', $trx->id) }}" class="confirm-form-compact">
                                                @csrf
                                                <button type="submit" class="action-btn px-3 py-1 text-sm rounded-md bg-green-600 text-white hover:bg-green-700 transition">
                                                    Kirim Pesanan
                                                </button>
                                            </form>
                                        @elseif ($trx->status === 'paid')
                                            {{-- Info Pembayaran Otomatis --}}
                                            <div class="flex items-center gap-2">
                                                <span class="px-3 py-1 text-sm rounded-md bg-blue-100 text-blue-800 font-medium">
                                                    ✅ Terbayar Otomatis
                                                </span>
                                                <form method="POST" action="{{ route('admin.transactions.konfirmasi', $trx->id) }}" class="confirm-form-compact">
                                                    @csrf
                                                    <button type="submit" class="action-btn px-3 py-1 text-sm rounded-md bg-green-600 text-white hover:bg-green-700 transition">
                                                        Konfirmasi Pengiriman
                                                    </button>
                                                </form>
                                            </div>
                                        @elseif ($trx->status === 'dikirim')
                                            {{-- Tombol Selesaikan Pesanan --}}
                                            <form method="POST" action="{{ route('admin.transactions.complete', $trx->id) }}" class="confirm-form-compact">
                                                @csrf
                                                <button type="submit" class="action-btn px-3 py-1 text-sm rounded-md bg-purple-600 text-white hover:bg-purple-700 transition">
                                                    Selesaikan Pesanan
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                    @if(config('fitur.admin_message'))
                                    {{-- Tombol Modal Pesan (Selalu ada di samping) --}}
                                    <button 
                                        @click="
                                            openModal = true; 
                                            currentTrxId = {{ $trx->id }};
                                            currentStatus = '{{ $trx->status }}';
                                            currentNote = '{{ addslashes($trx->note ?? '') }}';
                                        " 
                                        class="action-btn px-3 py-1 text-sm rounded-md bg-blue-600 text-white hover:bg-blue-700 transition">
                                        Tambahkan Pesan
                                    </button>
                                    @endif
                                </div>
                            </div>
                            
                            {{-- Catatan Pengiriman (Jika ada) --}}
                            @if ($trx->note)
                            <div class="p-3">
                                📢 **Pesan Pengiriman:** {{ $trx->note }}
                            </div>
                            @endif

                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- TAUTAN PAGINASI UNTUK TRANSAKSI INTERNAL --}}
                @if ($paginatedTrx->hasPages())
                <div class="user-trx-pagination-links col-span-full flex justify-end p-4">
                    {{ $paginatedTrx->links() }}
                </div>
                @endif
            </div>
            @empty
            <div class="empty-state text-center p-12 rounded-xl shadow-lg">
                <div class="empty-icon text-5xl mb-4">☕</div>
                <h3 class="text-xl font-bold">Belum Ada Pesanan</h3>
                <p>Belum ada pesanan coffee yang masuk saat ini.</p>
            </div>
            @endforelse
        </div>
    </div>
    
    @if(config('fitur.admin_message'))
    {{-- ================================================
    ⭐️ MODAL KELOLA STATUS & PESAN ⭐️
    ================================================ --}}
    <div x-show="openModal" @keydown.escape.window="openModal = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4"
         style="display: none;">
        
        <div @click.outside="openModal = false" 
             class="modal-content rounded-lg shadow-2xl w-full max-w-lg p-6">
            
            <h3 class="text-xl font-bold mb-4">Kelola Transaksi: #<span x-text="currentTrxId"></span></h3>

            {{-- Formulir akan mengarah ke route update dengan ID transaksi saat ini --}}
            <form :action="'{{ url('admin/transactions') }}/' + currentTrxId" method="POST">
                @csrf
                @method('PUT') 

                <div class="space-y-4">
                    
                    {{-- Dropdown Status --}}
                    <div>
                        <label for="modal_status" class="block text-sm font-medium mb-1">Ubah Status Transaksi</label>
                        <select name="status" id="modal_status" x-model="currentStatus" required
                            class="modal-select mt-1 block w-full pl-3 pr-10 py-2 text-base rounded-md">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="dikirim">Dikirim</option>
                            <option value="selesai">Selesai</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>
                    </div>

                    {{-- Kolom Pesan Admin --}}
                    <div>
                        <label for="modal_note" class="block text-sm font-medium mb-1">Pesan/Catatan Pengiriman (Opsional)</label>
                        <textarea name="note" id="modal_note" rows="3" x-model="currentNote"
                            class="modal-textarea mt-1 block w-full shadow-sm text-sm rounded-md p-2"
                            placeholder="Pesan ini akan terlihat oleh Pembeli."></textarea>
                        <p class="mt-1 text-xs">Pesan ini akan muncul di riwayat transaksi pembeli.</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="openModal = false"
                            class="modal-btn-cancel px-4 py-2 rounded-lg transition font-medium">
                        Batal
                    </button>
                    <button type="submit"
                            class="modal-btn-submit px-4 py-2 rounded-lg transition font-semibold">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const transactionContainer = document.getElementById('transactionContainer');
        
        // Sync theme with navbar
        const syncTheme = () => {
            const isDark = document.documentElement.classList.contains('dark');
            transactionContainer.setAttribute('data-theme', isDark ? 'dark' : 'light');
        };

        // Initial sync
        syncTheme();

        // Watch for theme changes from navbar
        const observer = new MutationObserver(syncTheme);
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });

        // --- Filter functionality ---
        const filterTabs = document.querySelectorAll('.filter-tab');
        const userGroupCards = document.querySelectorAll('.user-group-card');

        filterTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                filterTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                const status = this.dataset.status;

                userGroupCards.forEach(group => {
                    let groupMatches = false;
                    let hasVisibleCardInGroup = false;

                    group.querySelectorAll('.transaction-card').forEach(card => {
                        // FIX: Change display style from 'flex' (which overrides Dark Mode class) to 'block'
                        if (status === 'all' || card.dataset.status === status) {
                            card.style.display = 'block';
                            hasVisibleCardInGroup = true;
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    if (hasVisibleCardInGroup || status === 'all') {
                        group.style.display = 'grid';
                    } else {
                        group.style.display = 'none';
                    }
                });
            });
        });

        // --- Search functionality ---
        const searchInput = document.querySelector('.search-input');
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            userGroupCards.forEach(group => {
                let groupHasMatch = false;
                
                group.querySelectorAll('.transaction-card').forEach(card => {
                    const text = card.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        card.style.display = 'block'; // FIX: Use block for compatibility
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
                        card.style.display = 'block';
                    });
                }
                
                if (groupHasMatch) {
                    group.style.display = 'grid';
                } else {
                    group.style.display = 'none';
                }
            });
        });

        // Handle konfirmasi/complete via AJAX with SweetAlert feedback
        const confirmFormsCompact = document.querySelectorAll('.confirm-form-compact');
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : null;

        confirmFormsCompact.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
                const actionUrl = this.action;
                const isKonfirmasi = actionUrl.includes('/konfirmasi');
                const confirmText = isKonfirmasi ? 'Yakin ingin konfirmasi pengiriman pesanan ini?' : 'Yakin ingin menandai pesanan ini selesai?';

                Swal.fire({
                    title: isKonfirmasi ? 'Konfirmasi Pesanan' : 'Selesaikan Pesanan',
                    text: confirmText,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then(result => {
                    if (!result.isConfirmed) return;

                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Processing... ⏳';

                    // Send POST as form data so middleware CSRF still valid
                    const formData = new FormData(this);

                    fetch(actionUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(async (res) => {
                        const json = await res.json().catch(() => ({}));
                        if (res.ok) {
                            Swal.fire({ icon: 'success', title: json.message || 'Berhasil', toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 });

                            // Update UI: change badge and remove/replace action button
                            const card = this.closest('.transaction-card');
                            if (card) {
                                const newStatus = json.status || (isKonfirmasi ? 'dikirim' : 'selesai');
                                card.dataset.status = newStatus;
                                const badge = card.querySelector('.status-badge');
                                if (badge) {
                                    badge.className = 'status-badge status-' + newStatus + ' px-2 py-1 rounded-full text-xs font-semibold';
                                    let icon = '';
                                    if (newStatus === 'pending') icon = '⏳ ';
                                    if (newStatus === 'paid') icon = '💳 ';
                                    if (newStatus === 'dikirim') icon = '🚚 ';
                                    if (newStatus === 'selesai') icon = '✅ ';
                                    badge.innerHTML = icon + newStatus.toUpperCase();
                                }

                                // Remove the form so admin won't double-click; optionally you could replace with next action
                                this.remove();
                            }
                        } else {
                            Swal.fire({ icon: 'error', title: json.message || 'Gagal', text: json.message || 'Terjadi kesalahan', toast: true, position: 'top-end', showConfirmButton: false, timer: 4000 });
                            submitBtn.disabled = false;
                            submitBtn.innerText = isKonfirmasi ? 'Kirim Pesanan' : 'Selesaikan Pesanan';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan pada server', toast: true, position: 'top-end', showConfirmButton: false, timer: 4000 });
                        submitBtn.disabled = false;
                        submitBtn.innerText = isKonfirmasi ? 'Kirim Pesanan' : 'Selesaikan Pesanan';
                    });
                });
            });
        });

        // Card hover effects (Dipindahkan ke CSS untuk konsistensi)
        /*
        const allUserGroupCards = document.querySelectorAll('.user-group-card');
        allUserGroupCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        */
    });
</script>
@endpush

@push('styles')
<style>
    /* ================================
    CSS Variables for Theme Toggle
    ================================ */
    :root {
        --bg-gradient-start: #18181b;
        --bg-gradient-end: #27272a;
        --dashboard-bg: #111827;
        --card-bg: rgba(30, 30, 40, 0.95);
        --text-primary: #ffffff;
        --text-secondary: #a1a1aa;
        --border-color: rgba(55, 65, 81, 0.8);
        --stat-pending-bg: #422006;
        --stat-pending-text: #fcd34d;
        --stat-shipped-bg: #1e3a8a;
        --stat-shipped-text: #60a5fa;
        --stat-complete-bg: #065f46;
        --stat-complete-text: #34d399;
        --filter-active-bg: #2563eb;
        --filter-inactive-bg: #374151;
        --filter-inactive-hover: #4b5563;
        --input-bg: #1f2937;
        --input-border: #374151;
        --button-primary: #2563eb;
        --button-primary-hover: #1d4ed8;
        --status-pending-bg: #92400e;
        --status-pending-text: #fcd34d;
        --status-shipped-bg: #1e3a8a;
        --status-shipped-text: #60a5fa;
        --status-complete-bg: #065f46;
        --status-complete-text: #34d399;
        --user-card-bg: #1f2937;
        --user-info-bg: #111827;
        --item-detail-bg: #1f2937;
        --footer-bg: #111827;
        --empty-icon-color: #4b5563;
        --total-color: #34d399;
        --modal-bg: #1f2937;
        --modal-btn-cancel-bg: #374151;
        --modal-btn-cancel-hover: #4b5563;
        --modal-btn-cancel-text: #d1d5db;
    }

    #transactionContainer[data-theme="light"] {
        --bg-gradient-start: #e0f2fe;
        --bg-gradient-end: #bae6fd;
        --dashboard-bg: #f3f4f6;
        --card-bg: rgba(255, 255, 255, 0.95);
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --border-color: rgba(203, 213, 225, 0.8);
        --stat-pending-bg: #fef3c7;
        --stat-pending-text: #d97706;
        --stat-shipped-bg: #dbeafe;
        --stat-shipped-text: #2563eb;
        --stat-complete-bg: #d1fae5;
        --stat-complete-text: #059669;
        --filter-active-bg: #3b82f6;
        --filter-inactive-bg: #ffffff;
        --filter-inactive-hover: #f3f4f6;
        --input-bg: #ffffff;
        --input-border: #d1d5db;
        --button-primary: #3b82f6;
        --button-primary-hover: #2563eb;
        --status-pending-bg: #fef3c7;
        --status-pending-text: #d97706;
        --status-shipped-bg: #dbeafe;
        --status-shipped-text: #2563eb;
        --status-complete-bg: #d1fae5;
        --status-complete-text: #059669;
        --user-card-bg: #ffffff;
        --user-info-bg: #f9fafb;
        --item-detail-bg: #f9fafb;
        --footer-bg: #f9fafb;
        --empty-icon-color: #9ca3af;
        --total-color: #059669;
        --modal-bg: #ffffff;
        --modal-btn-cancel-bg: #e5e7eb;
        --modal-btn-cancel-hover: #d1d5db;
        --modal-btn-cancel-text: #374151;
    }

    #transactionContainer {
        background: linear-gradient(135deg, var(--bg-gradient-start), var(--bg-gradient-end));
        transition: all 0.4s ease;
    }

    #transactionContainer .transaction-dashboard {
        transition: background 0.3s ease;
    }

    #transactionContainer .dashboard-header {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        transition: all 0.3s ease;
    }

    #transactionContainer .page-title {
        color: var(--text-primary);
        transition: color 0.3s ease;
    }

    #transactionContainer .page-subtitle {
        color: var(--text-secondary);
        transition: color 0.3s ease;
    }

    #transactionContainer .stat-card {
        transition: all 0.3s ease;
    }

    #transactionContainer .stat-card:nth-child(1) {
        background: var(--stat-pending-bg);
    }

    #transactionContainer .stat-card:nth-child(1) .stat-value,
    #transactionContainer .stat-card:nth-child(1) .stat-label {
        color: var(--stat-pending-text);
    }

    #transactionContainer .stat-card:nth-child(2) {
        background: var(--stat-shipped-bg);
    }

    #transactionContainer .stat-card:nth-child(2) .stat-value,
    #transactionContainer .stat-card:nth-child(2) .stat-label {
        color: var(--stat-shipped-text);
    }

    #transactionContainer .stat-card:nth-child(3) {
        background: var(--stat-complete-bg);
    }

    #transactionContainer .stat-card:nth-child(3) .stat-value,
    #transactionContainer .stat-card:nth-child(3) .stat-label {
        color: var(--stat-complete-text);
    }

    #transactionContainer .filter-tab.active {
        background: var(--filter-active-bg);
        color: white;
        transition: all 0.3s ease;
    }

    #transactionContainer .filter-tab:not(.active) {
        background: var(--filter-inactive-bg);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    #transactionContainer .filter-tab:not(.active):hover {
        background: var(--filter-inactive-hover);
    }

    #transactionContainer .search-input {
        background: var(--input-bg);
        color: var(--text-primary);
        border: 1px solid var(--input-border);
        transition: all 0.3s ease;
    }

    #transactionContainer .search-input:focus {
        outline: none;
        border-color: var(--button-primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    #transactionContainer .search-btn {
        background: var(--button-primary);
        transition: all 0.3s ease;
    }

    #transactionContainer .search-btn:hover {
        background: var(--button-primary-hover);
    }

    #transactionContainer .user-group-card {
        background: var(--user-card-bg);
        border: 1px solid var(--border-color);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: grid;
        grid-template-columns: 250px 1fr;
        grid-template-rows: auto auto;
        cursor: pointer;
    }

    #transactionContainer .user-group-card:hover {
        transform: translateY(-4px);
    }

    #transactionContainer .user-info-section {
        background: var(--user-info-bg);
        border-right: 1px solid var(--border-color);
        color: var(--text-primary);
        transition: all 0.3s ease;
        grid-row: 1 / span 2;
    }

    #transactionContainer .user-group-name {
        color: var(--text-primary);
        transition: color 0.3s ease;
    }

    #transactionContainer .user-group-email,
    #transactionContainer .user-group-stats {
        color: var(--text-secondary);
        transition: color 0.3s ease;
    }

    #transactionContainer .transaction-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    #transactionContainer .card-header {
        background: var(--user-info-bg);
        border-bottom: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    #transactionContainer .invoice-number-compact {
        color: var(--text-primary);
        transition: color 0.3s ease;
    }

    #transactionContainer .transaction-date-compact {
        color: var(--text-secondary);
        transition: color 0.3s ease;
    }

    #transactionContainer .item-details-list {
        background: var(--item-detail-bg);
        border-top: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    #transactionContainer .item-details-header {
        color: var(--text-secondary);
        border-color: var(--border-color);
        transition: all 0.3s ease;
    }

    #transactionContainer .item-name {
        color: var(--text-primary);
        transition: color 0.3s ease;
    }

    #transactionContainer .item-meta {
        color: var(--text-secondary);
        transition: color 0.3s ease;
    }

    #transactionContainer .card-footer {
        background: var(--footer-bg);
        border-top: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    #transactionContainer .action-btn {
        transition: all 0.3s ease;
    }

    #transactionContainer .empty-state {
        background: var(--card-bg);
        color: var(--text-primary);
        transition: all 0.3s ease;
    }

    #transactionContainer .empty-icon {
        color: var(--empty-icon-color);
        transition: color 0.3s ease;
    }

    #transactionContainer .total-amount-compact {
        color: var(--total-color);
        transition: color 0.3s ease;
    }

    #transactionContainer .user-avatar-lg {
        border-color: var(--user-info-bg);
        transition: border-color 0.3s ease;
    }

    #transactionContainer .accordion-arrow {
        color: var(--text-secondary);
        transition: color 0.3s ease;
    }

    #transactionContainer .item-detail-row {
        border-bottom: 1px solid var(--border-color);
        transition: border-color 0.3s ease;
    }

    #transactionContainer .item-details-header {
        border-bottom: 1px solid var(--border-color);
        transition: border-color 0.3s ease;
    }

    #transactionContainer .modal-content {
        background: var(--modal-bg);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    #transactionContainer .modal-select,
    #transactionContainer .modal-textarea {
        background: var(--input-bg);
        color: var(--text-primary);
        border: 1px solid var(--input-border);
        transition: all 0.3s ease;
    }

    #transactionContainer .modal-select:focus,
    #transactionContainer .modal-textarea:focus {
        outline: none;
        border-color: var(--input-focus);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    #transactionContainer .modal-btn-cancel {
        background: var(--modal-btn-cancel-bg);
        color: var(--modal-btn-cancel-text);
        transition: all 0.3s ease;
    }

    #transactionContainer .modal-btn-cancel:hover {
        background: var(--modal-btn-cancel-hover);
    }

    #transactionContainer .modal-btn-submit {
        background: var(--button-primary);
        color: white;
        transition: all 0.3s ease;
    }

    #transactionContainer .modal-btn-submit:hover {
        background: var(--button-primary-hover);
    }

    #transactionContainer .user-transactions-list {
        grid-column: 2;
        grid-row: 1;
        padding: 1rem;
    }

    #transactionContainer .user-trx-pagination-links {
        grid-column: 2;
        grid-row: 2;
        padding: 0.5rem 1rem 1rem;
        background: var(--footer-bg);
        border-top: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    /* ================================
    Styles (Dark Mode & Structure)
    ================================ */
    
    /* ⭐️ FIX: Tombol Status & Hover ⭐️ */
    .btn-confirm-compact {
        background-color: #10b981; /* Green */
        color: white;
    }
    .btn-confirm-compact:hover {
        background-color: #059669; /* Darker Green */
    }
    .btn-complete-compact {
        background-color: #9b59b6; /* Purple */
        color: white;
    }
    .btn-complete-compact:hover {
        background-color: #8e44ad; /* Darker Purple */
    }

    /* Dark Mode Button Hover Fix */
    .dark .btn-confirm-compact {
        background-color: #065f46; /* Dark Green */
    }
    .dark .btn-confirm-compact:hover {
        background-color: #059669;
    }
    .dark .btn-complete-compact {
        background-color: #7e339b; /* Dark Purple */
    }
    .dark .btn-complete-compact:hover {
        background-color: #9b59b6;
    }
    

    /* --- Struktur Utama --- */
    .transaction-dashboard {
        transition: background 0.3s ease;
    }

    /* Status Badges */
    .status-badge { 
        padding: 0.2rem 0.5rem; 
        font-size: 0.6rem; 
        font-weight: 600; 
        text-transform: uppercase;
        transition: all 0.3s ease;
    }
    
    #transactionContainer .status-pending { 
        background: var(--status-pending-bg); 
        color: var(--status-pending-text); 
    }

    #transactionContainer .status-dikirim { 
        background: var(--status-shipped-bg); 
        color: var(--status-shipped-text); 
    }

    #transactionContainer .status-selesai { 
        background: var(--status-complete-bg); 
        color: var(--status-complete-text); 
    }
    
    #transactionContainer .status-paid { 
        background: #059669; 
        color: #ffffff; 
    }
    
    #transactionContainer .status-dibatalkan { 
        background: #dc2626; 
        color: #ffffff; 
    }
    
    /* Responsive Overrides */
    @media (max-width: 992px) {
        #transactionContainer .user-group-card { 
            grid-template-columns: 1fr; 
            grid-template-rows: auto auto auto; 
        }
        #transactionContainer .user-info-section { 
            grid-row: 1; 
            border-right: none; 
            border-bottom: 1px solid var(--border-color); 
        }
        #transactionContainer .user-transactions-list { 
            grid-column: 1; 
            grid-row: 2; 
        }
        #transactionContainer .user-trx-pagination-links { 
            grid-column: 1; 
            grid-row: 3; 
            justify-content: center; 
        }
    }
</style>
@endpush
