@extends('layouts.admin')

@section('content')

{{-- Class admin-dashboard tidak perlu warna karena dihandle oleh body di app.css --}}
<div class="admin-dashboard p-8 min-h-screen dark:bg-gray-800 bg-white" 
    style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;">
    
    <div class="stats-bar grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        
        {{-- Card Total Users --}}
        <div class="stat-item bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 transition-all duration-300 hover:shadow-2xl hover:scale-[1.02] border border-gray-200 dark:border-gray-700">
            <div class="stat-value text-3xl font-bold text-blue-600 dark:text-blue-400 mb-1 transition-transform duration-300">
                {{ $userCount ?? '...' }}
            </div>
            <div class="stat-label text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Users</div>
        </div>
        
        {{-- Card Total Products --}}
        <div class="stat-item bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 transition-all duration-300 hover:shadow-2xl hover:scale-[1.02] border border-gray-200 dark:border-gray-700">
            <div class="stat-value text-3xl font-bold text-green-600 dark:text-green-400 mb-1 transition-transform duration-300">
                {{ $bookCount ?? '...' }}
            </div>
            <div class="stat-label text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Products</div>
        </div>
        
        {{-- Card Total Transactions --}}
        <div class="stat-item bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 transition-all duration-300 hover:shadow-2xl hover:scale-[1.02] border border-gray-200 dark:border-gray-700">
            <div class="stat-value text-3xl font-bold text-yellow-600 dark:text-yellow-400 mb-1 transition-transform duration-300">
                {{ $transactionCount ?? '...' }}
            </div>
            <div class="stat-label text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Transactions</div>
        </div>
    </div>

    <div class="welcome-section flex justify-between items-center mb-8 p-6 md:p-8 rounded-xl backdrop-blur-md transition-colors duration-500 
         bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
        <div class="welcome-text">
            <h1 class="text-3xl md:text-4xl font-extrabold mb-1.5 text-gray-900 dark:text-white">Dashboard Overview</h1>
            <p class="text-lg text-gray-600 dark:text-gray-300">Monitor and manage your entire system from this central hub</p>
        </div>
    </div>

    <div class="main-grid grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Primary Column (User & Product) --}}
        <div class="primary-column lg:col-span-2 space-y-4">
            <a href="{{ route('admin.users.index') }}" 
                class="primary-card users-card block p-6 md:p-8 rounded-2xl shadow-lg transition-all duration-400 hover:shadow-2xl hover:translate-y-[-4px] 
                       bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 dark:hover:border-blue-500">
                <div class="card-content">
                    <div class="flex justify-between items-center mb-4">
                        <div class="text-3xl text-gray-900 dark:text-white">👥</div>
                        <div class="px-3 py-1 text-xs font-semibold uppercase rounded-full bg-blue-100 text-blue-600 dark:bg-blue-800 dark:text-blue-300">
                            Active</div>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold mb-1 text-gray-900 dark:text-white">User Management</h3>
                    <p class="text-base text-gray-600 dark:text-gray-400">Manage user accounts, permissions, and access levels</p>
                </div>
            </a>

            <a href="{{ route('admin.produk.index') }}" 
                class="primary-card products-card block p-6 md:p-8 rounded-2xl shadow-lg transition-all duration-400 hover:shadow-2xl hover:translate-y-[-4px] 
                       bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 dark:hover:border-green-500">
                <div class="card-content">
                    <div class="flex justify-between items-center mb-4">
                        <div class="text-3xl text-gray-900 dark:text-white">📦</div>
                        <div class="px-3 py-1 text-xs font-semibold uppercase rounded-full bg-green-100 text-green-600 dark:bg-green-800 dark:text-green-300">
                            Updated</div>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold mb-1 text-gray-900 dark:text-white">Product Catalog</h3>
                    <p class="text-base text-gray-600 dark:text-gray-400">Add, edit, and organize your product inventory</p>
                </div>
            </a>
        </div>

        {{-- Secondary Column (Categories, Orders, Chat) --}}
        <div class="secondary-column lg:col-span-1 space-y-4">
            
            <a href="{{ route('admin.kategori.index') }}" 
                class="secondary-card flex items-center gap-4 p-4 rounded-xl shadow-md transition-all duration-300 hover:scale-[1.03] 
                       bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 dark:hover:border-blue-500">
                <div class="text-2xl text-gray-900 dark:text-white">🏷️</div>
                <div class="flex-grow">
                    <h4 class="text-base font-semibold text-gray-900 dark:text-white">Categories</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Organize product categories</p>
                </div>
                <div class="text-xl text-blue-500">→</div>
            </a>

            <a href="{{ route('admin.transactions.index') }}" 
                class="secondary-card urgent flex items-center gap-4 p-4 rounded-xl shadow-md transition-all duration-300 hover:scale-[1.03] 
                       bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 dark:hover:border-red-500">
                <div class="text-2xl text-gray-900 dark:text-white">🛒</div>
                <div class="flex-grow">
                    <h4 class="text-base font-semibold text-gray-900 dark:text-white">Order Confirmation</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Process customer orders</p>
                </div>
                <div class="text-xl text-red-500">→</div>
            </a>

            <a href="{{ route('admin.chat') }}" 
                class="secondary-card chat-card flex items-center gap-4 p-4 rounded-xl shadow-md transition-all duration-300 hover:scale-[1.03] 
                       bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 dark:hover:border-blue-500">
                <div class="relative text-2xl">
                    <span class="text-gray-900 dark:text-white">💬</span>
                    <span id="chat-notification-badge" class="absolute top-[-8px] right-[-8px] bg-red-500 text-white rounded-full w-4 h-4 flex justify-center items-center text-[10px] font-bold hidden">
                        N
                    </span>
                </div>
                <div class="flex-grow">
                    <h4 class="text-base font-semibold text-gray-900 dark:text-white">Admin Chat</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Team communication</p>
                </div>
                <div class="text-xl text-blue-500">→</div>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // --- Dark Mode Toggle Script Logic (Ulangi untuk memastikan inisialisasi ikon) ---
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        function applyTheme(theme) {
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
                if (themeToggleLightIcon && themeToggleDarkIcon) {
                    themeToggleLightIcon.classList.remove('hidden');
                    themeToggleDarkIcon.classList.add('hidden');
                }
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
                if (themeToggleLightIcon && themeToggleDarkIcon) {
                    themeToggleDarkIcon.classList.remove('hidden');
                    themeToggleLightIcon.classList.add('hidden');
                }
            }
        }
        
        // Inisialisasi ikon toggle saat DOMContentLoaded
        if (themeToggleBtn) {
             const savedTheme = localStorage.getItem('color-theme');
             const isSystemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
             
             if (savedTheme === 'dark' || (!savedTheme && isSystemDark)) {
                 applyTheme('dark');
             } else {
                 applyTheme('light');
             }
             
             themeToggleBtn.addEventListener('click', function () {
                 const isDark = document.documentElement.classList.contains('dark');
                 applyTheme(isDark ? 'light' : 'dark');
             });
         }
        // ----------------------------------------------------------------------------------

        
        // --- Logika Polling dan Animasi ---
        if ({{ auth()->check() ? 'true' : 'false' }}) {
            const chatBadge = document.getElementById('chat-notification-badge');
            let previousCount = 0;

            function showNotification() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true, position: 'top-end', icon: 'info', title: 'Ada pesan masuk baru!',
                        showConfirmButton: false, timer: 3000, timerProgressBar: true,
                    });
                } else {
                    alert('🔔 Ada pesan masuk baru!');
                }
            }

            function fetchUnreadCount() {
                fetch('{{ route('admin.notifications.count') }}', { method: 'GET', headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.json())
                .then(data => {
                    const count = data.count;
                    const chatCard = document.querySelector('.chat-card');

                    if (count > 0 && count > previousCount) { showNotification(); }
                    previousCount = count;

                    if (count > 0) {
                        chatBadge.textContent = count > 9 ? '9+' : count;
                        chatBadge.classList.remove('hidden');
                        chatCard.style.boxShadow = '0 0 15px rgba(239, 68, 68, 0.6)';
                    } else {
                        chatBadge.classList.add('hidden');
                        chatCard.style.boxShadow = 'none';
                    }
                });
            }

            fetchUnreadCount(); 
            setInterval(fetchUnreadCount, 5000); 

            const chatCardLink = document.querySelector('.chat-card');
            if (chatCardLink) {
                chatCardLink.addEventListener('click', function() {
                    if (!chatBadge.classList.contains('hidden')) {
                        chatBadge.classList.add('hidden');
                        // TODO: AJAX POST mark-read
                    }
                    previousCount = 0; 
                });
            }
        }
        
        // SCRIPT ANIMASI
        const statValues = document.querySelectorAll('.stat-value');
        statValues.forEach((stat, index) => {
             // Animate stats on load
        });

        const cards = document.querySelectorAll('.primary-card, .secondary-card, .stat-item');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-4px) scale(1.02)';
                this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.2)';
            });

            card.addEventListener('mouseleave', function () {
                this.style.transform = 'translateY(0) scale(1)';
                this.style.boxShadow = '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)'; // Shadow default
            });
        });

    });
</script>
@endpush

<style>
/* Animasi (tetap gunakan agar transisi halus) */
@keyframes slideInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.primary-card, .secondary-card, .stat-item {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    animation: slideInUp 0.6s ease-out;
    /* Tambahkan shadow default untuk tampilan light mode */
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}
</style>
@endsection