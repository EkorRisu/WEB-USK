@extends('layouts.admin')
@section('content')
<div class="admin-dashboard-dark"
    style="min-height: 90vh; color:white; font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; padding: 2rem;">
    <div class="welcome-section"
        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding: 2rem; backdrop-filter: blur(10px); background: rgba(0, 0, 0, 0.1); border-radius: 16px;">
        <div class="welcome-text">
            <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem;">Dashboard Overview</h1>
            <p style="color: white; font-size: 1.1rem;">Monitor and manage your entire system from this central hub</p>
        </div>

        <div class="main-grid" style="display: grid; grid-template-columns: 2fr 1.5fr 1fr; gap: 2rem;">
            <div class="primary-column">
                <a href="{{ route('admin.users.index') }}" class="primary-card users-card"
                    style="position: relative; background: rgba(25, 25, 25, 0.1)); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 20px; padding: 2rem; margin-bottom: 1.5rem; text-decoration: none; color: inherit; display: block; overflow: hidden; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);">
                    <div class="card-content">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <div style="font-size: 2rem;">👥</div>
                            <div
                                style="background: rgba(99, 102, 241, 0.2); color: #6366f1; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">
                                Active</div>
                        </div>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; color: #f1f5f9;">User
                            Management</h3>
                        <p style="color:white ; line-height: 1.6; margin-bottom: 1rem;">Manage user accounts,
                            permissions, and access levels</p>
                    </div>
                </a>

                <a href="{{ route('admin.produk.index') }}" class="primary-card products-card"
                    style="position: relative; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 20px; padding: 2rem; margin-bottom: 1.5rem; text-decoration: none; color: inherit; display: block; overflow: hidden; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);">
                    <div class="card-content">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <div style="font-size: 2rem;">📦</div>
                            <div
                                style="background: rgba(16, 185, 129, 0.2); color: #10b981; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">
                                Updated</div>
                        </div>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; color: #f1f5f9;">Product
                            Catalog</h3>
                        <p style="color:white; line-height: 1.6; margin-bottom: 1rem;">Add, edit, and organize your
                            product inventory</p>
                    </div>
                </a>
            </div>

            <div class="secondary-column">
                <a href="{{ route('admin.kategori.index') }}" class="secondary-card"
                    style="position: relative; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 20px; padding: 2rem; margin-bottom: 1.5rem; text-decoration: none; color: inherit; display: block; overflow: hidden; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);">
                    <div class="card-icon-small" style="font-size: 1.5rem; width: 40px; text-align: center;">🏷️</div>
                    <div class="card-info   " style="flex-grow: 1;">
                        <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.25rem; color: #f1f5f9;">
                            Categories</h4>
                        <p style="font-size: 0.875rem; color:white; margin: 0;">Organize product categories</p>
                    </div>
                    <div class="card-arrow"
                        style="color: #6366f1; font-size: 1.25rem; transition: transform 0.3s ease;">→</div>
                </a>


                
                <a href="{{ route('admin.transactions.index') }}" class="secondary-card urgent"
                    style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.2)">
                    <div class="card-icon-small" style="font-size: 1.5rem; width: 40px; text-align: center;">🛒</div>
                    <div class="card-info" style="flex-grow: 1;">
                        <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.25rem; color: #f1f5f9;">Order
                            Confirmation</h4>
                        <p style="font-size: 0.875rem; color:white; margin: 0;">Process customer orders</p>
                    </div>
                    <div class="card-arrow"
                        style="color: #6366f1; font-size: 1.25rem; transition: transform 0.3s ease;">→</div>
                </a>

                <a href="{{ route('admin.chat') }}" class="secondary-card chat-card"
                    style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.2)">
                    <div class="card-icon-small"
                        style="position:relative; font-size: 1.5rem; width: 40px; text-align: center;">
                        💬

                        <span id="chat-notification-badge"
                            style="position: absolute; top: 0; right: 0; background-color: #ef4444; color: white; border-radius: 50%; width: 15px; height: 15px; display: none; justify-content: center; align-items: center; font-size: 10px; font-weight: bold;">
                            N
                        </span>
                    </div>
                    <div class="card-info" style="flex-grow: 1;">
                        <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.25rem; color: #f1f5f9;">Admin
                            Chat</h4>
                        <p style="font-size: 0.875rem; color:white; margin: 0;">Team communication</p>
                    </div>
                    <div class="card-arrow"
                        style="color: #6366f1; font-size: 1.25rem; transition: transform 0.3s ease;">→</div>
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // Cek apakah admin sudah login.
        if (!{{ auth()->check() ? 'true' : 'false' }}) {
            return;
        }

        // Variabel-variabel untuk notifikasi
        const chatBadge = document.getElementById('chat-notification-badge');
        let previousCount = 0; // Melacak hitungan notifikasi dari Polling sebelumnya

        function showNotification() {
            // Menggunakan fungsi alert() sebagai pop-up paling simpel.
            // Anda bisa menggantinya dengan Toastr atau SweetAlert untuk tampilan yang lebih baik.
            alert('🔔 Ada pesan masuk baru!');
        }

        function fetchUnreadCount() {
            
            // Panggil route yang sudah didefinisikan di routes/web.php
            fetch('{{ route('admin.notifications.count') }}', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const count = data.count;
                const chatCard = document.querySelector('.chat-card');

                // === LOGIKA NOTIFIKASI POP-UP ===
                // Memicu notifikasi hanya jika hitungan saat ini > 0 DAN lebih besar dari hitungan sebelumnya.
                if (count > 0 && count > previousCount) {
                    showNotification(); 
                }
                
                // SELALU update previousCount
                previousCount = count;
                // ======================================

                // Logika Tampilan Badge
                if (count > 0) {
                    chatBadge.textContent = count > 9 ? '9+' : count;
                    chatBadge.style.display = 'flex';
                    
                    // Efek visual pada card chat
                    chatCard.style.boxShadow = '0 0 15px rgba(239, 68, 68, 0.6)';
                } else {
                    chatBadge.style.display = 'none';
                    chatCard.style.boxShadow = 'none';
                }
            })
            .catch(error => {
                console.error('Error fetching notification count:', error);
            });
        }

        // -------------------------------------------------------------------
        // INISIALISASI POLLING
        // -------------------------------------------------------------------
        
        // Jalankan fetch segera saat DOM selesai dimuat
        fetchUnreadCount(); 
        
        // Atur interval polling (5000 milidetik = 5 detik)
        setInterval(fetchUnreadCount, 5000); 

        // -------------------------------------------------------------------
        // LOGIKA MENGHILANGKAN BADGE SAAT ADMIN MENGKLIK LINK CHAT
        // -------------------------------------------------------------------
        const chatCardLink = document.querySelector('.chat-card');
        if (chatCardLink) {
            chatCardLink.addEventListener('click', function() {
                // Sembunyikan badge secara instan
                if (chatBadge.style.display !== 'none') {
                    chatBadge.style.display = 'none'; 
                    
                    // *TODO PENTING*: Di sini harus ada AJAX POST request ke endpoint Laravel 
                    // (misalnya route('admin.notifications.mark-read')) untuk menandai semua notifikasi 
                    // sebagai sudah dibaca di database (mengatur read_at = timestamp).
                    // Tanpa ini, badge akan muncul lagi setelah 5 detik karena polling menemukan notif belum dibaca.
                }
                // Set previousCount menjadi 0 agar saat polling berikutnya, notif pop-up tidak muncul
                previousCount = 0; 
            });
        }
        
        // -------------------------------------------------------------------
        // SCRIPT ANIMASI (ANDA)
        // -------------------------------------------------------------------

        // Animate stats on load
        const statValues = document.querySelectorAll('.stat-value');
        statValues.forEach((stat, index) => {
            setTimeout(() => {
                stat.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    stat.style.transform = 'scale(1)';
                }, 200);
            }, index * 100);
        });

        // Add hover effects to cards
        const cards = document.querySelectorAll('.primary-card, .secondary-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-4px) scale(1.02)';
            });

            card.addEventListener('mouseleave', function () {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Animate chart bars
        const chartBars = document.querySelectorAll('.chart-bar');
        chartBars.forEach((bar, index) => {
            setTimeout(() => {
                bar.style.transform = 'scaleY(1)';
            }, index * 100);
        });
    });
