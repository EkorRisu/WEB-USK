<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil semua input filter dari URL
        $search = $request->input('search');
        $kategoriId = $request->input('kategori');
        $perPage = $request->input('perpage', 10); // Default 10 jika tidak ada
        $sortBy = $request->input('sort_by', 'created_at_desc'); // Default 'Produk Terbaru'

        // 2. Mulai query produk
        $produkQuery = Produk::query();

        // 3. Terapkan filter PENCARIAN
        if ($search) {
            $produkQuery->where('nama', 'like', '%' . $search . '%');
        }

        // 4. Terapkan filter KATEGORI
        if ($kategoriId) {
            $produkQuery->where('kategori_id', $kategoriId);
        }

        // 5. INI ADALAH PERBAIKAN UNTUK "URUTKAN"
        // Terapkan logika sorting berdasarkan input $sortBy
        switch ($sortBy) {
            case 'created_at_asc':
                $produkQuery->orderBy('created_at', 'asc');
                break;
            case 'harga_asc':
                $produkQuery->orderBy('harga', 'asc');
                break;
            case 'harga_desc':
                $produkQuery->orderBy('harga', 'desc');
                break;
            case 'nama_asc':
                $produkQuery->orderBy('nama', 'asc');
                break;
            case 'nama_desc':
                $produkQuery->orderBy('nama', 'desc');
                break;
            case 'created_at_desc':
            default:
                // Default sorting (Produk Terbaru)
                $produkQuery->orderBy('created_at', 'desc');
                break;
        }

        // 6. Ambil data produk dengan paginasi
        //    appends() akan memastikan filter (search, kategori, sort_by) tetap ada saat pindah halaman
        $produk = $produkQuery->paginate($perPage)->appends($request->except('page'));

        // 7. Ambil semua kategori (untuk dropdown filter)
        $kategori = Kategori::all();

        // 8. Kirim data ke view
        return view('user.dashboard', compact('produk', 'kategori'));
    }
}