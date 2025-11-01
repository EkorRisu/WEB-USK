<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class GeneralAboutController extends Controller
{
    /**
     * Mengambil HANYA 10 produk terbaru, dibagi menjadi 2 halaman (5 per halaman).
     */
    public function index()
    {
        // 1. Ambil 10 produk terbaru DARI DATABASE (menggunakan take(10))
        $query = Produk::with('kategori')
                      ->orderBy('created_at', 'desc') // Pastikan hanya yang terbaru
                      ->take(10); // Ambil hanya 10 hasil total
                      
        // 2. Sekarang, ambil hasilnya dan lakukan pagination manual untuk membaginya menjadi 2 halaman (5 per halaman)
        // Catatan: Jika Anda menggunakan ->paginate() di sini, ia akan mengabaikan take(10).
        // Jadi, kita akan mengambil semua 10 hasil, dan menggunakan LengthAwarePaginator untuk membagi 5/halaman.
        
        $totalItems = 10; 
        $perPage = 5;

        // Ambil data (total 10 item)
        $latestProductsCollection = $query->get();

        // 3. Buat LengthAwarePaginator secara manual (karena kita membatasi total hasil di query)
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage('produk_page');
        $currentItems = $latestProductsCollection->slice(($currentPage - 1) * $perPage, $perPage);
        
        $latestProducts = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $totalItems, // Total hasil yang DIBATASI adalah 10
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'pageName' => 'produk_page']
        );

        return view('user.about', compact('latestProducts'));
    }
}