</script>
@endpush

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .admin-dashboard-dark {
        min-height: 90vh;
        color: #e2e8f0;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        padding: 2rem;
    }

    /* Stats Bar */
    .stats-bar {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-item {
        background: linear-gradient(135deg, #1e1e3f 0%, #2d2d5f 100%);
        border: 1px solid #3d3d7f;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .stat-item:hover {
        border-color: #6366f1;
        box-shadow: 0 0 20px rgba(99, 102, 241, 0.3);
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #6366f1;
        margin-bottom: 0.5rem;
        transition: transform 0.3s ease;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Welcome Section */
    .welcome-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding: 2rem;
        backdrop-filter: blur(12px);
        background: rgba(255, 255, 255, 0.1);
        border-radius: 16px;
    }

    .welcome-text h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }

    .welcome-text p {
        color: #94a3b8;
        font-size: 1.1rem;
    }

    .welcome-avatar {
        position: relative;
    }

    .avatar-circle {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
    }

    .status-dot {
        position: absolute;
        bottom: 4px;
        right: 4px;
        width: 16px;
        height: 16px;
        background: #10b981;
        border-radius: 50%;
        border: 3px solid #0f0f23;
    }

    /* Main Grid */
    .main-grid {
        display: grid;
        grid-template-columns: 2fr 1.5fr 1fr;
        gap: 2rem;
    }

    /* Primary Cards */
    .primary-card {
        position: relative;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        text-decoration: none;
        color: inherit;
        display: block;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .primary-card:hover {
        border-color: #6366f1;
        box-shadow: 0 20px 40px rgba(99, 102, 241, 0.2);
    }

    .card-background {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .users-card .card-background {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
    }

    .products-card .card-background {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(6, 182, 212, 0.1));
    }

    .primary-card:hover .card-background {
        opacity: 1;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .card-icon {
        font-size: 2rem;
    }

    .card-badge {
        background: rgba(99, 102, 241, 0.2);
        color: #6366f1;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .card-content h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #f1f5f9;
    }

    .card-content p {
        color: #94a3b8;
        line-height: 1.6;
        margin-bottom: 1rem;
    }

    .card-stats {
        color: #6366f1;
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* Secondary Cards */
    .secondary-card {
        background: linear-gradient(135deg, #1e1e3f 0%, #2d2d5f 100%);
        border: 1px solid #3d3d7f;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
    }

    .secondary-card:hover {
        border-color: #6366f1;
        transform: translateX(4px);
    }

    .secondary-card.urgent {
        border-color: #ef4444;
    }

    .secondary-card.urgent:hover {
        border-color: #dc2626;
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
    }

    .card-icon-small {
        font-size: 1.5rem;
        width: 40px;
        text-align: center;
    }

    .card-info {
        flex-grow: 1;
    }

    .card-info h4 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
        color: #f1f5f9;
    }

    .card-info p {
        font-size: 0.875rem;
        color: #94a3b8;
        margin: 0;
    }

    .urgent-badge {
        background: #ef4444;
        color: white;
        padding: 0.125rem 0.5rem;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .online-indicator {
        color: #10b981;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .card-arrow {
        color: #6366f1;
        font-size: 1.25rem;
        transition: transform 0.3s ease;
    }

    .secondary-card:hover .card-arrow {
        transform: translateX(4px);
    }

    /* Analytics Column */
    .analytics-card,
    .activity-card {
        background: linear-gradient(135deg, #1e1e3f 0%, #2d2d5f 100%);
        border: 1px solid #3d3d7f;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .analytics-card h4,
    .activity-card h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #f1f5f9;
    }

    .chart-container {
        display: flex;
        align-items: end;
        gap: 0.5rem;
        height: 80px;
        margin-bottom: 1rem;
    }

    .chart-bar {
        background: linear-gradient(to top, #6366f1, #8b5cf6);
        border-radius: 4px 4px 0 0;
        flex: 1;
        transform: scaleY(0);
        transform-origin: bottom;
        transition: transform 0.6s ease;
    }

    .analytics-card p {
        color: #94a3b8;
        font-size: 0.875rem;
    }

    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .activity-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .activity-dot {
        width: 8px;
        height: 8px;
        background: #6366f1;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .activity-text span {
        display: block;
        font-size: 0.875rem;
        color: #f1f5f9;
    }

    .activity-text time {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .main-grid {
            grid-template-columns: 1fr 1fr;
        }

        .analytics-column {
            grid-column: span 2;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
    }

    @media (max-width: 768px) {
        .admin-dashboard-dark {
            padding: 1rem;
        }

        .welcome-section {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }

        .welcome-text h1 {
            font-size: 2rem;
        }

        .main-grid {
            grid-template-columns: 1fr;
        }

        .analytics-column {
            grid-column: span 1;
            display: block;
        }

        .stats-bar {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Animations */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .primary-card,
    .secondary-card,
    .analytics-card,
    .activity-card {
        animation: slideInUp 0.6s ease-out;
    }

    .primary-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .primary-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .secondary-card:nth-child(1) {
        animation-delay: 0.3s;
    }

    .secondary-card:nth-child(2) {
        animation-delay: 0.4s;
    }

    .secondary-card:nth-child(3) {
        animation-delay: 0.5s;
    }

    .secondary-card:nth-child(4) {
        animation-delay: 0.6s;
    }
</style>
@endsection