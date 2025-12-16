<?php
/**
 * Konfigurasi fitur-fitur aplikasi Azur Coffee
 * 
 * Ubah nilai true/false untuk mengaktifkan/menonaktifkan fitur
 * Setelah mengubah konfigurasi, jalankan: php artisan config:cache
 */

return [
    // === FITUR UMUM ===
    "translate" => false,           // Fitur multi-bahasa
    "dark_mode" => false,           // Mode gelap
    "about" => false,               // Halaman tentang kami
    "chat" => false,                // Fitur chat customer service

    // === FITUR ADMIN ===
    "card_review" => false,         // Kartu review di dashboard admin
    "user_menegement" => false,     // Manajemen pengguna
    "konfirmasi"=> true,            // Konfirmasi pesanan admin
    "admin_message" => false,       // Tambahkan pesan admin ke transaksi
    "order_preparation" => false,   // Siapkan pesanan (konfirmasi pengiriman)
    "topping_management" => false,  // Manajemen topping produk
    "raw_management" => false,      // Raw Stock/Inventory Management
    "recipe_bom" => false,          // Product Recipes (BOM - Bill of Materials)
    // === FITUR USER ===
    "wishlist" => false,            // Fitur wishlist produk (tampil di navbar + produk card)
    "favorit" => false,             // Fitur favorit produk (like button + filter favorit)
    "rating" => false,              // Fitur rating produk
    "review" => false,              // Fitur review produk
    "sort_by" => false,             // Fitur sorting produk (dropdown sort by) - DAPAT DIKONFIGURASI
    "filter_rating" => false,       // Fitur filter rating produk (filter berdasarkan bintang)
    "show_stock" => false,          // Tampilkan informasi stok produk di product card
    
    // === FITUR PEMBAYARAN ===
    "qris_payment" => false,         // Fitur pembayaran QRIS (scan QR code)
                                   // 
                                   // CARA MENGGUNAKAN:
                                   // true  = Tampilkan opsi pembayaran QRIS
                                   // false = Sembunyikan opsi QRIS (hanya cash payment)
                                   //
                                   // Setelah mengubah, jalankan: php artisan config:cache

    // === FITUR INVOICE & STRUK ===
    "pdf_invoice" => true,          // Download struk dalam format PDF
                                   // 
                                   // CARA MENGGUNAKAN:
                                   // true  = Struk digital + tombol download PDF
                                   // false = Hanya struk digital di web (hemat server, ramah lingkungan)
                                   //
                                   // Setelah mengubah, jalankan: php artisan config:cache
];