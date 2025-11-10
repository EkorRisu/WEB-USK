<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController; // Controller yang Anda berikan
// Gunakan alias jika Anda punya App\Http\Controllers\Admin\ProdukController juga


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route API bawaan Laravel
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// ---------------- ROUTE API KHUSUS (LIVE SEARCH) ----------------
// Ini adalah route yang dipanggil oleh Fetch API di dashboard user.
// Middleware 'web' tidak berlaku di sini, jadi kita tidak perlu prefix 'user'.
// Route::get('/search_products', [ProductController::class, 'apiSearch']); // Jika ingin menggunakan nama 'apiSearch'
// Menggunakan nama yang lebih umum:
Route::get('/search_products', [ProductController::class, 'searchApi'])->name('api.search_products');
// CATATAN: Pastikan Anda telah mengganti nama method di ProductController dari apiSearch menjadi searchApi
// atau sesuaikan rute ini dengan nama method yang ada di ProductController Anda (yaitu 'apiSearch').
// ---------------------------------------------------------------