<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Fungsi "live" untuk Like atau Unlike (Toggle).
     * Ini akan dipanggil oleh JavaScript (AJAX/Fetch).
     */
    public function toggle(Produk $produk)
    {
        $user = Auth::user();

        // Cek apakah user sudah like
        $isFavorited = $user->favorites()->where('produk_id', $produk->id)->exists();

        if ($isFavorited) {
            // Jika sudah, hapus (Unlike)
            $user->favorites()->detach($produk->id);
            $message = 'Buku dihapus dari favorit.';
            $favorited = false;
        } else {
            // Jika belum, tambahkan (Like)
            $user->favorites()->attach($produk->id);
            $message = 'Buku ditambahkan ke favorit!';
            $favorited = true;
        }

        // Ambil jumlah total like baru untuk buku ini
        $totalLikes = $produk->favoritedBy()->count();

        // Kembalikan response dalam format JSON
        return response()->json([
            'success' => true,
            'message' => $message,
            'is_favorited' => $favorited,
            'total_likes' => $totalLikes
        ]);
    }
}