<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil input filter
        $search = $request->input('search');
        $kategoriId = $request->input('kategori');
        $perPage = $request->input('perpage', 12); // Ganti default jadi 12
        $sortBy = $request->input('sort_by', 'created_at_desc');

        // 2. Mulai Query Produk
        $produkQuery = Produk::with('kategori')
            ->withCount('favoritedBy')
            ->with(['favoritedBy' => function($query) {
                $query->where('user_id', Auth::id());
            }])
            ->withSum('transactionItems', 'jumlah') // Pastikan nama kolom 'jumlah'
            // Tambahkan agregat untuk ulasan (menggunakan hasManyThrough 'reviewsThrough' di model Produk)
            ->withCount('reviewsThrough')
            ->withAvg('reviewsThrough', 'rating');

        // 3. Terapkan filter PENCARIAN
        if ($search) {
            $produkQuery->where('nama', 'like', '%' . $search . '%');
        }

        // 4. Terapkan filter KATEGORI
        if ($kategoriId) {
            $produkQuery->where('kategori_id', $kategoriId);
        }

        // 4.5 Terapkan filter Rating
        if ($request->input('rating_filter')) {
            $minRating = (float) $request->input('rating_filter');
            $produkQuery->having('reviews_through_avg_rating', '>=', $minRating);
        }

        // 5. Terapkan filter Favorit
        if ($request->input('filter_favorit') === 'true') {
            $produkQuery->whereHas('favoritedBy', function ($query) {
                $query->where('user_id', Auth::id());
            });
        }

        // 6. Terapkan logika sorting
        switch ($sortBy) {
            case 'terlaris':
                $produkQuery->orderBy('transaction_items_sum_jumlah', 'desc');
                break;
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
                $produkQuery->orderBy('created_at', 'desc');
                break;
        }

        // 7. Ambil data produk
        $produk = $produkQuery->paginate($perPage)->appends($request->except('page'));

        // Map aggregated fields to view-friendly names used in blade (average_rating, reviews_count)
        $produk->getCollection()->transform(function ($p) {
            // withCount('reviewsThrough') -> produces 'reviews_through_count'
            // withAvg('reviewsThrough','rating') -> produces 'reviews_through_avg_rating'
            $p->reviews_count = $p->reviews_through_count ?? 0;
            $avg = $p->reviews_through_avg_rating ?? 0;
            $p->average_rating = $avg ? round($avg, 1) : 0;
            return $p;
        });

        // 8. Ambil data pendukung
        $kategori = Kategori::all();
        $wishlistItems = Wishlist::where('user_id', Auth::id())
                                ->pluck('id', 'produk_id');

        // 9. Opsi untuk dropdown sorting
        $sortOptions = [
            'created_at_desc' => 'Produk Terbaru',
            'terlaris' => 'Produk Terlaris',
            'harga_asc' => 'Harga: Termurah',
            'harga_desc' => 'Harga: Termahal',
            'nama_asc' => 'Nama: A-Z',
            'nama_desc' => 'Nama: Z-A',
            'created_at_asc' => 'Produk Terlama',
        ];

        // 10. Kirim data ke view
        return view('user.dashboard', compact(
            'produk',
            'kategori',
            'wishlistItems',
            'sortOptions' 
        ));
    }
}