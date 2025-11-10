<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// --- Imports Controller Utama ---
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\ProdukController as AdminProdukController; // Ganti nama agar tidak bentrok
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminTransactionController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\TransactionController;
use App\Http\Controllers\User\PDFController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\NotificationCountController;
use App\Http\Controllers\GeneralAboutController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProductController; // Digunakan untuk API Search (akan dipindah ke api.php)


// Default Landing Page
Route::get('/', function () {
    return view('welcome');
});

// ---------------- PERBAIKAN ROUTE LOCALIZATION ----------------
Route::get('/lang/{locale}', [LanguageController::class, 'switchLang'])->name('lang.switch');

// Custom Auth
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Redirect user berdasarkan role
Route::get('/redirect', function () {
    $role = Auth::user()->role ?? null;

    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($role === 'user') {
        return redirect()->route('user.dashboard');
    }

    return redirect('/login');
});

// ---------------- RUTE PUBLIK ----------------
Route::get('/about-us', [GeneralAboutController::class, 'index'])->name('about');


// ---------------- ADMIN ROUTES ----------------
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('produk', AdminProdukController::class); // Menggunakan AdminProdukController
    Route::resource('kategori', KategoriController::class);
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // Transaksi Admin
    Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{transaction}/edit', [AdminTransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('/transactions/{transaction}', [AdminTransactionController::class, 'update'])->name('transactions.update');

    Route::post('/transactions/{id}/konfirmasi', [AdminTransactionController::class, 'konfirmasi'])->name('transactions.konfirmasi');
    Route::post('/transactions/{id}/complete', [AdminTransactionController::class, 'complete'])->name('transactions.complete');

    // Chat admin
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');

    // ROUTE NOTIFIKASI BARU (POLING AJAX)
    Route::get('/notifications/count', [NotificationCountController::class, 'getUnreadCount'])
        ->name('notifications.count');
});

// ---------------- USER ROUTES ----------------
Route::middleware('auth')->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    Route::get('/about', [GeneralAboutController::class, 'index'])->name('about.user');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');

    // Checkout
    Route::get('/checkout', [TransactionController::class, 'checkoutForm'])->name('checkout.form');
    Route::post('/checkout', [TransactionController::class, 'processCheckout'])->name('checkout.process');

    // User Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
    Route::post('/transactions/selesai/{id}', [TransactionController::class, 'terimaPesanan'])->name('transactions.selesai');
    Route::get('/struk/{id}', [PDFController::class, 'cetakStruk'])->name('struk');

    // Chat user
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');

    // RUTE BARU: Untuk polling notifikasi chat di dashboard
    Route::get('/chat/unread-count', [ChatController::class, 'getUnreadCount'])->name('chat.unread.count');

    Route::get('/pay/va', [PaymentController::class, 'createVA']);

    // RUTE WISHLIST
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');

    // RUTE FAVORIT
    Route::post('/favorite/{produk}', [FavoriteController::class, 'toggle'])->name('favorite.toggle');

    // RUTE REVIEW & API
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    
    // RUTE API REVIEW (untuk modal detail/ulasan)
    Route::get('/produk/{produk}/reviews', [ReviewController::class, 'getProductReviews'])->name('produk.reviews.api');

    // Rute pencarian produk
    Route::get('/search-products', [ProductController::class, 'searchApi'])->name('search.products');
});

// Catatan: Route::get('/search_products', ...) yang ada di bawah blok user di kode Anda saya hapus
// karena sebaiknya diletakkan di routes/api.php.